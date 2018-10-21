<?php

namespace Model;

use Psr\Log\InvalidArgumentException;
use Table\carbon_locations;
use Table\golf_course as Course;
use Table\golf_rounds as Rounds;
use CarbonPHP\Singleton;
use Model\Helpers\iSport;
use Model\Helpers\GlobalMap;
use CarbonPHP\Error\PublicAlert;
use Table\carbon_users as Users;

class Golf extends GlobalMap implements iSport
{
    use Singleton;

    /**
     * @return bool
     */
    public function golf(): bool  // This is the home page for the user
    {
        return true;
    }

    /**
     * @param $user_uri
     */
    public function rounds($user_uri)
    {
        global $user, $user_id;

        if ($user_uri !== $_SESSION['id']) {
            $user_id = Users::user_id_from_uri($user_uri);
        }

        Rounds::Get($user[$user_id], $user_id, []);
    }

    /**
     * @param $user
     * @param $id
     * @return array
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function stats(&$user, $id): array
    {
        if (!\is_array($user)) {
            throw new InvalidArgumentException('Bad User Passed To Golf Stats');
        }

        Rounds::Get($user['rounds'], $id, []);

        if (!array_key_exists(0, $user['rounds'])) {
            $user['rounds'] = [$user['rounds']];
        }

        $user['stats'] = self::fetch('SELECT stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_putts, stats_gnr, stats_ffs FROM StatsCoach.golf_stats WHERE stats_id = ? LIMIT 1', $id);

        return $user;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function course($id): bool
    {
        $this->course[$id] = self::fetch('SELECT * FROM golf_course JOIN carbon_locations ON entity_id = course_id WHERE course_id = ? LIMIT 1', $id);

        if (!\is_array($this->course[$id])) {
            return false;
        }

        $this->course[$id]['course_par'] = unserialize($this->course[$id]['course_par'], []);
        $this->course[$id]['course_handicap'] = unserialize($this->course[$id]['course_handicap'], []);

        return true;
    }

    /**
     * @param $id
     * @param $color
     * @return mixed
     * @throws \RuntimeException
     */
    public function teeBox($id, $color)
    {
        if (!\is_array($this->course[$id])) {
            throw new \RuntimeException('invalid distance lookup');
        }
        $sql = 'SELECT * FROM golf_tee_box WHERE course_id = ? AND distance_color = ? LIMIT 1';

        $this->course[$id]['teeBox'] = self::fetch($sql, $id, $color);

        $this->course[$id]['teeBox']['distance'] = unserialize($this->course[$id]['teeBox']['distance'], []);

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
        // forum variables are stored in globals
        global $gnr, $ffs, $putts, $newScore, $roundDate;

        //json
        global $json;   // This will fill our template

        $json['state'] = $state;    // was validated in controller

        // low key got to high one day camping and moved the state selection to
        // an even tin the bootstrap.. it loads the mustache in a seperate div using startApplication in js...
        // I felt like it should be a thing

        // Get Course so we can display the tee box colors
        if (!empty($course_id) &&
            (!($this->course[$course_id] ?? false))
            && !$this->course($course_id)) {
            throw new PublicAlert('The course could not be found');
        }

        if (empty($boxColor)) {
            $json['step2'] = true;
            $json['course'] = $this->course[$course_id];

            // TODO - make this color palate happen
            /*
            foreach ($course_colors as $key => $value) {
                if (empty($value)) {
                    break;
                }
                switch ($value = strtolower($value)) {
                    case 'white':
                        $color = 'aqua';
                        break;
                    case 'gold':
                        $color = 'yellow';
                        break;
                    default:
                        $color = $value;
                }
            }
            */
        }

        // Get each color and make it readable in mustache bc im
        // dumb and made it this way back in the the day TODO - this engouth validation???
        if (empty($boxColor) && !empty($course_id) && \is_array($this->course)) {      // TODO - to high =-- see tif this is okay
            for ($i = 1; $i < 6; $i++) {
                if (empty($c = $this->course[$course_id]["box_color_$i"])) {
                    break;
                }

                $json['colors'][] = [
                    'color' => $c,
                    'lower' => strtolower($c)
                ];
            }
            return true;
        }


        // A tee box color is set, get the distances.
        // I don't like that the tee box color is stored twice
        if (!empty($boxColor)) {
            if (!isset($this->course[$course_id]['teeBox']) || !\is_array($this->course[$course_id]['teeBox'])) {
                $this->teeBox($course_id, $boxColor);
            }

            $json['step3'] = true;
            $json['course'] = &$this->course[$course_id];
            $json['date'] = date('m/d/Y');

            for ($i = 0; $i < $json['course']['course_holes'];) {
                $json['holes'][] = [
                    'par' => $this->course[$course_id]['course_par'][$i],
                    'distance' => $this->course[$course_id]['teeBox']['distance'][$i],
                    'distance_color' => $this->course[$course_id]['teeBox']['distance_color'],
                    'number' => ++$i,
                    'first' => $i === 1,
                    'last' => $i === (int)$json['course']['course_holes']
                ];
            }

            return true;
        }

        // ahh shit
        // Insert into database
        if (!empty($newScore) && \is_array($newScore)) {
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

            if (!($this->course[$course_id]['course_id'] ?? false)) {
                $this->course($course_id);
            }
            alert('Post new round');

            ################# Add Round ################
            Rounds::add($this->user[$_SESSION['id']], $course_id, [
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
            ]);

            $sql = 'UPDATE StatsCoach.golf_stats SET stats_rounds = stats_rounds + 1, stats_strokes = stats_strokes + ?, stats_putts = stats_putts + ?, stats_ffs = stats_ffs + ?, stats_gnr = stats_gnr + ? WHERE stats_id = ?';

            if (!$this->db->prepare($sql)->execute([$score_tot, $putts_tot, $ffs_tot, $gnr_tot, $_SESSION['id']])) {
                throw new \RuntimeException('stats update failed');
            }

            PublicAlert::success('Score successfully added!');
            startApplication(true);
            return false;
        }

        return true;
    }


    public function coursesByState($state)
    {
        global $json;

        $sql = 'SELECT course_name, course_id FROM StatsCoach.golf_course LEFT JOIN StatsCoach.carbon_locations ON entity_id = course_id WHERE state = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$state]);

        $json['courses'] = $stmt->fetchAll();                 // setting to global

        return true;
    }


    public function AddCourseBasic($phone, $pga_pro, $course_website, $name, $access, $style, $street, $city, $state, $tee_boxes, $handicap_number, $holes): bool
    {
        if (false === (($id = Course::Post([
                    'course_name' => $name,
                    'created_by' => $_SESSION['id'],
                    'course_input_completed' => 0,
                    'tee_boxes' => $tee_boxes,
                    'handicap_count' => $handicap_number,
                    'pga' => $pga_pro,
                    'site' => $course_website,
                    'course_holes' => $holes,
                    'course_phone' => $phone,
                    'course_type' => $style,
                    'course_access' => $access,
                    'course_par' => '',
                    'course_handicap' => ''])) &&
                carbon_locations::Post([
                    'entity_id' => $id,
                    'city' => $city,
                    'street' => $street,
                    'state' => $state,
                ]))
        ) {
            throw new PublicAlert('Sorry, we failed to add that course.');
        }

        self::commit(function() use ($id) {
            PublicAlert::success('YAYAY WE DID IT!');
            startApplication("AddCourse/Color/$id/1");
        });
        return false;
    }


    public function AddCourseColor($courseId, $box_number, $color, $slope, $difficulty)
    {

        global $json;

        if (!\is_array($json['course'])) {
            Course::Get($json['course'], $courseId, []);
        }

        if (!Course::Put($json['course'], null, [
            'course_id' => $courseId,
            'number' => $box_number,
            'color' => $color,
            'slope' => json_encode($slope),
            'difficulty' => json_encode($difficulty)
        ])) {
            throw new PublicAlert('Sorry, we failed to add that course.');
        }

    }


    public function AddCourseDistance()
    {

        PublicAlert::success('The course has been added and is public to the world!');

    }


}



