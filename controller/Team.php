<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/28/17
 * Time: 11:42 PM
 */

namespace Controller;


use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Request;

class Team extends Request
{
    /**
     * @param bool $team_id
     * @return array|bool|mixed
     * @throws \CarbonPHP\Error\PublicAlert
     */
    public function team($team_id = false)
    {
        if (!empty($_POST)) {
            global $team_photo;
            $team_photo = $this->files( 'FileToUpload' )->storeFiles('Data/Uploads/Pictures/Team/');
            return (bool) $team_photo;
        }

        if (!$team_id) {
            startApplication( 'Home/' );
            return false;
        }

        return $this->set( $team_id )->alnum();
    }

    /**
     * @return array|null
     */
    public function createTeam() : ?array
    {
        if (empty($_POST)) {
            return null;
        }
        [$teamName, $schoolName]
            = $this->post( 'teamName', 'schoolName' )->text();

        if (!$schoolName) {
            $schoolName = null;
        }
        return (!empty($teamName) ? [$teamName, $schoolName] : null);
    }

    /**
     * @return array|bool|mixed|null
     * @throws \CarbonPHP\Error\PublicAlert
     */
    public function joinTeam()
    {
        if (empty($_POST)) {
            return null;
        }

        if (!$teamCode = $this->post( 'teamCode' )->alnum()) {
            throw new PublicAlert( 'Sorry, your team code appears to be invalid' );
        }

        return $teamCode;
    }
}