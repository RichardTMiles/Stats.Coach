<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/21/17
 * Time: 1:24 PM
 */

namespace Model\Helpers;


use Modules\Helpers\DataFetch;

abstract class Team extends DataFetch
{
    protected function team($team_id)
    {
        $this->team[$team_id] = $this->fetch_object( 'SELECT * FROM StatsCoach.teams WHERE StatsCoach.teams.team_id = ?', $team_id );
    }

    protected function createTeam($teamName, $schoolName = null)
    {
        $key = $this->beginTransaction( 5 );
        $sql = "INSERT INTO StatsCoach.teams (team_id, team_name, team_school, team_coach, team_code) VALUES (?,?,?,?,?)";
        if (!$this->db->prepare( $sql )->execute( [$key, $teamName, $schoolName, $_SESSION['id'], Bcrypt::genRandomHex( 20 )] ))
            throw new PublicAlert( 'Sorry, we we\'re unable to create your team at this time.' );
        $this->commit();
        PublicAlert::success( "We successfully created `$teamName`!" );
    }

    protected function newTeamMember($team_code)
    {
        $member = $this->beginTransaction( 6 );
        if ($teamId = $this->team_exists( $team_code ))
            $this->db->prepare( 'INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)' )->execute( [$member, $_SESSION['id'], $team_id] );
        else PublicAlert::danger( "The team code you provided appears to be invalid. Select `Join Team` from the menu to try again." );
        $this->commit();
    }

    protected function userTeams($id)
    {
        $stmt= $this->db->prepare( 'SELECT team_id FROM StatsCoach.team_members WHERE user_id = ?' );
        $stmt->execute([$id]);
        $this->user[$id]->teams = is_array($stmt = $stmt->fetchAll(\PDO::FETCH_COLUMN)) ? $stmt : [];
        $stmt = $this->db->prepare( 'SELECT team_id FROM StatsCoach.teams WHERE team_coach = ?' );
        $stmt->execute([$id]);
        $stmt = is_array($stmt = $stmt->fetchAll(\PDO::FETCH_COLUMN)) ? $stmt : [];
        $this->user[$id]->teams = array_merge($this->user[$id]->teams, $stmt);
    }

    protected function teamMembers($id)
    {
        if (!isset($this->team[$id]))
            throw new \InvalidArgumentException("No team[ $id ] found in array");
        $team = $this->team[$id];

        $sql = 'SELECT user_id FROM StatsCoach.team_members WHERE team_id = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([$id]);
        $team->members = is_array( $stmt = $stmt->fetchAll()) ? $stmt : [];

        if (!empty($team->members))
            foreach ($team->members as $user_id)
                $this->user[$user_id] = $this->user( $user_id );
    }



}