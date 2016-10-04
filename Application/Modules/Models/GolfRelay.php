<?php

namespace App\Modules\Models;

use App\Models\Database\db;


class GolfRelay extends db
{

    public function courseByState($state)
    {
        return parent::getRow( "SELECT `course_name` FROM `golf_courses` 
				WHERE `course_state`= ?", array($state) );

    }
}