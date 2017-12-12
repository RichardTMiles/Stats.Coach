<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 */

namespace Model\Helpers;

use Carbon\Entities;
use Carbon\Helpers\Pipe;

abstract class GlobalMap extends Entities
{
    protected $user = array();
    protected $team = array();
    protected $course = array();
    protected $tournament = array();
    
    public function __construct()
    {
        parent::__construct();
        global $user, $team, $course, $tournament;
        $this->user = &$user;
        $this->team = &$team;
        $this->course = &$course;
        $this->tournament = &$tournament;
    }

    public static function sendUpdate(string $id, string $uri){
        Pipe::send( $uri, SERVER_ROOT . 'Data/Temp/' . $id . '.fifo' );
    }

}