<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 */

namespace Model\Helpers;

use Modules\Helpers\Entities;

abstract class DataMap extends Entities
{
    const USER = 0;
    const USER_FOLLOWERS = 1;
    const USER_MESSAGES = 3;
    const USER_TASKS = 4;
    const TEAMS = 5;
    const TEAM_MEMBERS = 6;
    const GOLF_TOURNAMENTS = 7;
    const GOLF_ROUNDS = 8;
    const GOLF_COURSE = 9;
    const ENTITY_COMMENTS = 10;
    const ENTITY_PHOTOS = 11;

    protected $user = array();
    protected $team = array();
    protected $course = array();
    protected $tournament = array();

    
    public function __construct()
    {
        parent::__construct();
        static::$inTransaction = false;
        global $user, $team, $course, $tournament;
        $this->user = &$user;
        $this->team = &$team;
        $this->course = &$course;
        $this->tournament = &$tournament;
    }

}