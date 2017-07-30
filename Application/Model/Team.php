<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/21/17
 * Time: 1:24 PM
 */

namespace Model;


use Model\Helpers\DataMap;
use Modules\Helpers\Bcrypt;
use Modules\Helpers\Reporting\PublicAlert;

class Team extends DataMap
{

    protected function team_exists($team_code)
    {
        $sql = 'SELECT count(team_coach) FROM StatsCoach.teams WHERE team_id = :id OR team_code = :id';
        $stmt = $this->db->prepare( $sql );
        $stmt->bindValue( ':id', $team_code );
        $stmt->execute( );
        return $stmt->fetchColumn();
    }

    public function team($team_id)
    {
        if ($this->team_exists( $team_id ))
            $this->team[$team_id] = $this->fetch_object( 'SELECT * FROM StatsCoach.teams WHERE StatsCoach.teams.team_id = ?', $team_id );
        else
            startApplication( 'Home/' );
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
    
    public function joinTeam($teamCode)
    {
        if (!$teamId = $this->team_exists( $teamCode ))
            throw new PublicAlert( 'The team code you provided appears to be invalid.', 'warning' );

        $sql = 'SELECT COUNT(user_id) FROM StatsCoach.team_members WHERE team_id = ? AND user_id = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$teamId, $_SESSION['id']] );

        if ($stmt->fetchColumn() > 0) throw new PublicAlert( 'It appears you are already a member of this team.', 'warning' );

        $member = $this->beginTransaction( 6 );
        $sql = "INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)";
        if (!$this->db->prepare( $sql )->execute( [$member, $_SESSION['id'], $teamId] ))
            throw new PublicAlert( 'Unable to join this team. ', '' );
        $this->commit();

        PublicAlert::success( 'We successfully add you!' );
        startApplication( true );

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
        $stmt= $this->db->prepare( 'SELECT StatsCoach.team_members.team_id FROM StatsCoach.team_members WHERE StatsCoach.team_members.user_id = ? UNION SELECT StatsCoach.teams.team_id FROM StatsCoach.teams WHERE StatsCoach.teams.team_coach = ?' );
        $stmt->execute([$id,$id]);
        $stmt = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $this->user[$id]->teams = $stmt;
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
        $user = User::getInstance();
        if (!empty($team->members))
            foreach ($team->members as $user_id)
                $this->user[$user_id] = $user->user( $user_id );
    }

    
    
    
}