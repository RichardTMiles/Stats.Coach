<?php

namespace Controller;

use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Request;
use Tables\carbon_locations;
use Tables\carbon_golf_courses as Course;

class Golf extends Request  // Validation
{

    public function coursesByState($state) {
        // TODO - validate state
        if (!$this->set($state)->word()) {
            return null;
        }
        return $state;
    }


    public function NewTournament($state = null)
    {
        global $json;

        if ($state && $this->PostScoreBasic($state)) {
            $json['course'] = (new \Model\Golf())->coursesByState($state);
            return null;
        }

        if (empty($_POST)) {
            return null;
        }

        [$tournamentName, $hostName] = $this->post('tournamentName', 'hostName')->words();

        [$hostID, $courseID] = $this->post('hostID', 'courseID')->hex();

        if (!$hostID) {
            PublicAlert::danger('The host input appears incorrect.');
            return null;
        }

        if (!$courseID) {
            PublicAlert::danger('The course selected appears incorrect.');
            return null;
        }

        $playStyles = [
            'Match_Play',
            'Stroke_Play',
            'Best_Ball',
            'Scramble',
            'Alternate_Shot',
            'Four_Ball',
            'Skins_Game',
            'Ryder_Cup',
            'Shamble',
            'Stableford',
            'Chapman_or_Pinehurst',
            'Bingo_Bango_Bongo',
            'Flags',
            'Money_Ball_or_Lone_Ranger',
            'Quota_Tournament',
            'Peoria_System'
        ];

        if (!in_array($_POST['style'], $playStyles)) {
            PublicAlert::danger('The play style appears incorrect.');
            return null;
        }

        if (!$tournamentName || !$hostName) {
            PublicAlert::danger('You must provide input for both input fields.');
            return null;
        }

        return [$tournamentName, $hostName, $hostID, $courseID, $_POST['style']];
    }

    /**
     * @param $id
     * @param $color
     * @return array|bool
     * @throws PublicAlert
     */
    public function PostScoreDistance($id, $color)
    {
        global $json;

        if (!ctype_xdigit($id)) {
            PublicAlert::danger('Failed to load course.');
            return startApplication('/PostScore/Basic');
        }

        if (!$this->set($color)->word()) {
            PublicAlert::danger('The color chosen appears invalid.');
            return startApplication("/PostScore/$id");
        }

        //todo validate json
        $json['score_color_input'] = $color;

        if (empty($_POST)) {
            return [$id, $color];
        }

        $datePicker = $this->post('datepicker')->regex('#^\d{1,2}\/\d{1,2}\/\d{4}$#');
        $timePicker = $this->post('timepicker')->regex('#^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9]\s(A|P)M$#');

        if (!$datePicker || !$timePicker) {
            throw new PublicAlert('Sorry, invalid tee time provided.');
        }

        [$timePicker, $midday] = explode(' ', $timePicker); // trim the last to chars
        [$hour, $minute] = explode(':', $timePicker);
        [$day, $month, $year] = explode('/', $datePicker);

        if ($midday === 'PM') {
            $hour += 12;
        }

        $roundDate = mktime($hour, $minute, 0, $month, $day, $year) ?: time();

        $newScore = $this->post('hole-1', 'hole-2', 'hole-3', 'hole-4', 'hole-5', 'hole-6', 'hole-7', 'hole-8', 'hole-9', 'hole-10', 'hole-11', 'hole-12', 'hole-13', 'hole-14', 'hole-15', 'hole-16', 'hole-17', 'hole-18')->int();

        foreach ($newScore as $key => $value) {
            if (!$value) {
                $newScore = false;
                break;
            }
        }

        if ($newScore) {
            $ffs = $this->post('ffs-1', 'ffs-2', 'ffs-3', 'ffs-4', 'ffs-5', 'ffs-6', 'ffs-7', 'ffs-8', 'ffs-9', 'ffs-10', 'ffs-11', 'ffs-12', 'ffs-13', 'ffs-14', 'ffs-15', 'ffs-16', 'ffs-17', 'ffs-18')->is('bool');
            $gnr = $this->post('gnr-1', 'gnr-2', 'gnr-3', 'gnr-4', 'gnr-5', 'gnr-6', 'gnr-7', 'gnr-8', 'gnr-9', 'gnr-10', 'gnr-11', 'gnr-12', 'gnr-13', 'gnr-14', 'gnr-15', 'gnr-16', 'gnr-17', 'gnr-18')->is('bool');
            $putts = $this->post('putts-1', 'putts-2', 'putts-3', 'putts-4', 'putts-5', 'putts-6', 'putts-7', 'putts-8', 'putts-9', 'putts-10', 'putts-11', 'putts-12', 'putts-13', 'putts-14', 'putts-15', 'putts-16', 'putts-17', 'putts-18')->int();
        }


        return [
            $id, $color, [
                'date' => $roundDate,
                'shots' => $newScore,
                'ffs' => $ffs,
                'gnr' => $gnr,
                'putts' => $putts
            ]
        ];
    }

    public function golf(): ?bool
    {
        return true;    // moving to the model to get round data
    }

    public function Rounds($user_uri)
    {
        return $this->set($user_uri)->alnum() ?: $_SESSION['id'];  // session id must be set (route)
    }

    public function PostScoreBasic($state)
    {
        return $this->coursesByState($state);
    }

    public function PostScoreColor($id)
    {
        if (!ctype_xdigit($id)) {
            throw new PublicAlert('Could not load the course requested!');
        }
        return $id;
    }

    /**
     * @param $state
     * @param $course_id
     * @param $boxColor
     * @return array|bool
     * @throws PublicAlert
     */
    public function PostScore($state, $course_id, $boxColor)
    {
        global $json;

        if (!$state) {
            $json['step1'] = true;
            return null;                                // goto view
        }

        $state = ucfirst(strtolower($this->set($state)->alnum()));

        $course_id = $this->set($course_id)->hex();         // hex id

        $boxColor = $this->set($boxColor)->word();

        if (empty($_POST)) {
            return [$state, $course_id, $boxColor];     // goto the model
        }

        global $roundDate, $newScore, $ffs, $gnr, $putts;

        $datePicker = $this->post('datepicker')->regex('#^\d{1,2}\/\d{1,2}\/\d{4}$#');
        $timePicker = $this->post('timepicker')->regex('#^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]\s(A|P)M$#');

        if (!$datePicker || !$timePicker) {
            throw new PublicAlert('Sorry, invalid tee time provided');
        }

        [$timePicker, $midday] = explode(' ', $timePicker); // trim the last to chars
        [$hour, $minute] = explode(':', $timePicker);
        [$day, $month, $year] = explode('/', $datePicker);

        if ($midday === 'PM') {
            $hour += 12;
        }

        $roundDate = mktime($hour, $minute, 0, $month, $day, $year) ?: time();

        $newScore = $this->post('hole-1', 'hole-2', 'hole-3', 'hole-4', 'hole-5', 'hole-6', 'hole-7', 'hole-8', 'hole-9', 'hole-10', 'hole-11', 'hole-12', 'hole-13', 'hole-14', 'hole-15', 'hole-16', 'hole-17', 'hole-18')->int();

        foreach ($newScore as $key => $value) {
            if (!$value) {
                $newScore = false;
                break;
            }
        }

        if ($newScore) {
            $ffs = $this->post('ffs-1', 'ffs-2', 'ffs-3', 'ffs-4', 'ffs-5', 'ffs-6', 'ffs-7', 'ffs-8', 'ffs-9', 'ffs-10', 'ffs-11', 'ffs-12', 'ffs-13', 'ffs-14', 'ffs-15', 'ffs-16', 'ffs-17', 'ffs-18')->int();
            $gnr = $this->post('gnr-1', 'gnr-2', 'gnr-3', 'gnr-4', 'gnr-5', 'gnr-6', 'gnr-7', 'gnr-8', 'gnr-9', 'gnr-10', 'gnr-11', 'gnr-12', 'gnr-13', 'gnr-14', 'gnr-15', 'gnr-16', 'gnr-17', 'gnr-18')->int();
            $putts = $this->post('putts-1', 'putts-2', 'putts-3', 'putts-4', 'putts-5', 'putts-6', 'putts-7', 'putts-8', 'putts-9', 'putts-10', 'putts-11', 'putts-12', 'putts-13', 'putts-14', 'putts-15', 'putts-16', 'putts-17', 'putts-18')->int();
        }

        return [$state, $course_id, $boxColor];
    }

    /**
     * @param $state
     * @return mixed
     * @throws PublicAlert
     */
    public function AddCourseBasic(&$state)
    {
        global $json;

        $json['course'] = [];

        Course::Get($json['course'], null, [
            'where' => [
                Course::CREATED_BY => $_SESSION['id'],
                Course::COURSE_INPUT_COMPLETED => 0
            ],
            'pagination' => [
                'limit' => 1
            ]
        ]);

        if (!empty($json['course'])) {
            PublicAlert::success('It seems like you have unfinished changes! Please finish entering the data for this course.');
            $json['course']['location'] = [];
            carbon_locations::Get($json['course']['location'], $json['course']['course_id'], []);
        }

        if ($state) {
            $state = ucfirst(parent::set($state)->alnum());
        }  // uri

        if (empty($_POST)) {
            return null;
        }

        $phone = $this->post('c_phone')->phone();
        $pga_pro = $this->post('pga_pro')->text();
        $course_website = $this->post('course_website')->website();


        [$name, $access, $style, $street, $city, $state]
            = $this->post('c_name', 'c_access', 'c_style', 'c_street', 'c_city', 'c_state')->regex('#( *[\w])*\w+#');


        if (($tee_boxes = $this->post('tee_boxes')->int(1, 5)) === false) {
            throw new PublicAlert('Invalid Tee Box Count');
        }


        if (false === ($handicap_number = $this->post('Handicap_number')->int(0, 2))) {
            throw new PublicAlert('Sorry, handicap number appears invalid');
        }

        switch ($style) {
            case '9-hole':
                $holes = 9;
                break;
            case 'Approach':
            case 'Executive':
            case '18-hole':
            default:
                $holes = 18;
        }

        return [$phone, $pga_pro, $course_website, $name, $access, $style, $street, $city, $state, $tee_boxes, $handicap_number, $holes];

    }


    public function AddCourseColor($courseId, $box_number)
    {
        global $json;

        $json['current_hole'] = $box_number;

        if ($courseId !== ($json['course']['course_id'] ?? false)) {
            $json['course'] = [];
            if (!Course::Get($json['course'], $courseId, [])) {
                throw new PublicAlert('Failed to get course data');
            }
        }

        if (\count($json['course']['course_tee_boxes']) + 1 < $box_number) {
            PublicAlert::danger('Something when wrong. Moved to the correct input.');
            return startApplication("AddCourse/Color/$courseId/$box_number/");
        }

        if ($box_number > $json['course']['tee_boxes']) {
            return startApplication('AddCourse/Distance/' . $json['course']['course_id'] . '/1/');
        }

        $json['addColor'] = $json['course']['course_tee_boxes'][$box_number] ?? [];

        if (empty($_POST)) {
            return null;
        }

        $color = ucfirst(strtolower($this->post('color')->word()));

        // Lets make a new standard, we will always "softly" validate url prams first
        if (!$this->set($box_number)->int(1, 5)) {
            throw new PublicAlert('That appears to be an incorrect tee box choice.');
        }

        if (!$this->set($courseId)->hex()) {
            PublicAlert::danger('That appears to be an incorrect course ID.');
            return startApplication(true);
        }

        if (!\in_array($color, [
            'Blue',
            'Black',
            'Green',
            'Gold',
            'Red',
            'White'])) {
            throw new PublicAlert('That appears to be an incorrect color choice.');
        }

        $slope = $this->post('general_slope', 'women_slope')->int();

        $difficulty = $this->post('general_difficulty', 'women_difficulty')->int();

        return [$courseId, $box_number, $color, $slope, $difficulty];
    }

    /**
     * @param $courseId
     * @param $holeNumber
     * @return array|bool
     * @throws PublicAlert
     */
    public function AddCourseDistance($courseId, $holeNumber)
    {
        global $json;

        $courseId = $this->set($courseId)->hex();

        $holeNumber = $this->set($holeNumber)->int(1, 18);

        if (!($courseId && $holeNumber)) {
            throw new PublicAlert('Failed loading course!');
        }

        $json['current_hole'] = $holeNumber;

        if ($courseId !== ($json['course']['course_id'] ?? false)) {
            $json['course'] = [];
            if (!Course::Get($json['course'], $courseId, [])) {
                throw new PublicAlert('Failed to get course data');
            }
        }

        $json['par'] = $json['course']['course_par']['par'][$holeNumber] ?? 1;


        $json['addDist'] = [];      // otherwise inputs will duplicate with startApplication is called with ++holeNumber

        foreach ($json['course']['course_tee_boxes'] as $key => $value) {
            $json['addDist'][] =
                [
                    'color' => ucfirst($value['color']),
                    'distance' => (int)($json['course']['course_par'][$value['color']][$holeNumber] ?? 1),
                ];
        }

        // This is only for the mustache template and isn't used anywhere else in this Controller/Model
        switch ($json['course']['handicap_count']) {
            case 1:
                $json['handicap'] = [];
                break;
            case 2:
                $json['handicap'] = [
                    [
                        'name' => 'Men',
                        'value' => $json['course']['course_handicap'][$holeNumber]['m'] ?? 1
                    ],
                    [
                        'name' => 'Women',
                        'value' => $json['course']['course_handicap'][$holeNumber]['w'] ?? 1
                    ]
                ];
                break;
        }

        if (!$_POST) {
            return null;    // display the template, dont goto the model
        }

        switch ($json['course']['handicap_count']) {
            case 1:
                $handicap = $this->post('hc')->int();
                break;
            case 2:
                $handicap = $this->post('hc_Men', 'hc_Women')->int();
                break;
            default:
                $handicap = null;
        }

        $color = [];
        // we need to get the colors out of the previous array
        // Im going to be very uncreated about this
        foreach ($json['course']['course_tee_boxes'] as $key => $value) {
            $color[$value['color']] = $this->post(ucfirst($value['color']))->int();
        }

        if (\in_array(false, $color, true)) {
            throw new PublicAlert('Damn, something peculiar happened.');
        }

        $par = $this->post($holeNumber)->int();

        return [$courseId, $holeNumber, $par, $handicap, $color];
    }


}
