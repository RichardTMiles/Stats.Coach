<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/21/17
 * Time: 1:24 PM
 */

namespace Model;

use Model\Helpers\GlobalMap;
use CarbonPHP\Helpers\Bcrypt;
use Tables\carbon_photos as Photos;
use Tables\carbon_teams as Teams;
use Tables\carbon_users as Users;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Singleton;

class Team extends GlobalMap
{
    use Singleton;

    /**
     * @param string $teamIdentifier
     * @return bool
     * @throws \RuntimeException
     * @throws \CarbonPHP\Error\PublicAlert
     */
    public function team(string $teamIdentifier)
    {
        global $team_id, $team_photo;

        if (!$team_id = Teams::team_exists( $teamIdentifier )) {
            startApplication( 'Home/' );
            return false;
        }

        // if (!is_object($this->team[$team_id] ?? false))
        $team = Teams::get($this->team[$team_id], $team_id);

        if (($team_photo ?? false ) && $team['team_coach'] == $_SESSION['id']) {
            // unlink( $team->photo_path ); TODO - Delete photo from db
            Photos::add( $team, $team_id, [
                'photo_path' => "$team_photo",
                'photo_description' => "profile pic"] );
        }

        if (isset( $this->user[$this->team[$team_id]['team_coach']] )) {
            return true;
        }

        if ($team['team_coach'] === null) {
            throw new \RuntimeException( 'Why is there no coach?' );
        }

        // Users::Get( $this->user[$team['team_coach']], $team['team_coach'] );

        return true;
    }


    /**
     * @param $teamName
     * @param null $schoolName
     * @throws PublicAlert
     */
    public function createTeam($teamName, $schoolName = null)
    {
        if (!Teams::Post([
            'team_name' => $teamName,
            'team_school'=> $schoolName,
            'team_coach' => $_SESSION['id'],
            'team_code' => Bcrypt::genRandomHex( 20 )
        ])) {
            throw new PublicAlert( 'Sorry, we we\'re unable to create your team at this time.' );
        }

        $sql = 'UPDATE carbon_users SET user_type = "Coach" WHERE user_id = ?';

        $return = [];

        if (!Users::Put($return, $_SESSION['id'], ['user_type' => 'Coach'])) {
            throw new PublicAlert('Sorry, we we\'re unable to create your team at this time.');
        }

        self::commit();

        PublicAlert::success( "We successfully created `$teamName`!" );

        startApplication(true);
    }

    /**
     * @param $teamCode
     * @throws PublicAlert
     */
    public function joinTeam($teamCode)
    {
        if (!$teamId = Teams::team_exists( $teamCode )) {
            throw new PublicAlert( 'The team code you provided appears to be invalid.', 'warning' );
        }

        Teams::all( $this->user[$_SESSION['id']], $_SESSION['id'] );

        if (array_key_exists( $teamCode, $this->user[$_SESSION['id']]['teams'] )) {
            throw new PublicAlert( 'It appears you are already a member of this team.', 'warning' );
        }

        $member = self::beginTransaction( 6, $_SESSION['id'] );
        $sql = 'INSERT INTO StatsCoach.carbon_team_members (member_id, user_id, team_id) VALUES (?,?,?)';

        if (!$this->db->prepare( $sql )->execute( [$member, $_SESSION['id'], $teamId] )) {
            throw new PublicAlert( 'Unable to join this team. ', '' );
        }

        self::commit();

        PublicAlert::success( 'We successfully add you!' );

        startApplication( true );

    }



}