<?php

namespace Model;

use Psr\Log\InvalidArgumentException;
use Tables\Course;
use Tables\Rounds;
use Carbon\Singleton;
use Model\Helpers\iSport;
use Model\Helpers\GlobalMap;
use Carbon\Error\PublicAlert;
use Tables\Users;

class Golf extends GlobalMap implements iSport
{
    use Singleton;

    public function golf()  // This is the home page for the user
    {
        return true;
    }

    public function rounds($user_uri)
    {
        global $user_id;
        if ($user_uri !== $_SESSION['id'])
            $user_id = Users::user_id_from_uri( $user_uri );

        Rounds::all($this->user[$user_id], $user_id);
    }

    public function stats(&$user, $id)
    {
        if (!is_object($user)) throw new InvalidArgumentException('Bad User Passed To Golf Stats');
        $user->rounds = Rounds::get( $user->rounds, $id );
        $user->stats = $this->fetch_object( 'SELECT * FROM StatsCoach.golf_stats WHERE stats_id = ? LIMIT 1', $id );
        return $user;
    }

    public function course($id)
    {
        $this->course[$id] = $this->fetch_object( 'SELECT * FROM StatsCoach.golf_course JOIN StatsCoach.entity_location ON entity_id = course_id WHERE course_id = ? LIMIT 1', $id );
        if (!is_object( $course = $this->course[$id] )) throw new \Exception( 'invalid course id' );
        $course->course_par = unserialize( $course->course_par );
        $course->course_handicap = unserialize( $course->course_handicap );
        return $course;
    }

    public function teeBox($id, $color)
    {
        if (!is_object( $this->course[$id] )) throw new \Exception( 'invalid distance lookup' );
        $sql = "SELECT * FROM StatsCoach.golf_tee_box WHERE course_id = ? AND distance_color = ? LIMIT 1";
        $teeBox = $this->course[$id]->teeBox = $this->fetch_object( $sql, $id, $color );
        $teeBox->distance = unserialize( $teeBox->distance );
        $this->course[$id]->teeBox->distance_color = $color;
        return $teeBox;
    }

    public function postScore($state, $course_id, $boxColor)
    {
        global $course_colors, $courses, $gnr, $ffs, $putts, $newScore, $roundDate;

        // Insert into database
        if (!empty($newScore) && is_array( $newScore )) {
            $score_out = $score_in = $score_tot = $gnr_tot = $ffs_tot = $putts_tot = 0;
            for ($i = 0; $i < 8; $i++) $score_out += $newScore[$i];
            for ($i = 9; $i < 18; $i++) $score_in += $newScore[$i];
            $score_tot = $score_in + $score_out;

            for ($i = 0; $i < 18; $i++) {
                $gnr_tot += $gnr[$i];
                $ffs_tot += $ffs[$i];
                $putts_tot += $putts[$i];
            }

            $course = $this->course[$course_id]->course_id ?? $this->course( $course_id );

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

            $sql = "UPDATE StatsCoach.golf_stats SET stats_rounds = stats_rounds + 1, stats_strokes = stats_strokes + ?, stats_putts = stats_putts + ?, stats_ffs = stats_ffs + ?, stats_gnr = stats_gnr + ? WHERE stats_id = ?";
            if (!$this->db->prepare( $sql )->execute( [$score_tot, $putts_tot, $ffs_tot, $gnr_tot, $_SESSION['id']] ))
                throw new \Exception( 'stats update failed' );

            PublicAlert::success( "Score successfully added!" );
            startApplication( TRUE );
        }

        // Get Course so we can display the tee box colors
        if (!empty($course_id) && (is_array( $this->course ) && !array_key_exists( $course_id, $this->course ) || !is_object( $course = $this->course[$course_id] ) || !isset($course->course_id) || $course->course_id != $course_id))
            $course = $this->course( $course_id );

        // A tee box color is set, get the distances.
        // I dont like that the tee box color is stored twice
        if (!empty($boxColor)) {
            if (!isset($course->teeBox) || !is_object( $course->teeBox )) {
                $this->teeBox( $course_id, $boxColor );
            }
            return null;
        }

        if (!empty($course_id) && is_object( $course ))
            return $course_colors = [$course->box_color_1, $course->box_color_2, $course->box_color_3, $course->box_color_4, $course->box_color_5];

        if (!empty($state)) {
            $sql = "SELECT course_name, course_id FROM StatsCoach.golf_course LEFT JOIN StatsCoach.entity_location ON entity_id = course_id WHERE state = ?";
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( [$state] );
            $courses = $stmt->fetchAll();                 // setting to global
            if (empty($courses)) $courses = true;
        }
        return true;
    }

    public function addCourse($course, $handicap)
    {
        global $holes, $par, $tee_boxes, $teeBox, $handicap_number, $phone, $course_website, $pga_pro;

        $par_out = 0;
        $par_in = 0;
        for ($i = 0; $i < 9; $i++) $par_out += $par[$i];
        for ($i = 9; $i < 18; $i++) $par_in += $par[$i];
        $par_tot = $par_out + $par_in;

        Course::add( null, null, $argv = [
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

        ]);
       
        PublicAlert::success( 'The course has been added!' );
        startApplication( 'Home/' );

    }

}



