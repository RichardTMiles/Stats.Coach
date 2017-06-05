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
        $this->request = Request::getInstance();
    }

    public function Golf()
    {
        return false;   
    }

    public function PostScore()
    {

        if (empty($_POST)) return false;


        list($this->c_name, $this->c_access, $this->c_style, $this->c_street, $this->c_city, $this->c_state )
            = $this->request->post( 'c_name', 'c_access', 'c_style', 'c_street', 'c_city', 'c_state' )->alnum();

        $this->c_phone = $this->request->post( 'c_phone' )->is( 'int' );

        for ($i = 1; $i < 19; $i++) {
            $_POST['par_' . $i] = $this->request->post( 'par_' . $i )->alnum(); $j = 1;
            do $this->{"tee_$j" . "_$i"} = $this->request->post( "tee_$j" . "_$i" )->alnum();    // Tee_1_12 = par_1;
            while ($j < 6 && isset($_POST['tee_' . ++$j . '_color']));
        }

        alert("past for/while");

        sortDump( $_POST );
    }

    // echo "<script>alert(\"POST()DOG\");</script>";

    public function AddCourse()
    {
        $this->teeBox1 = null;
        $this->teeBox2 = null;
        $this->teeBox3 = null;
        $this->teeBox4 = null;
        $this->teeBox5 = null;

        if (empty($_POST)) return false;

        sortDump($_POST);

        try {
            $this->par = array();

            list($this->phone) = $this->request->post('c_phone')->phone();

            $this->request->addMethod( 'post', function () {
                $only = func_get_args(); $this->storage = null;
                $error = function ($key) { throw new \Exception("Sorry, $key appears invalid."); };
                $post = function ($key) use ($error){ $this->storage[] = (array_key_exists( $key, $_POST ) && !empty($_POST[$key]) ? $_POST[$key] : $error($key)); };
                if (count( $only ) == 0 || !array_walk( $only, $post)) $this->storage = $_POST;
            });

            list($this->name, $this->access, $this->style, $this->street, $this->city, $this->state)
                = $this->request->post('c_name', 'c_access', 'c_style', 'c_street', 'c_city', 'c_state' )->regex('#( *[\w])*\w+#');

            $this->par = $this->request->post( 'par_1', 'par_2', 'par_3','par_4','par_5','par_6','par_7','par_8','par_9','par_10','par_11','par_12','par_13','par_14','par_15','par_16','par_17','par_18')->int();
            

            // TODO - validate number range
            for ($i = 1; $i < 5; $i++) {
                if (isset($_POST["tee_$i" . "_color"])) {
                    $this->teeColor[$i] = $this->request->post( "tee_$i" . "_color" )->alnum();
                    if ($this->teeColor[$i] != "none")
                        $this->teeBox[$i] = $this->request->post("tee_$i"."_1", "tee_$i"."_2", "tee_$i"."_3", "tee_$i"."_4", "tee_$i"."_5", "tee_$i" . "_6", "tee_$i" . "_7", "tee_$i" . "_8", "tee_$i" . "_9", "tee_$i" . "_10", "tee_$i" . "_11", "tee_$i"."_12", "tee_$i"."_13", "tee_$i"."_14", "tee_$i"."_15", "tee_$i"."_16", "tee_$i"."_17", "tee_$i"."_18" )->int();
                    else $this->teeBox[$i] = 0;
                }
            }

            $this->handicap[1] = $this->request->post("hc_1_1", "hc_1_2","hc_1_3","hc_1_4","hc_1_5","hc_1_6","hc_1_7","hc_1_8","hc_1_9","hc_1_10","hc_1_11","hc_1_12","hc_1_13","hc_1_14","hc_1_15","hc_1_16","hc_1_17","hc_1_18")->int();
            // hc2 may not need to be validated?
            if ($this->request->post("hc2")->alnum() != "none" || $this->handicap[2] != null)
                $this->handicap[2] = $this->request->post("hc_2_1", "hc_2_2","hc_2_3","hc_2_4","hc_2_5","hc_2_6","hc_2_7","hc_2_8","hc_2_9","hc_2_10","hc_2_11","hc_2_12","hc_2_13","hc_2_14","hc_2_15","hc_2_16","hc_2_17","hc_2_18")->int();
            else $this->handicap[2] = null;

            return true;
        } catch (\Exception $e) {
            alert( $e->getMessage() );
        } return false;
    }


}
