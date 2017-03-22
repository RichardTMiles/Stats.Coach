<?php

namespace Model\Helpers;


use Modules\Database;

class GolfRelay
{
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
}