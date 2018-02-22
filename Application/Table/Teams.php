<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 9/4/17
 * Time: 12:45 AM
 */

namespace Table;

use Carbon\Database;
use Carbon\Interfaces\iTable;
use Model\User;
use Carbon\Error\PublicAlert;
use Carbon\Helpers\Bcrypt;
use \Carbon\Entities;


class Teams extends Entities implements iTable
{


    /**
     * @param $array - values received will be placed in this array
     * @param $id - the rows primary key
     * @return bool
     */
    public static function All(array &$array, string $id): bool
    {
        global $team; // array, referenced for static function

        $temp = self::fetch('SELECT carbon_teams.team_id, team_coach, parent_team, team_code, team_name, 
          team_rank, team_sport, team_division, team_school, team_district, team_membership, team_photo FROM StatsCoach.carbon_teams LEFT JOIN StatsCoach.team_members ON StatsCoach.carbon_teams.team_coach = ? OR member_id = ?', $id, $id);

        if (array_key_exists('team_id', $temp)) {
            $temp = [$temp];
        }

        foreach ($temp as $key => $value) {
            $team[$value['team_id']] = $value;
            $array['teams'][] = $value['team_id'];
            self::members($team[$value['team_id']], $value['team_id']);
            Photos::All($team[$value['team_id']], $value['team_id']);
        }

        return true;
    }        // Get all data from a table given its primary key

    /**
     * @param $array - should be set to null on success
     * @param $id - the rows primary key
     * @return bool
     */
    public static function Delete(array &$array, string $id): bool
    {
        return true;
    }    // Delete all data from a table given its primary key

    /**
     * @param $array - values received will be placed in this array
     * @param $id - the rows primary key
     * @param $argv - column names desired to be in our array
     * @return bool
     */

    public static function Get(array &$array, string $id, array $argv): bool
    {
        $team = self::fetch('SELECT * FROM StatsCoach.carbon_teams WHERE StatsCoach.carbon_teams.team_id = ?', $id);

        Photos::All($team, $id);

        self::members($team, $id);

        return true;

    }   // Get table columns given in argv (usually an array) and place them into our array

    /**
     * @param $array - The array we are trying to insert
     * @return bool
     * @throws \Carbon\Error\PublicAlert
     */
    public static function Post(array $array): bool
    {
        $key = self::beginTransaction(TEAMS, $_SESSION['id']);

        $sql = 'INSERT INTO StatsCoach.carbon_teams (team_id, team_name, team_school, team_coach, team_code) VALUES (?,?,?,?,?)';


        if (!static::execute($sql, $key, $array['teamName'], $array['schoolName'], $_SESSION['id'], Bcrypt::genRandomHex(20))) {
            throw new PublicAlert('Sorry, we we\'re unable to create your team at this time.');
        }
        self::commit();

        PublicAlert::success("We successfully created `$teamName`!");

        return true;
    }

    /**
     * @param $array - on success, fields updated will be
     * @param $id - the rows primary key
     * @param $argv - an associative array of Column => Value pairs
     * @return bool  - true on success false on failure
     */
    public static function Put(array &$array, string $id, array $argv): bool
    {

        return true;
    }


    public static function members(&$team, $id)  // Team obj
    {
        global $user;

        $sql = 'SELECT user_id FROM StatsCoach.team_members WHERE team_id = ?';
        $stmt = Database::database()->prepare($sql);
        $stmt->execute([$id]);
        $team['members'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($team['members'])) {
            foreach ($team['members'] as $user_id) {
                if (!\is_array($user) || !array_key_exists($user_id, $user)) {    // cache
                    new User($user_id);
                }
            }
        }
        return $team;
    }

    public static function team_exists($teamIdentifier)
    {
        $sql = 'SELECT team_id FROM StatsCoach.carbon_teams WHERE team_code = :id OR team_id = :id LIMIT 1';
        $stmt = Database::database()->prepare($sql);
        $stmt->bindValue(':id', $teamIdentifier);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function newTeamMember($team_code): bool
    {   // We can assume its the session id
        $member = self::beginTransaction(6, $_SESSION['id']);
        if ($team_id = Database::team_exists($team_code)) {
            Database::database()->prepare('INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)')->execute([$member, $_SESSION['id'], $team_id]);
        } else {
            PublicAlert::danger('The team code you provided appears to be invalid. Select `Join Team` from the menu to try again.');
        }
        return self::commit();
    }

}