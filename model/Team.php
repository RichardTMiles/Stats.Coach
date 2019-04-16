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
use Tables\carbon_team_members as TeamMembers;
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
    public function team(string $team_id)
    {
        global $json;

        $this->team[$team_id] = [];


        $json['myTeam'] = &$this->team[$team_id];

        if (!Teams::get($this->team[$team_id], $team_id, [])
            || empty($this->team[$team_id])) {
            PublicAlert::warning('Failed to lookup team id.');
            return startApplication('Home/');
        }

        $json['imTheCoach'] = $_SESSION['id'] === $json['myTeam']['team_coach'];

        if (!$json['imTheCoach']) {
            $id = $json['myTeam']['team_coach'];
            $json['myTeam']['team_coach'] = [];
            if (!Users::Get($json['myTeam']['team_coach'], $id, [])
                || empty($json['myTeam']['team_coach'])) {
                PublicAlert::danger('Failed to lookup team coach.');
                return startApplication('Home/');
            }
        }

        $json['myTeam']['members'] = self::fetch('select user_first_name, user_last_name, HEX(carbon_users.user_id) as user_id, 
       user_profile_pic, user_cover_photo, stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_ffs, stats_gnr, stats_putts
from carbon_users join carbon_team_members join carbon_teams join carbon_user_golf_stats
                                  where carbon_teams.team_id = carbon_team_members.team_id 
                                    and carbon_user_golf_stats.stats_id = carbon_users.user_id
                                    and carbon_users.user_id = carbon_team_members.user_id
                                    and carbon_teams.team_id = unhex(?)', $team_id);

        $json['myTeam']['memberCount'] = count($json['myTeam']['members']);

        $json['rounds'] = 2;

        // sortDump($json);


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
            'team_school' => $schoolName,
            'team_coach' => $_SESSION['id'],
            'team_code' => Bcrypt::genRandomHex(20)
        ])) {
            throw new PublicAlert('Sorry, we we\'re unable to create your team at this time.');
        }

        $return = [];

        if (!Users::Put($return, $_SESSION['id'], [
            'user_type' => 'Coach'
        ])) {
            throw new PublicAlert('Sorry, we we\'re unable to create your team at this time.');
        }

        self::commit();

        PublicAlert::success("We successfully created `$teamName`!");

        return startApplication(true);
    }

    /**
     * @param $teamCode
     * @throws PublicAlert
     */
    public function joinTeam($teamCode)
    {
        $team = [];

        if (!Teams::get($team, null, [
            'where' => [
                'team_code' => $teamCode
            ],
            'pagination' => [
                'limit' => 1
            ]
        ])) {
            throw new PublicAlert('We failed to lookup the team. Please try again later.', 'warning');
        }

        if (empty($team)) {
            throw new PublicAlert('The team code you provided appears to be invalid.', 'warning');
        }

        $member = [];

        if (!TeamMembers::get($member, null, [
            'where' => [
                'team_id' => $team['team_id'],
                'user_id' => $_SESSION['id'],
                'accepted' => 0
            ]
        ])) {
            throw new PublicAlert('We failed to lookup the membership history. Please try again later.', 'danger');
        }

        if (!empty($member)) {
            throw new PublicAlert('It appears you are already a member of this team.', 'warning');
        }

        if (!TeamMembers::Post([
            'user_id' => $_SESSION['id'],
            'team_id' => $team['team_id']
        ])) {
            throw new PublicAlert('Failed to update team status. Please try again later.', 'danger');
        }

        self::commit();

        PublicAlert::success('We successfully add you!');

        return startApplication(true);
    }
}