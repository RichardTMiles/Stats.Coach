<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/4/17
 * Time: 12:45 AM
 */

namespace Tables;

use Carbon\Database;
use Model\User;
use Carbon\Error\PublicAlert;
use Carbon\Helpers\Bcrypt;
use \Carbon\Entities;
use Carbon\Interfaces\iEntity;

class Teams extends Entities implements iEntity
{
    static function get(&$team, $id)   // team obj
    {
        $team = self::fetch( 'SELECT * FROM StatsCoach.teams WHERE StatsCoach.teams.team_id = ?', $id );
        if (!is_array($team))
            throw new \Exception('Fetch teams invalid');
        Photos::all( $team, $id );
        self::members( $team, $id );
        return $team;
    }

    static function all(&$user, $id)   // user obj, reset teams
    {
        global $team; // array, referenced for static function

        $temp = self::fetch( 'SELECT teams.team_id, team_coach, parent_team, team_code, team_name, 
          team_rank, team_sport, team_division, team_school, team_district, team_membership, team_photo FROM StatsCoach.teams LEFT JOIN StatsCoach.team_members ON StatsCoach.teams.team_coach = ? or member_id = ?', $id ,$id );

        if (array_key_exists('team_id', $temp))
            $temp = [$temp];

        foreach ($temp as $id => $array) {
            $team[$array['team_id']] = $array;
            $user['teams'][] = $array['team_id'];
            self::members( $team[$array['team_id']], $array['team_id'] );
            Photos::all( $team[$array['team_id']], $array['team_id']);
        }
        return $user;
    }

    static function members(&$team, $id)  // Team obj
    {
        global $user;

        $sql = 'SELECT user_id FROM StatsCoach.team_members WHERE team_id = ?';
        $stmt = Database::database()->prepare( $sql );
        $stmt->execute( [$id] );
        $team['members'] = is_array( $stmt = $stmt->fetchAll( \PDO::FETCH_COLUMN ) ) ? $stmt : [$stmt];

        if (!empty( $team['members'] )) foreach ($team['members'] as $user_id)
            if (!is_array($user) || !array_key_exists($user_id, $user))     // cache
                new User( $user_id );

        return $team;
    }

    static function add(&$object, $id, $argv)
    {
        $lambda = function (...$require) use ($argv) {
            foreach ($require as $key => $value)
                $array = $argv[$key] ?? false;
            return $array;
        };

        list( $teamName, $schoolName ) = $lambda( 'teamName', 'schoolName' );

        $key = self::beginTransaction( 5, $_SESSION['id'] );
        $sql = "INSERT INTO StatsCoach.teams (team_id, team_name, team_school, team_coach, team_code) VALUES (?,?,?,?,?)";
        if (!Database::database()->prepare( $sql )->execute( [$key, $teamName, $schoolName, $_SESSION['id'], Bcrypt::genRandomHex( 20 )] ))
            throw new PublicAlert( 'Sorry, we we\'re unable to create your team at this time.' );
        self::commit();
        PublicAlert::success( "We successfully created `$teamName`!" );
        return true;
    }

    static function remove(&$object, $id)
    {

    }

    static function range(&$object, $id, $argv)
    {

    }

    static function team_exists($teamIdentifier)
    {
        $sql = 'SELECT team_id FROM StatsCoach.teams WHERE team_code = :id OR team_id = :id LIMIT 1';
        $stmt = Database::database()->prepare( $sql );
        $stmt->bindValue( ':id', $teamIdentifier );
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    static function newTeamMember($team_code) : bool
    {   // We can assume its the session id
        $member = self::beginTransaction( 6, $_SESSION['id'] );
        if ($team_id = Database::team_exists( $team_code ))
            Database::database()->prepare( 'INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)' )->execute( [$member, $_SESSION['id'], $team_id] );
        else PublicAlert::danger( "The team code you provided appears to be invalid. Select `Join Team` from the menu to try again." );
        return self::commit();
    }

}