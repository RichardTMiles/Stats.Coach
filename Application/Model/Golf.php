<?php

namespace Model;

use Psr\Log\InvalidArgumentException;
use Table\Course;
use Table\Rounds;
use Carbon\Singleton;
use Model\Helpers\iSport;
use Model\Helpers\GlobalMap;
use Carbon\Error\PublicAlert;
use Table\Users;

class Golf extends GlobalMap implements iSport
{
    use Singleton;

    /**
     * @return bool
     */
    public function golf() : bool  // This is the home page for the user
    {
        return true;
    }

    /**
     * @param $user_uri
     */
    public function rounds($user_uri)
    {
        global $user_id;

        if ($user_uri !== $_SESSION['id']) {
            $user_id = Users::user_id_from_uri( $user_uri );
        }

        Rounds::all($this->user[$user_id], $user_id);
    }

    /**
     * @param $user
     * @param $id
     * @return array
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function stats(&$user, $id) : array
    {
        if (!\is_array($user)) {
            throw new InvalidArgumentException('Bad User Passed To Golf Stats');
        }
        $user['rounds'] = Rounds::get( $user['rounds'], $id );

        if (!array_key_exists(0, $user['rounds'])) {
            $user['rounds'] = [$user['rounds']];
        }

        $user['stats'] = self::fetch( 'SELECT stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_putts, stats_gnr, stats_ffs FROM StatsCoach.golf_stats WHERE stats_id = ? LIMIT 1', $id );

        return $user;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function course($id)
    {
        $this->course[$id] = self::fetch( 'SELECT * FROM golf_course JOIN carbon_location ON entity_id = course_id WHERE course_id = ? LIMIT 1', $id );
        if (!\is_array( $course = &$this->course[$id] )) {
            throw new \RuntimeException( 'invalid course id' );
        }
        $course['course_par'] = unserialize( $course['course_par'], false );
        $course['course_handicap'] = unserialize( $course['course_handicap'], false );
        return $course;
    }

    /**
     * @param $id
     * @param $color
     * @return mixed
     * @throws \RuntimeException
     */
    public function teeBox($id, $color)
    {
        if (!\is_array( $this->course[$id] )) {
            throw new \RuntimeException( 'invalid distance lookup' );
        }
        $sql = 'SELECT * FROM golf_tee_box WHERE course_id = ? AND distance_color = ? LIMIT 1';
        $this->course[$id]['teeBox'] = self::fetch( $sql, $id, $color );
        $this->course[$id]['teeBox']['distance'] = unserialize( $this->course[$id]['teeBox']['distance'], false );
        $this->course[$id]['teeBox']['distance_color'] = $color;
        return $this->course[$id]['teeBox'];
    }

    /**
     * @param $state
     * @param $course_id
     * @param $boxColor
     * @return array|bool|null
     * @throws \Exception
     */
    public function postScore($state, $course_id, $boxColor)
    {
        global $course_colors, $course, $courses, $gnr, $ffs, $putts, $newScore, $roundDate;

        // Insert into database
        if (!empty($newScore) && \is_array( $newScore )) {
            alert('news');

            $score_out = $score_in = $score_tot = $gnr_tot = $ffs_tot = $putts_tot = 0;

            for ($i = 0; $i < 8; $i++) {
                $score_out += $newScore[$i];
            }
            for ($i = 9; $i < 18; $i++) {
                $score_in += $newScore[$i];
            }
            $score_tot = $score_in + $score_out;

            for ($i = 0; $i < 18; $i++) {
                $gnr_tot += $gnr[$i];
                $ffs_tot += $ffs[$i];
                $putts_tot += $putts[$i];
            }

            if (!$this->course[$course_id]['course_id'] ?? false){
                $this->course( $course_id );
            }
            alert('Post new round');

            ################# Add Round ################
            Rounds::add( $this->user[$_SESSION['id']], $course_id, [
                'roundDate' => $roundDate,
                'newScore' => $newScore,
                'gnr' => $gnr,
                'ffs' => $ffs,
                'putts' => $putts,
                'score_out' => $score_out,
                'score_in' => $score_in,
                'score_tot' => $score_tot,
                'gnr_tot' => $gnr_tot,
                'ffs_tot' => $ffs_tot,
                'putts_tot' => $putts_tot
            ] );

            $sql = 'UPDATE StatsCoach.golf_stats SET stats_rounds = stats_rounds + 1, stats_strokes = stats_strokes + ?, stats_putts = stats_putts + ?, stats_ffs = stats_ffs + ?, stats_gnr = stats_gnr + ? WHERE stats_id = ?';

            if (!$this->db->prepare( $sql )->execute( [$score_tot, $putts_tot, $ffs_tot, $gnr_tot, $_SESSION['id']] )) {
                throw new \RuntimeException( 'stats update failed' );
            }

            PublicAlert::success( 'Score successfully added!' );
            startApplication( true );
            return false;
        }


        // Get Course so we can display the tee box colors
        if (!empty($course_id) && (!\is_array( $this->course ) || !array_key_exists( $course_id, $this->course ) || !\is_array( $course = &$this->course[$course_id] ))) {
            $course = $this->course($course_id);
        }

        // A tee box color is set, get the distances.
        // I don't like that the tee box color is stored twice
        if (!empty($boxColor)) {
            if (!isset($course['teeBox']) || !\is_array( $this->course['teeBox'] )) {
                $this->teeBox( $course_id, $boxColor );
            }
            return true;
        }


        if (!empty($course_id) && \is_array( $course )) {
            $course_colors = [
                $course['box_color_1'], $course['box_color_2'], $course['box_color_3'],
                $course['box_color_4'], $course['box_color_5']
            ];
            return true;
        }

        if (!empty($state)) {
            $sql = 'SELECT course_name, course_id FROM StatsCoach.golf_course LEFT JOIN StatsCoach.carbon_locations ON entity_id = course_id WHERE state = ?';
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( [$state] );
            $courses = $stmt->fetchAll();                 // setting to global
            if (empty($courses)) {
                $courses = true;
            }
        }
        return true;
    }

    /**
     * @param $course
     * @param $handicap
     * @return bool
     * @throws \Carbon\Error\PublicAlert
     */
    public function addCourse($course, $handicap) : bool
    {
        global $holes, $par, $tee_boxes, $teeBox, $handicap_number, $phone, $course_website, $pga_pro;

        $par_out = 0;
        $par_in = 0;
        for ($i = 0; $i < 9; $i++) {
            $par_out += $par[$i];
        }
        for ($i = 9; $i < 18; $i++) {
            $par_in += $par[$i];
        }
        $par_tot = $par_out + $par_in;

        $null = null;
        if (!Course::add( $null, $null, $argv = [
            'course' => $course,
            'handicap' => $handicap,
            'holes' => $holes,
            'par' => $par,
            'tee_boxes' => $tee_boxes,
            'teeBox' => $teeBox,
            'phone' => $phone,
            'course_website' => $course_website,
            'pga_pro' => $pga_pro,
            'par_out' => $par_out,
            'par_in' => $par_in,
            'par_tot' => $par_tot,
            'handicap_number' => $handicap_number

        ])) {
            throw new PublicAlert('Sorry, we failed to add that course.');
        };
       
        PublicAlert::success( 'The course has been added!' );

        return true;
    }

}



