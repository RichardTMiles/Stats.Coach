<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/21/17
 * Time: 1:24 PM
 */

namespace Model;


use Model\Helpers\GlobalMap;
use Modules\Helpers\Bcrypt;
use Modules\Helpers\Entities\Photos;
use Modules\Error\PublicAlert;
use Modules\Singleton;

class Team extends GlobalMap
{
    use Singleton;

    protected function team_exists($teamIdentifier)
    {
        $sql = 'SELECT team_id FROM StatsCoach.teams WHERE team_code = :id OR team_id = :id LIMIT 1';
        $stmt = $this->db->prepare( $sql );
        $stmt->bindValue( ':id', $teamIdentifier );
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function team(string $teamIdentifier)
    {
        global $team_id, $team_photo;

        if (!$team_id = $this->team_exists( $teamIdentifier ))
            return startApplication( 'Home/' );

        $team = $this->team[$team_id] = $this->fetch_object( 'SELECT * FROM StatsCoach.teams LEFT JOIN StatsCoach.entity_photos ON parent_id = team_id WHERE StatsCoach.teams.team_id = ?', $team_id );
        
        if ($team_photo && $team->team_coach == $_SESSION['id']) {
            alert('change photo');
            unlink( $team->photo_path );
            Photos::add( $team, $team_id, [
                'photo_path'=>"$team_photo",
                'photo_description'=>""] );
        }

        $team->photo = SITE . ($team->photo_path ?? 'Data/Uploads/Pictures/Defaults/team-icon.png');
        $this->teamMembers( $team_id );
        if (isset($this->user[$team->team_coach])) return null;
        $user = User::getInstance();
        $user->basicUser( $team->team_coach );

        return $team;
    }

    protected function createTeam($teamName, $schoolName = null)
    {
        $key = $this->beginTransaction( 5, $_SESSION['id'] );
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

        $this->team( $teamId );

        if (array_key_exists( $teamCode, $this->user[$_SESSION['id']]->teams ))
            throw new PublicAlert( 'It appears you are already a member of this team.', 'warning' );

        $member = $this->beginTransaction( 6, $_SESSION['id'] );
        $sql = "INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)";
        if (!$this->db->prepare( $sql )->execute( [$member, $_SESSION['id'], $teamId] ))
            throw new PublicAlert( 'Unable to join this team. ', '' );
        $this->commit();

        PublicAlert::success( 'We successfully add you!' );
        startApplication( true );

    }

    protected function newTeamMember($team_code)
    {
        $member = $this->beginTransaction( 6, $_SESSION['id'] );
        if ($team_id = $this->team_exists( $team_code ))
            $this->db->prepare( 'INSERT INTO StatsCoach.team_members (member_id, user_id, team_id) VALUES (?,?,?)' )->execute( [$member, $_SESSION['id'], $team_id] );
        else PublicAlert::danger( "The team code you provided appears to be invalid. Select `Join Team` from the menu to try again." );
        $this->commit();
    }

    protected function userTeams($id)
    {
        $stmt = $this->db->prepare( 'SELECT StatsCoach.team_members.team_id FROM StatsCoach.team_members WHERE StatsCoach.team_members.user_id = :id UNION SELECT StatsCoach.teams.team_id FROM StatsCoach.teams WHERE StatsCoach.teams.team_coach = :id' );
        $stmt->bindValue( ':id', $id );
        $stmt->execute();
        $stmt = $stmt->fetchAll( \PDO::FETCH_COLUMN );
        $this->user[$id]->teams = $stmt;
    }

    protected function teamMembers($id)
    {
        $team = $this->team[$id];
        $sql = 'SELECT user_id FROM StatsCoach.team_members WHERE team_id = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$id] );
        $team->members = is_array( $stmt = $stmt->fetchAll( \PDO::FETCH_COLUMN ) ) ? $stmt : [];
        $user = User::getInstance();
        if (!empty($team->members))
            foreach ($team->members as $user_id)
                if (!isset($this->user[$user_id]))
                    $user->basicUser( $user_id );
    }


}