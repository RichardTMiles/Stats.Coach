<?php

namespace Model;

use Psr\Log\InvalidArgumentException;
use Tables\carbon_locations as Location;
use Tables\carbon_golf_courses as Course;
use Tables\carbon_golf_course_rounds as Rounds;
use CarbonPHP\Singleton;
use Model\Helpers\iSport;
use Model\Helpers\GlobalMap;
use CarbonPHP\Error\PublicAlert;
use Tables\carbon_team_members as members;
use Tables\carbon_teams as teams;
use Tables\carbon_user_golf_stats as stats;
use Tables\carbon_users as Users;
use Tables\golf_rounds;

class Golf extends GlobalMap implements iSport
{
    use Singleton;


    public function NewTournament() {
        return null;
    }

    public static function sessionStuff(&$my) {
        $my = array_merge($my, [
            'stats' => [],
            'coachedTeams' => [],
            'teamsJoined' => [],
            'teams' => [],
            'followers' => [],
            'messages' => []
        ]);

        stats::Get($my['stats'], $_SESSION['id'], []);

        members::Get($my['teamsJoined'], null, [
            'where' => [
                'user_id' => $_SESSION['id']
            ]
        ]);

        teams::Get($my['coachedTeams'], null, [
            'where' => [
                'team_coach' => $_SESSION['id'],
            ]
        ]);

        foreach ($my['coachedTeams'] as $key => &$value) {
            // get team members
            $value['members'] = self::fetch('select user_first_name, user_last_name, HEX(carbon_users.user_id) as user_id, user_profile_pic, user_cover_photo from carbon_users join carbon_team_members join carbon_teams 
                                  where carbon_teams.team_id = carbon_team_members.team_id 
                                    and carbon_users.user_id = carbon_team_members.user_id
                                    and carbon_teams.team_id = unhex(?)', $value['team_id']);


        }

        foreach ($my['teamsJoined'] as $key => &$value) {
            teams::Get($my['teams'], $value['team_id'], []);
            $value = array_merge($value, $my['teams']);
        }

        $my['teams'] = array_merge($my['teamsJoined'], $my['coachedTeams']);

    }

    public function PostScoreDistance($id, $color, $post = null)
    {
        global $json;

        if (!$this->course($id)) {
            PublicAlert::danger('Failed to load the course!');
            return startApplication('/PostScore/Basic/');
        }

        $json['courseInput'] = &$json['course'][$id];

        $holes = $json['course'][$id]['course_par'][ucfirst(strtolower($color))] ?? false;

        if (false === $holes) {
            PublicAlert::danger('Failed to load the course tee box!');
            return startApplication('/PostScore/Basic/');
        }

        $max = max($holes);

        $json['holes'] = [];
        $json['number_of_holes'] = count($holes);

        foreach ($holes as $key => $distance) {
            $json['holes'][] = [
                'par' => $json['course'][$id]['course_par']['par'][$key],
                'first' => $key === 1,
                'number' => $key,
                'distance' => $distance,
                'data_max' => $max,
                'last' => $key === $json['number_of_holes']
            ];
        }

        if (empty($post)) {
            return null;
        }

        if (false ===
            Rounds::Post([
                'user_id' => $_SESSION['id'],
                'course_id' => $id,
                'round_json' => $post,
                'round_public' => true,
                'round_out' => $out = array_sum(array_slice($post['shots'], 0, 8)),
                'round_in' => $in = array_sum(array_slice($post['shots'], 9, 17)),
                'round_total' => $total = $out + $in,
                'round_total_gnr' => $gnr = array_sum($post['gnr']),
                'round_total_ffs' => $ffs = array_sum($post['ffs']),
                'round_total_putts' => $putts = array_sum($post['putts']),
                'round_date' => $post['date'],
                'round_input_complete' => true,
                'round_tee_box_color' => $color
            ])) {
            throw new PublicAlert('Failed to post round!');
        }

        if (!self::execute('UPDATE StatsCoach.carbon_user_golf_stats SET 
                                             stats_rounds = stats_rounds + 1, 
                                             stats_strokes = stats_strokes + ?, 
                                             stats_putts = stats_putts + ?, 
                                             stats_ffs = stats_ffs + ?, 
                                             stats_gnr = stats_gnr + ? WHERE stats_id = UNHEX(?)',
            $total, $putts, $ffs, $gnr, $_SESSION['id'])) {
            throw new PublicAlert('Could not update user stats.');
        }

        if (!self::commit(function () {
            PublicAlert::success('We posted your score successfully!');
            return true;
        })) {
            PublicAlert::danger('Sorry, we failed to post your score.');
            return true;
        }

        return startApplication('/');
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
    public function rounds($user_uri_id, $limit = 20)
    {
        global $json;

        $json['roundUser'] = [];

        Users::Get($json['roundUser'], $user_uri_id, [
            'select' => [
                'user_first_name',
                'user_last_name',
                'user_profile_pic',
                'user_id',
            ]
        ]);

        $json['my']['rounds'] = [];


        Rounds::Get($json['my']['rounds'], null, [
            'where' => [
                'user_id' => $user_uri_id
            ],
            'pagination' => [
                'limit' => $limit
            ]
        ]);

        foreach ($json['my']['rounds'] as $k => &$v) {
            if (!$this->course($v['course_id'])) {
                throw new PublicAlert('Course lookup failed.');
            }

            $json['my']['rounds'][$k]['course'] = $json['course'][$v['course_id']];

            $json['my']['rounds'][$k]['course_name'] = $json['course'][$v['course_id']]['course_name'];

            $json['my']['rounds'][$k]['course_distance'] =
                array_sum(
                    $json['course']
                    [$v['course_id']]
                    ['course_par']
                    [ucfirst($v['round_tee_box_color'])]);

            $json['my']['rounds'][$k]['course_par'] =
                array_sum($json['course'][$v['course_id']]
                ['course_par']['par']);


        }

        // date( 'm/d/Y',

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
        global $json;


        $json['course'][$id] = $json['course'][$id] ?? [];

        if (!Course::Get($json['course'][$id], $id, [])) {
            throw new PublicAlert('Failed to fetch course!');
        }

        $location = [];

        if (!Location::Get($location, $id, [])) {
            throw new PublicAlert('Failed to fetch course location!');
        }

        // Should this use case mean rest should change?
        $json['course'][$id] = array_merge_recursive($json['course'][$id], $location);

        $this->course[$id] = &$json['course'][$id];

        if (!\is_array($json['course'][$id])) {
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
     *  TBD
     *
     *
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
            (!($json['course'][$course_id] ?? false))
            && !$this->course($course_id)) {
            throw new PublicAlert('The course could not be found');
        }

        $json['course_id'] = $json['course'][$course_id]['course_id'];

        $json['course_name'] = $json['course'][$course_id]['course_name'];

        $json['course_colors'] = [];

        foreach ($json['course'][$course_id]['course_tee_boxes'] as $box) {
            switch ($color = strtolower($box['color'])) {
                case 'white':
                    $json['course_colors'][] = [
                        'color' => $color,
                        'color_style_name' => 'aqua'
                    ];
                    break;
                case 'gold':
                    $json['course_colors'][] = [
                        'color' => $color,
                        'color_style_name' => 'yellow'

                    ];
                    break;
                default:
                    $json['course_colors'][] = [
                        'color' => $color,
                        'color_style_name' => $color
                    ];
            }
        }

    }


    public function PostScoreBasic($state)
    {
        global $json;

        //return startApplication('/');

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
                Location::Put($json['course']['location'], $id, [
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
                Location::Post([
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


        if ($course_input_completed) {
            PublicAlert::success('The course has been added and is public to the world!');
            return startApplication('/');
        } else {
            PublicAlert::info("Hole $holeNumber was successfully saved!");
            $holeNumber++;
            return startApplication("AddCourse/Distance/$courseId/$holeNumber/");
        }
    }
}



