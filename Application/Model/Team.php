<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/21/17
 * Time: 1:24 PM
 */

namespace Model;

use Carbon\Database;
use Model\Helpers\GlobalMap;
use Carbon\Helpers\Bcrypt;
use Psr\Log\InvalidArgumentException;
use Tables\Photos;
use Tables\Teams;
use Tables\Users;
use Carbon\Error\PublicAlert;
use Carbon\Singleton;

class Team extends GlobalMap
{
    use Singleton;

    public function team(string $teamIdentifier)
    {
        global $team_id, $team_photo;
        if (!$team_id = Teams::team_exists( $teamIdentifier ))
            return startApplication( 'Home/' );

        // if (!is_object($this->team[$team_id] ?? false))
        $team = Teams::get($this->team[$team_id], $team_id);

        if ($team_photo ?? false && $team['team_coach'] == $_SESSION['id']) {
            // unlink( $team->photo_path ); TODO - Delete photo from db
            Photos::add( $team, $team_id, [
                'photo_path' => "$team_photo",
                'photo_description' => "profile pic"] );
        }


        if (isset( $this->user[$this->team[$team_id]['team_coach']] ))
            return true;

        if ($team['team_coach'] === null)
            throw new \Exception( 'Why is there no coach?' );

        Users::get( $this->user[$team['team_coach']], $team['team_coach'] );

        return true;
    }


    static function createTeam($teamName, $schoolName = null)
    {
        $key = self::beginTransaction( 5, $_SESSION['id'] );
        $sql = "INSERT INTO StatsCoach.teams (team_id, team_name, team_school, team_coach, team_code) VALUES (?,?,?,?,?)";
        if (!Database::database()->prepare( $sql )->execute( [$key, $teamName, $schoolName, $_SESSION['id'], Bcrypt::genRandomHex( 20 )] ))
            throw new PublicAlert( 'Sorry, we we\'re unable to create your team at this time.' );

        $sql = "UPDATE StatsCoach.user SET user_type = 'Coach' WHERE user_id = ?";
        if (!Database::database()->prepare($sql)->execute([$_SESSION['id']]))
            throw new PublicAlert('Sorry, we we\'re unable to create your team at this time.');
        self::commit();
        PublicAlert::success( "We successfully created `$teamName`!" );
        startApplication(true);
    }

    public function joinTeam($teamCode)
    {
        if (!$teamId = Teams::team_exists( $teamCode ))
            throw new PublicAlert( 'The team code you provided appears to be invalid.', 'warning' );

        Teams::all( $this->user[$_SESSION['id']], $_SESSION['id'] );

        if (array_key_exists( $teamCode, $this->user[$_SESSION['id']]['teams'] ))
            throw new PublicAlert( 'It appears you are already a member of this team.', 'warning' );

        $member = $this->beginTransaction( 6, $_SESSION['id'] );
        $sql = "INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)";
        if (!$this->db->prepare( $sql )->execute( [$member, $_SESSION['id'], $teamId] ))
            throw new PublicAlert( 'Unable to join this team. ', '' );
        $this->commit();

        PublicAlert::success( 'We successfully add you!' );
        startApplication( true );

    }



}