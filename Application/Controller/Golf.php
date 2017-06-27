<?php
namespace Controller;

use Modules\Request;
use Psr\Singleton;

class Golf
{
    use Singleton;

    private $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function Golf()
    {
        return true;
    }

    public function PostScore()
    {
        $this->courseId = $this->request->set( $this->courseId )->int();
        $this->state = ucfirst( $this->request->set( $this->state )->alnum() );
        $this->boxColor = $this->request->set( $this->boxColor )->alnum();

        if (empty($_POST) && !empty($this->state))
            return true;

        if (!empty($_POST)) {
            $this->ffs = $this->request->post( 'ffs-1','ffs-2','ffs-3','ffs-4','ffs-5','ffs-6','ffs-7','ffs-8','ffs-9','ffs-10','ffs-11','ffs-12','ffs-13','ffs-14','ffs-15','ffs-16','ffs-17','ffs-18' )->int();
            $this->gnr = $this->request->post( 'gnr-1','gnr-2','gnr-3','gnr-4','gnr-5','gnr-6','gnr-7','gnr-8','gnr-9','gnr-10','gnr-11','gnr-12','gnr-13','gnr-14','gnr-15','gnr-16','gnr-17','gnr-18' )->int();
            $this->newScore = $this->request->post( 'hole-1','hole-2','hole-3','hole-4','hole-5','hole-6','hole-7','hole-8','hole-9','hole-10','hole-11','hole-12','hole-13','hole-14','hole-15','hole-16','hole-17','hole-18' )->int();
        }
        return !(empty($this->newScore));
    }

    public function AddCourse()
    {
        if ($this->state)
            $this->state = $this->request->set($this->state)->alnum();  // uri

        if (empty($_POST))
            return false;

        $this->course['phone'] = $this->request->post( 'c_phone' )->phone();

        // sortDump();

        $validate = function ($array) {
            if (!is_array( $array ) && !empty($array)) $array[] = $array;
            if (count($array)) foreach ($array as $key => $value) {
                if ($value === false && $key != 'phone')
                    throw new \Exception( "Sorry, $key appears to be invalid" );
            }
        };
        try {
            list($this->course['name'], $this->course['access'], $this->course['style'], $this->course['street'], $this->course['city'], $this->course['state'])
                = $this->request->post( 'c_name', 'c_access', 'c_style', 'c_street', 'c_city', 'c_state' )->regex( '#( *[\w])*\w+#' );

            $validate( $this->course );

            if (false === ($this->tee_boxes = $this->request->post( 'tee_boxes' )->int()))
                throw new \Exception( 'Invalid Tee Box Count' );

            switch ($this->course['style']) {
                case "9-hole":
                    $this->holes = 9;
                    break;
                case "Approach":
                case "Executive":
                case "18-hole":
                default:
                    $this->holes = 18;
            }

            $this->par = $this->request->post( 'par_1', 'par_2', 'par_3', 'par_4', 'par_5', 'par_6', 'par_7', 'par_8', 'par_9', 'par_10', 'par_11', 'par_12', 'par_13', 'par_14', 'par_15', 'par_16', 'par_17', 'par_18' )->int();

            $validate( $this->par );

            // TODO - validate number range
            for ($i = 1; $i <= $this->tee_boxes; $i++) {
                if (array_key_exists( "tee_$i" . "_color", $_POST )) {
                    $this->slope[$i] = $this->request->post( "general_slope_$i", "women_slope_$i" )->int();
                    $this->difficulty[$i] = $this->request->post("general_difficulty_$i","women_difficulty_$i" )->int();
                    $this->teeBox[$i] = $this->request->post( "tee_$i" . "_1", "tee_$i" . "_2", "tee_$i" . "_3", "tee_$i" . "_4", "tee_$i" . "_5", "tee_$i" . "_6", "tee_$i" . "_7", "tee_$i" . "_8", "tee_$i" . "_9", "tee_$i" . "_10", "tee_$i" . "_11", "tee_$i" . "_12", "tee_$i" . "_13", "tee_$i" . "_14", "tee_$i" . "_15", "tee_$i" . "_16", "tee_$i" . "_17", "tee_$i" . "_18" )->int();
                    array_unshift( $this->teeBox[$i], $this->request->post( "tee_$i" . "_color" )->alnum() );
                }
            }

            $validate( $this->teeBox );

            if (false === ($this->handicap_number = $this->request->post( 'Handicap_number' )->int()))
                throw new \Exception( 'Sorry, handicap number appears invalid' );


            switch ($this->handicap_number) {
                case 2:     // No break
                    $this->handicap[2] = $this->request->post( "hc_2_1", "hc_2_2", "hc_2_3", "hc_2_4", "hc_2_5", "hc_2_6", "hc_2_7", "hc_2_8", "hc_2_9", "hc_2_10", "hc_2_11", "hc_2_12", "hc_2_13", "hc_2_14", "hc_2_15", "hc_2_16", "hc_2_17", "hc_2_18" )->int();
                    $validate( $this->handicap[2] );
                case 1:
                    $this->handicap[1] = $this->request->post( "hc_1_1", "hc_1_2", "hc_1_3", "hc_1_4", "hc_1_5", "hc_1_6", "hc_1_7", "hc_1_8", "hc_1_9", "hc_1_10", "hc_1_11", "hc_1_12", "hc_1_13", "hc_1_14", "hc_1_15", "hc_1_16", "hc_1_17", "hc_1_18" )->int();
                    $validate( $this->handicap[1] );
                    break;
                default:
                    $this->handicap = null;
            }

            return true;
        } catch (\Exception $e) {
            $this->alert['danger'] = $e->getMessage();
        }
        return false;
    }


}
