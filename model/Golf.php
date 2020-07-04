<?php

namespace Model;

use Tables\carbon_golf_tournaments;
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

    /**
     * @param $id
     * @return bool
     * @throws PublicAlert
     */
    public function tournamentSettings($id): bool
    {
        $this->json['tournament'] = [];
        if (!carbon_golf_tournaments::Get($this->json['tournament'], $id, [])) {
            throw new PublicAlert('Failed to load tournament data');
        }

        $host = $this->json['tournament'][carbon_golf_tournaments::TOURNAMENT_CREATED_BY_USER_ID];

        if ($host !== $_SESSION['id']) {
            throw new PublicAlert('You do not have access to edit this tournament.');
        }
        return true;
    }

    /**
     * @param $id
     * @return bool
     * @throws PublicAlert
     */
    public function tournament($id): bool
    {
        $this->json['tournament'] = [];
        if (!carbon_golf_tournaments::Get($this->json['tournament'], $id, [])) {
            throw new PublicAlert('Failed to load tournament data');
        }

        $host = $this->json['tournament'][carbon_golf_tournaments::TOURNAMENT_CREATED_BY_USER_ID];

        $this->json['im_the_host'] = $host === $_SESSION['id'];

        $this->json['tournament_host_info'] = $this->json['im_the_host'] ?
            $this->user[$_SESSION['id']] :
            getUser($this->json['tournament'][carbon_golf_tournaments::TOURNAMENT_CREATED_BY_USER_ID], 'Basic');

        return true;
    }

    public function coursesByState($state)
    {
        // this is actually a resolving route with an hbs. do not modify
        return $this->json['courses'] = self::fetch('SELECT course_name, HEX(course_id) AS course_id FROM StatsCoach.carbon_golf_courses LEFT JOIN StatsCoach.carbon_locations ON entity_id = course_id WHERE state = ? AND course_input_completed = 1', $state);
    }

    public function NewTournament($tournamentName, $hostName, $hostID, $courseID, $playStyle): ?bool
    {
        if (!carbon_golf_tournaments::Post([
            carbon_golf_tournaments::TOURNAMENT_NAME => $tournamentName,
            carbon_golf_tournaments::TOURNAMENT_HOST_NAME => $hostName,
            carbon_golf_tournaments::TOURNAMENT_HOST_ID => $hostID,
            carbon_golf_tournaments::TOURNAMENT_COURSE_ID => $courseID,
            carbon_golf_tournaments::TOURNAMENT_STYLE => $playStyle,
            carbon_golf_tournaments::TOURNAMENT_CREATED_BY_USER_ID => $_SESSION['id']
        ])) {
            PublicAlert::danger('Failed to post new tournament!');
            return true;
        }

        if (self::commit()) {
            PublicAlert::success('Tournament created!');
        } else {
            PublicAlert::danger('An unexpected error occurred!');
            return true;
        }
        return startApplication('home');    // can return true or null
    }

    public static function sessionStuff(&$my)
    {
        $my = array_merge($my, [
            'stats' => [],
            'coachedTeams' => [],
            'teamsJoined' => [],
            'teams' => [],
            'followers' => [],
            'messages' => [],
            'tournaments' => []
        ]);

        if (!carbon_golf_tournaments::Get($my['tournaments'], null, [
            'select' => [
                carbon_golf_tournaments::TOURNAMENT_ID,
                carbon_golf_tournaments::TOURNAMENT_NAME
            ],
            'where' => [
                carbon_golf_tournaments::TOURNAMENT_CREATED_BY_USER_ID => $_SESSION['id']
            ]
        ])) {
            PublicAlert::danger('Failed to lookup golf tournaments');
        }

        stats::Get($my['stats'], $_SESSION['id'], []);

        members::Get($my['teamsJoined'], null, [
            'where' => [
                members::USER_ID => $_SESSION['id']
            ]
        ]);

        teams::Get($my['coachedTeams'], null, [
            'where' => [
                teams::TEAM_COACH => $_SESSION['id'],
            ]
        ]);

        foreach ($my['coachedTeams'] as $key => &$value) {
            // get team members
            $value['members'] = self::fetch('select user_first_name, user_last_name, HEX(carbon_users.user_id) as user_id, user_profile_pic, user_cover_photo from carbon_users join carbon_team_members join carbon_teams 
                                  where carbon_teams.team_id = carbon_team_members.team_id 
                                    and carbon_users.user_id = carbon_team_members.user_id
                                    and carbon_teams.team_id = unhex(?)', $value['team_id']);
        }

        unset($value);

        foreach ($my['teamsJoined'] as $key => &$value) {
            if (!teams::Get($my['teams'], $value['team_id'], [])) {
                PublicAlert::warning('Failed to retrieve all team information');
            }
            /** @noinspection SlowArrayOperationsInLoopInspection - TODO - see if this can be removed */
            $value = array_merge($value, $my['teams']);
        }

        unset($value);

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

        if (false === Rounds::Post([
                Rounds::USER_ID => $_SESSION['id'],
                Rounds::COURSE_ID => $id,
                Rounds::ROUND_JSON => $post,
                Rounds::ROUND_PUBLIC => true,
                Rounds::ROUND_OUT => $out = array_sum(array_slice($post['shots'], 0, 8)),
                Rounds::ROUND_IN => $in = array_sum(array_slice($post['shots'], 9, 17)),
                Rounds::ROUND_TOTAL => $total = $out + $in,
                Rounds::ROUND_TOTAL_GNR => $gnr = array_sum($post['gnr']),
                Rounds::ROUND_TOTAL_FFS => $ffs = array_sum($post['ffs']),
                Rounds::ROUND_TOTAL_PUTTS => $putts = array_sum($post['putts']),
                Rounds::ROUND_DATE => $post['date'],
                Rounds::ROUND_INPUT_COMPLETE => true,
                Rounds::ROUND_TEE_BOX_COLOR => $color
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

        if (self::commit()) {
            PublicAlert::success('We posted your score successfully!');
        } else {
            PublicAlert::danger('Sorry, we failed to post your score.');
            return true;
        }

        return startApplication('/');
    }

    /**
     * @return bool
     * @throws PublicAlert
     */
    public function golf(): bool  // This is the home page for the user
    {
        $this->rounds($_SESSION['id']);
        return true;
    }

    /**
     * @param $user_uri_id
     * @param int $limit
     * @throws PublicAlert
     */
    public function rounds($user_uri_id, $limit = 20)
    {
        global $json;


        $json['roundUser'] = [];

        Users::Get($json['roundUser'], $user_uri_id, [
            'select' => [
                Users::USER_FIRST_NAME,
                Users::USER_LAST_NAME,
                Users::USER_PROFILE_PIC,
                Users::USER_ID,
            ]
        ]);

        $json['my']['rounds'] = [];


        Rounds::Get($json['my']['rounds'], null, [
            'where' => [
                Rounds::USER_ID => $user_uri_id
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
     * @throws PublicAlert
     */
    public function stats(&$user, $id): array
    {
        if (!\is_array($user)) {
            throw new PublicAlert('Bad User Passed To Golf Stats');
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
     * @param $course_id
     * @throws PublicAlert
     */
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
        $json['state'] = $state;
        $json['courses'] = $this->coursesByState($state);
        return true;
    }


    /**
     * @param $phone
     * @param $pga_pro
     * @param $course_website
     * @param $name
     * @param $access
     * @param $style
     * @param $street
     * @param $city
     * @param $state
     * @param $tee_boxes
     * @param $handicap_number
     * @param $holes
     * @return bool|null
     * @throws PublicAlert
     */
    public function AddCourseBasic(string $phone, string $pga_pro, string $course_website, string $name, string $access,
                                   string $style, string $street, string $city, string $state, int $tee_boxes,
                                   int $handicap_number, int $holes): ?bool
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

        return self::commit(function () use ($id) : bool {
            PublicAlert::success('Course Save Started!');
            startApplication("AddCourse/Color/$id/1");
            return false; // TODO - what the hell? why cant I return the start

        });
    }


    public function AddCourseColor($courseId, $box_number, $color, $slope, $difficulty)
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



