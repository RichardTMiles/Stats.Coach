<?php

namespace Controller;

use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Request;
use Table\carbon_locations;
use Table\golf_course;

class Golf extends Request  // Validation
{
    public function golf(): ?bool
    {
        return null;    // This will skip the model and just run the view
    }

    public function Rounds($user_uri)
    {
        global $user_id;
        return $this->set($user_uri)->alnum() ?: $user_id = $_SESSION['id'];  // session id must be set (route)
    }

    public function coursesByState($state)
    {
        return $this->set($state)->word();
    }

    /**
     * @param $state
     * @param $course_id
     * @param $boxColor
     * @return array|bool
     * @throws PublicAlert
     */
    public function PostScore(&$state, &$course_id, &$boxColor)
    {
        global $json;

        if (!$state) {
            $json['step1'] = true;
            return null;                                // goto view
        }

        $state = ucfirst($this->set($state)->alnum());
        $course_id = $this->set($course_id)->alnum();         // hex id
        $boxColor = $this->set($boxColor)->alnum();

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

        golf_course::Get($json['course'], null, [
            'where'=>[
               'created_by'=>$_SESSION['id'],
                'course_input_completed'=> 0
            ],
            'pagination' => [
                'limit'=>1
            ]
        ]);

        // sortDump($json['course']);
        # $argv = array_pop($json['sql']);
        # sortDump([$argv, $json['course']]);

        if (!empty($json['course'])) {
            $json['course']['location'] = [];
            carbon_locations::Get($json['course']['location'], $json['course']['course_id'], []);
            var_dump($json['course']);
            return null;
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

        return [$phone, $pga_pro, $course_website, $name, $access, $style, $street, $city, $state, $tee_boxes,$handicap_number, $holes];

    }


    public function AddCourseColor($courseId, $box_number)
    {
        if (empty($_POST)) {
            return null;
        }

        $color = $this->post('color')->word();

        if (!\in_array($color, [
            'blue',
            'black',
            'green',
            'gold',
            'red',
            'white'])) {
            throw new PublicAlert('That appears to be an incorrect color choice. What did you do?');
        }

        if (!$this->set($box_number)->int(1, 5)) {
            throw new PublicAlert('That appears to be an incorrect tee box choice. What did you do?');
        }

        if (!$this->set($courseId)->hex()) {
            throw new PublicAlert('That appears to be an incorrect course ID. What did you do?');
        }

        $slope = $this->post("general_slope", "women_slope")->int();

        $difficulty = $this->post("general_difficulty", "women_difficulty")->int();

        return [$courseId, $box_number, $color, $slope, $difficulty];
    }

    /**
     * @param $courseId
     * @param $holeNumber
     * @return array
     * @throws PublicAlert
     */
    public function AddCourseDistance($courseId, $holeNumber)
    {

        [$courseId, $holeNumber] = $this->set($courseId, $holeNumber)->hex();

        if (!($courseId && $holeNumber)) {
            throw new PublicAlert('Failed loading course!');
        }


        [$par, $distance]= $this->post('par', 'distance')->int();

        if (!($par && $distance)) {
            throw new PublicAlert('Failed loading course!');
        }

        $handicap = [];

        switch ($handicap_number) {
            case 2:     // No break
                $handicap[2] = $this->post('hc_2_1', 'hc_2_2', 'hc_2_3', 'hc_2_4', 'hc_2_5', 'hc_2_6', 'hc_2_7', 'hc_2_8', 'hc_2_9', 'hc_2_10', 'hc_2_11', 'hc_2_12', 'hc_2_13', 'hc_2_14', 'hc_2_15', 'hc_2_16', 'hc_2_17', 'hc_2_18')->int();
                $validate($handicap[2]);
            case 1:
                $handicap[1] = $this->post('hc_1_1', 'hc_1_2', 'hc_1_3', 'hc_1_4', 'hc_1_5', 'hc_1_6', 'hc_1_7', 'hc_1_8', 'hc_1_9', 'hc_1_10', 'hc_1_11', 'hc_1_12', 'hc_1_13', 'hc_1_14', 'hc_1_15', 'hc_1_16', 'hc_1_17', 'hc_1_18')->int();
                $validate($handicap[1]);
                break;
            default:
                $handicap = null;
        }

        $par_out = 0;
        $par_in = 0;
        for ($i = 0; $i < 9; $i++) {
            $par_out += $par[$i];
        }
        for ($i = 9; $i < 18; $i++) {
            $par_in += $par[$i];
        }
        $par_tot = $par_out + $par_in;

        return [$courseId, $holeNumber, $par, $par_tot, $par_out, $par_in, $handicap, $teeBox];
    }


}
