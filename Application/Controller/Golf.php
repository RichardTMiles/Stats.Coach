<?php
namespace Controller;

use Carbon\Error\PublicAlert;
use Carbon\Request;

class Golf extends Request  // Validation
{
    
    public function golf()
    {
        return true;
    }

    public function Rounds($user_uri)
    {
        global $user_id;
        return $this->set( $user_uri )->alnum() ?: $user_id = $_SESSION['id'];  // session id must be set (route)
    }

    public function PostScore(&$state, &$course_id, &$boxColor)
    {
        $state = ucfirst( parent::set( $state )->alnum() );
        $courseId = parent::set( $course_id )->int();
        $boxColor = parent::set( $boxColor )->alnum();

        if (!$state) {
            if (!$states = fopen( SERVER_ROOT . "Data/Indexes/UnitedStates.txt", "r" ))
                throw new \Exception( "Unable to open states file!" );

            while (!feof( $states )) $this->states[] = fgets( $states );
            fclose( $states );

            return false;
        }

        if (empty($_POST)) return [$state, $course_id, $boxColor];
        global $roundDate, $newScore, $ffs, $gnr, $putts;
        
        $datePicker = $this->post( 'datepicker' )->regex( '#^\d{1,2}\/\d{1,2}\/\d{4}$#' );
        $timePicker = $this->post( 'timepicker' )->regex( '#^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]\s(A|P)M$#' );

        if (!$datePicker || !$timePicker) throw new PublicAlert('Sorry, invalid tee time provided');

        list($timePicker, $midday) = explode( ' ', $timePicker ); // trim the last to chars
        list($hour, $minute) = explode( ':', $timePicker );
        list($day, $month, $year) = explode( '/', $datePicker );
        if ($midday = 'PM') $hour += 12;

        $roundDate = mktime( $hour, $minute, 0, $month, $day, $year ) ?: time(); 

        $newScore = $this->post( 'hole-1', 'hole-2', 'hole-3', 'hole-4', 'hole-5', 'hole-6', 'hole-7', 'hole-8', 'hole-9', 'hole-10', 'hole-11', 'hole-12', 'hole-13', 'hole-14', 'hole-15', 'hole-16', 'hole-17', 'hole-18' )->int();

        foreach ($newScore as $key => $value) if (!$value) {
            $newScore = false;
            break;
        }

        if ($newScore) {
            $ffs = $this->post( 'ffs-1', 'ffs-2', 'ffs-3', 'ffs-4', 'ffs-5', 'ffs-6', 'ffs-7', 'ffs-8', 'ffs-9', 'ffs-10', 'ffs-11', 'ffs-12', 'ffs-13', 'ffs-14', 'ffs-15', 'ffs-16', 'ffs-17', 'ffs-18' )->int();
            $gnr = $this->post( 'gnr-1', 'gnr-2', 'gnr-3', 'gnr-4', 'gnr-5', 'gnr-6', 'gnr-7', 'gnr-8', 'gnr-9', 'gnr-10', 'gnr-11', 'gnr-12', 'gnr-13', 'gnr-14', 'gnr-15', 'gnr-16', 'gnr-17', 'gnr-18' )->int();
            $putts = $this->post( 'putts-1', 'putts-2', 'putts-3', 'putts-4', 'putts-5', 'putts-6', 'putts-7', 'putts-8', 'putts-9', 'putts-10', 'putts-11', 'putts-12', 'putts-13', 'putts-14', 'putts-15', 'putts-16', 'putts-17', 'putts-18' )->int();
        }

        return [$state, $course_id, $boxColor];
    }

    public function AddCourse(&$state)
    {
        global $holes, $par, $tee_boxes, $teeBox, $handicap_number, $phone, $course_website, $pga_pro;

        if ($state) $state = ucfirst( parent::set( $state )->alnum() );  // uri

        if (empty($_POST)) return false;

        $phone = $this->post( 'c_phone' )->phone();
        $pga_pro = $this->post( 'pga_pro' )->text();
        $course_website = $this->post( 'course_website' )->website();

        $validate = function ($array) {
            if (!is_array( $array ) && !empty($array)) $array[] = $array;
            if (count( $array )) foreach ($array as $key => $value) {
                if ($value === false) throw new PublicAlert( "Sorry, $key appears to be invalid", 'warning' );
            }
        };

        list($course['name'], $course['access'], $course['style'], $course['street'], $course['city'], $course['state'])
            = $this->post( 'c_name', 'c_access', 'c_style', 'c_street', 'c_city', 'c_state' )->regex( '#( *[\w])*\w+#' );

        $validate( $course );

        if (($tee_boxes = $this->post( 'tee_boxes' )->int(1,5)) === false)
            throw new PublicAlert( 'Invalid Tee Box Count' );

        switch ($course['style']) {
            case "9-hole":
                $holes = 9;
                break;
            case "Approach":
            case "Executive":
            case "18-hole":
            default:
                $holes = 18;
        }


        $par = $this->post( 'par_1', 'par_2', 'par_3', 'par_4', 'par_5', 'par_6', 'par_7', 'par_8', 'par_9', 'par_10', 'par_11', 'par_12', 'par_13', 'par_14', 'par_15', 'par_16', 'par_17', 'par_18' )->int();

        $validate( $par );

        for ($i = 1; $i <= $tee_boxes; $i++) {
            if (isset($_POST["tee_$i" . "_color"])) {
                $slope[$i] = $this->post( "general_slope_$i", "women_slope_$i" )->int();
                $difficulty[$i] = $this->post( "general_difficulty_$i", "women_difficulty_$i" )->int();
                $teeBox[$i] = $this->post( "tee_$i" . "_1", "tee_$i" . "_2", "tee_$i" . "_3", "tee_$i" . "_4", "tee_$i" . "_5", "tee_$i" . "_6", "tee_$i" . "_7", "tee_$i" . "_8", "tee_$i" . "_9", "tee_$i" . "_10", "tee_$i" . "_11", "tee_$i" . "_12", "tee_$i" . "_13", "tee_$i" . "_14", "tee_$i" . "_15", "tee_$i" . "_16", "tee_$i" . "_17", "tee_$i" . "_18" )->int();
                array_unshift( $this->teeBox[$i], $this->post( "tee_$i" . "_color" )->alnum() );
            }
        }

        $validate( $teeBox );

        if (false === ($handicap_number = $this->post( 'Handicap_number' )->int(0,2)))
            throw new PublicAlert( 'Sorry, handicap number appears invalid' );

        switch ($handicap_number) {
            case 2:     // No break
                $handicap[2] = $this->post( "hc_2_1", "hc_2_2", "hc_2_3", "hc_2_4", "hc_2_5", "hc_2_6", "hc_2_7", "hc_2_8", "hc_2_9", "hc_2_10", "hc_2_11", "hc_2_12", "hc_2_13", "hc_2_14", "hc_2_15", "hc_2_16", "hc_2_17", "hc_2_18" )->int();
                $validate( $handicap[2] );
            case 1:
                $handicap[1] = $this->post( "hc_1_1", "hc_1_2", "hc_1_3", "hc_1_4", "hc_1_5", "hc_1_6", "hc_1_7", "hc_1_8", "hc_1_9", "hc_1_10", "hc_1_11", "hc_1_12", "hc_1_13", "hc_1_14", "hc_1_15", "hc_1_16", "hc_1_17", "hc_1_18" )->int();
                $validate( $handicap[1] );
                break;
            default:
                $handicap = null;
        }

        return [$course, $handicap];
    }

}
