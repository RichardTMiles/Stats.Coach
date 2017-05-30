<?php

namespace Model;


use Model\Helpers\GolfRelay;
use Psr\Singleton;
use Modules\Database;


class Golf
{
    use Singleton;

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function courseByState($state)
    {
        $sql = "SELECT `course_name` FROM `golf_courses` WHERE `course_state`= ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($state) );
        return $stmt->fetch();
    }

    public function Golf()
    {

    }

    public function PostScore($state = false)
    {
        sortDump($_POST);
        if ($state != false) $this->courses = $this->request->courseByState($state);
    }

    public function AddCourse()
    {
        $par =  $this->par;
        for ($i=1;$i<10;$i++) $par_out += $par[$i];
        for ($i=10;$i<19;$i++) $par_in += $par[$i];
        $par_tot = $par_out + $par_in;

        $sql = "INSERT INTO golf_courses (`course_name`, `course_type`, `course_access`, `course_holes`, `course_street`, `course_city`, `course_state`, `course_phone`, `box_color_1`, `box_color_2`, `box_color_3`, `box_color_4`, `box_color_5`, `par_1`, `par_2`, `par_3`, `par_4`, `par_5`, `par_6`, `par_7`, `par_8`, `par_9`, `par_out`, `par_10`, `par_11`, `par_12`, `par_13`, `par_14`, `par_15`, `par_16`, `par_17`, `par_18`, `par_in`, `par_tot`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if (!$this->db->prepare( $sql )->execute( array($this->name, $this->type, $this->access, 18, $this->street, $this->city, $this->state, $this->phone, $this->teeColor[1] ?: 0, $this->teeColor[2] ?: 0, $this->teeColor[3] ?: 0, $this->teeColor[4] ?: 0, $this->teeColor[5] ?: 0, $par[1], $par[2], $par[3], $par[4], $par[5], $par[6], $par[7], $par[8], $par[9], $par[2], $par_out, $par[10], $par[11], $par[12], $par[13],$par[14], $par[15],$par[16], $par[17],$par[18], $par_in, $par_tot )))
            throw new \Exception("Sorry, the server could not handle the request");

        
    }


}



