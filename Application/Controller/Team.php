<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/28/17
 * Time: 11:42 PM
 */

namespace Controller;


use Carbon\Error\PublicAlert;
use Carbon\Request;

class Team extends Request
{
    public function team($team_id = false)
    {
        if (!empty($_POST)) {
            global $team_photo;
            $team_photo = $this->files( 'FileToUpload' )->storeFiles('Data/Uploads/Pictures/Team/');
            return !!$team_photo;
        } elseif (!$team_id) startApplication( 'Home/' );
        return $this->set( $team_id )->alnum();
    }

    public function createTeam()
    {
        if (empty($_POST)) return false;
        list($teamName, $schoolName) = $this->post( 'teamName', 'schoolName' )->text();
        if (!$schoolName) $schoolName = null;
        return (!empty($teamName) ? [$teamName, $schoolName] : false);
    }

    public function joinTeam()
    {

        if (empty($_POST)) return false;

        if (!$teamCode = $this->post( 'teamCode' )->alnum())
            PublicAlert::warning( "Sorry, your team code appears to be invalid" );

        return $teamCode;
    }
}