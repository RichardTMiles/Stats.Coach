<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/4/17
 * Time: 12:45 AM
 */

namespace Tables;

use Model\User;
use Modules\Error\PublicAlert;
use Modules\Helpers\Bcrypt;
use \Modules\Helpers\Entities;
use Modules\Interfaces\iEntity;

class Teams extends Entities implements iEntity
{
    static function get($object, $id)
    {

    }

    static function add($object, $id, $argv)
    {
        $lambda = function (...$require) use ($argv) {
            foreach ($require as $key => $value)
                $array = $argv[$key] ?? false;
            return $array;
        };

        list( $teamName, $schoolName) = $lambda('teamName','schoolName');

        $key = self::beginTransaction( 5, $_SESSION['id'] );
        $sql = "INSERT INTO StatsCoach.teams (team_id, team_name, team_school, team_coach, team_code) VALUES (?,?,?,?,?)";
        if (!self::database()->prepare( $sql )->execute( [$key, $teamName, $schoolName, $_SESSION['id'], Bcrypt::genRandomHex( 20 )] ))
            throw new PublicAlert( 'Sorry, we we\'re unable to create your team at this time.' );
        self::commit();
        PublicAlert::success( "We successfully created `$teamName`!" );
    }

    static function remove($object, $id)
    {

    }

    static function all($object, $id)
    {
        global $team;
        $team = $team[$team_id] = self::fetch_object( 'SELECT * FROM StatsCoach.teams WHERE StatsCoach.teams.team_id = ?', $team_id );
        self::teamMembers( $team_id );
        $team = $team[$team_id] = self::fetch_object( 'SELECT * FROM StatsCoach.teams LEFT JOIN StatsCoach.entity_photos ON parent_id = team_id WHERE StatsCoach.teams.team_id = ?', $team_id );

    }

    static function range($object, $id, $argv)
    {

    }


    static function team_exists($teamIdentifier)
    {
        $sql = 'SELECT team_id FROM StatsCoach.teams WHERE team_code = :id OR team_id = :id LIMIT 1';
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':id', $teamIdentifier );
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    static function newTeamMember($team_code)
    {
        // We can assume its the session id
        $member = self::beginTransaction( 6, $_SESSION['id'] );
        if ($team_id = self::team_exists( $team_code ))
            self::database()->prepare( 'INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)' )->execute( [$member, $_SESSION['id'], $team_id] );
        else PublicAlert::danger( "The team code you provided appears to be invalid. Select `Join Team` from the menu to try again." );
        self::commit();
    }

    static function userTeams($id)
    {
        global $user;
        $stmt = self::database()->prepare( 'SELECT StatsCoach.team_members.team_id FROM StatsCoach.team_members WHERE StatsCoach.team_members.user_id = :id UNION SELECT StatsCoach.teams.team_id FROM StatsCoach.teams WHERE StatsCoach.teams.team_coach = :id' );
        $stmt->bindValue( ':id', $id );
        $stmt->execute();
        $user[$id]->teams = $stmt->fetchAll( \PDO::FETCH_COLUMN );
    }

    static function teamMembers($id)
    {
        global $team, $user;
        $my = $team[$id];
        $sql = 'SELECT user_id FROM StatsCoach.team_members WHERE team_id = ?';
        $stmt = self::database()->prepare( $sql );
        $stmt->execute( [$id] );
        $my->members = is_array( $stmt = $stmt->fetchAll( \PDO::FETCH_COLUMN ) ) ? $stmt : [];

        if (!empty( $my->members ))
            foreach ($my->members as $user_id)
                if ($user[$user_id] ?? false)
                    new User( $user_id );
    }



}