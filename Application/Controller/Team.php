<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/28/17
 * Time: 11:42 PM
 */

namespace Controller;


use Modules\Helpers\Reporting\PublicAlert;
use Modules\Request;

class Team extends Request
{
    public function team($team_id)
    {
        return $this->set( $team_id )->alnum();
    }

    public function createTeam()
    {
        if (empty($_POST)) return false;
        list($teamName, $schoolName) = $this->post( 'teamName', 'schoolName' )->text();
        return (empty($teamName) || empty($schoolName)) ? [$teamName, $schoolName] : false;
    }

    public function joinTeam()
    {
        if (empty($_POST)) return false;

        if (!$teamCode = $this->post( 'teamCode' )->alnum())
            PublicAlert::warning("Sorry, your team code appears to be invalid");

        return $teamCode;
    }
}