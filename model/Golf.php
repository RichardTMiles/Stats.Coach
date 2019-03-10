<?php

namespace Model;

use Psr\Log\InvalidArgumentException;
use Tables\carbon_locations;
use Tables\carbon_golf_courses as Course;
use Tables\carbon_golf_course_rounds as Rounds;
use CarbonPHP\Singleton;
use Model\Helpers\iSport;
use Model\Helpers\GlobalMap;
use CarbonPHP\Error\PublicAlert;
use Tables\carbon_users as Users;
use Tables\golf_rounds;

class Golf extends GlobalMap implements iSport
{
    use Singleton;


    public function PostScoreDistance($id, $color, $hole) {
        global $json;


        if (!$this->course($id)) {
            PublicAlert::danger('Failed to load the course!');
            return startApplication('/PostScore/Basic/');
        }

        $holes = $this->course[$id]['course_par'][ucfirst(strtolower($color))] ?? false;

        if (false === $holes) {
            PublicAlert::danger('Failed to load the course tee box!');
            return startApplication('/PostScore/Basic/');
        }

        $max = max($holes);

        $json['holes'] = [];

        foreach ($holes as $key => $distance) {
            $json['holes'][] = [
                'par' => $this->course[$id]['course_par']['par'][$key],
                'first' => $key === 1,
                'number' => $key,
                'distance' => $distance,
                'data_max' => $max
            ];
        }

        if (empty($_POST)) {
            return null;
        }

        sortDump($_POST);
        $post = [];

        if (false === Rounds::Get($post, null, [
                'where' => [
                    'round_input_complete' => false,
                    'course_id' => $id
                ]
            ])) {
            throw new PublicAlert('Failed to load rounds.');
        }

        if (empty($post)) {
            Rounds::Post([
                'round_id' => self::beginTransaction('carbon_golf_course', $id),
                'user_id' => $_SESSION['id'],
                'course_id' => $id,
                'round_json' => [
                    'color' => $color,
                    $hole => 1
                ]
            ]);
        } else {

            sortDump($post);

            Rounds::Put($json['course'], $post['round_id'], [
                'round_id' => $post['round_id'],
                'user_id' => $_SESSION['id'],
                'course_id' => $id,
            ]);

        }

        return null;
    }

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

        $user['stats'] = self::fetch('SELECT stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_putts, stats_gnr, stats_ffs FROM StatsCoach.carbon_user_golf_stats WHERE stats_id = ? LIMIT 1', $id);

        return $user;
    }

    /**
     * @param $id
     * @return bool
     * @throws PublicAlert
     */
    public function course($id): bool
    {
        $this->course[$id] = $this->course[$id] ?? [];

        if (!Course::Get($this->course[$id], $id, [])) {
            throw new PublicAlert('Failed to fetch course!');
        }

        if (!\is_array($this->course[$id])) {
            return false;
        }

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
        $sql = 'SELECT * FROM carbon_golf_tee_box WHERE course_id = ? AND distance_color = ? LIMIT 1';

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
        // forum variables are stored in globals?
        global $json, $gnr, $ffs, $putts, $newScore, $roundDate;

        $json['state'] = $state;    // was validated in controller

        // Get each color and make it readable in mustache bc im
        // dumb and made it this way back in the the day TODO - this enough validation???
        if (empty($boxColor)
            && !empty($course_id)
            && ($this->course[$course_id]['course_tee_boxes'] ?? false)
            && \is_array($this->course[$course_id]['course_tee_boxes'])) {      // TODO - to high =-- see tif this is okay
            foreach ($this->course[$course_id]['course_tee_boxes'] as $key => $value) {
                $json['colors'][] = [
                    'color' => $value['color'] ?? null,
                    'lower' => strtolower($value['color'])
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

            $sql = 'UPDATE StatsCoach.carbon_user_golf_stats SET stats_rounds = stats_rounds + 1, stats_strokes = stats_strokes + ?, stats_putts = stats_putts + ?, stats_ffs = stats_ffs + ?, stats_gnr = stats_gnr + ? WHERE stats_id = ?';

            if (!self::database()->prepare($sql)->execute([$score_tot, $putts_tot, $ffs_tot, $gnr_tot, $_SESSION['id']])) {
                throw new \RuntimeException('stats update failed');
            }

            PublicAlert::success('Score successfully added!');
            startApplication(true);
            return false;
        }

        return true;
    }

    public function PostScoreColor($course_id)
    {
        global $json;

        if (!empty($course_id) &&
            (!($this->course[$course_id] ?? false))
            && !$this->course($course_id)) {
            throw new PublicAlert('The course could not be found');
        }

        $json['course_id'] = $this->course[$course_id]['course_id'];

        $json['course_name'] = $this->course[$course_id]['course_name'];

        $json['course_colors'] = [];

        foreach ($this->course[$course_id]['course_tee_boxes'] as $box) {
            switch ($color = strtolower($box['color'])) {
                case 'white':
                    $json['course_colors'][] = ['color'=>'aqua'];
                    break;
                case 'gold':
                    $json['course_colors'][] = ['color'=>'yellow'];
                    break;
                default:
                    $json['course_colors'][] = ['color'=>$color];
            }
        }

    }


    public function PostScoreBasic($state)
    {
        global $json;

        $json['state'] = $state;
        $json['courses'] = self::fetch('SELECT course_name, HEX(course_id) AS course_id FROM StatsCoach.carbon_golf_courses LEFT JOIN StatsCoach.carbon_locations ON entity_id = course_id WHERE state = ?', $state);
        return true;
    }


    public
    function AddCourseBasic($phone, $pga_pro, $course_website, $name, $access, $style, $street, $city, $state, $tee_boxes, $handicap_number, $holes): bool
    {
        global $json;

        if ($id = $json['course']['course_id'] ?? false) {
            if (!(Course::Put($json['course'], $id, [
                    'course_name' => $name,
                    'created_by' => $_SESSION['id'],
                    'course_input_completed' => 0,
                    'tee_boxes' => $tee_boxes,
                    'handicap_count' => $handicap_number,
                    'pga_professional' => $pga_pro,
                    'website' => $course_website,
                    'course_holes' => $holes,
                    'course_phone' => $phone,
                    'course_type' => $style,
                    'course_access' => $access,
                    'course_handicap' => []
                ]) &&
                carbon_locations::Put($json['course']['location'], $id, [
                    'entity_id' => $json['course']['course_id'],
                    'city' => $city,
                    'street' => $street,
                    'state' => $state,
                ]))) {
                /** @noinspection ForgottenDebugOutputInspection */
                throw new PublicAlert(
                    'Failed to Update Data!');
            }

            PublicAlert::success('Course Save Started!');

            return startApplication("AddCourse/Color/$id/1");

        }
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
                    'course_tee_boxes' => [],
                    'course_par' => [],
                    'course_handicap' => []
                ])) &&
                carbon_locations::Post([
                    'entity_id' => $id,
                    'city' => $city,
                    'street' => $street,
                    'state' => $state,
                ]))
        ) {
            throw new PublicAlert('Sorry, we failed to add that course.');
        }

        return self::commit(function () use ($id) {
            PublicAlert::success('Course Save Started!');
            return startApplication("AddCourse/Color/$id/1");
        });
    }


    public
    function AddCourseColor($courseId, $box_number, $color, $slope, $difficulty)
    {
        global $json;

        $json['course']['course_tee_boxes'][$box_number] = [
            'box' => $box_number,
            'color' => $color,
            'slope' => [
                'm' => $slope[0],
                'w' => $slope[1]
            ],
            'difficulty' => [
                'm' => $difficulty[0],
                'w' => $difficulty[1]
            ]
        ];

        if (!Course::Put($json['course'], $courseId, [
            'course_tee_boxes' => $json['course']['course_tee_boxes'],
        ])) {
            throw new PublicAlert('Sorry, we failed to add that course.');
        }

        if ($box_number === $json['course']['tee_boxes']) {
            return startApplication('AddCourse/Distance/' . $json['course']['course_id'] . '/1/');
        }

        $json['current_hole'] = ++$box_number;

        $json['addColor'] = $json['course']['course_tee_boxes'][$box_number] ?? [];

        return true;
    }


    /** TODO - add input constraint validation (not just type checking)
     * @param $courseId
     * @param $holeNumber
     * @param $par
     * @param $handicap
     * @param $colors
     * @throws PublicAlert
     */
    public
    function AddCourseDistance($courseId, $holeNumber, $par, $handicap, $colors)
    {
        global $json;

        $json['course']['course_par']['par'][$holeNumber] = $par;

        foreach ($colors as $color => $distance) {
            $json['course']['course_par'][$color][$holeNumber] = $distance;
        }

        $json['course']['course_handicap'][$holeNumber] = \count($handicap) === 2 ?
            [
                'm' => $handicap[0],
                'w' => $handicap[1]
            ]
            : $handicap;

        if (!Course::Put($json['course'], $courseId, [
            'course_par' => $json['course']['course_par'],
            'course_handicap' => $json['course']['course_handicap'],
            'course_input_completed' => $course_input_completed = $holeNumber === (int)$json['course']['course_type']
        ])) {
            throw new PublicAlert('Sorry, we failed to add that course.');
        }

        PublicAlert::success('The course has been added and is public to the world!');

        if ($course_input_completed) {
            startApplication(true);
        } else {
            $holeNumber++;
            startApplication("AddCourse/Distance/$courseId/$holeNumber/");
        }
    }


}



