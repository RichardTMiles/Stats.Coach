<?php

namespace Model;

use Modules\Helpers\QuickFetch;
use Modules\Helpers\Reporting\PublicAlert;
use Modules\Singleton;

class Golf extends QuickFetch
{
    use Singleton;
    const Singleton = true;

    public $course;
    public $distance;
    public $handicap;
    public $tournaments;
    public $tournament_teams;

    public function __construct()
    {
        QuickFetch::__construct();
        if (empty($this->user->stats)) $this->user->stats = $this->stats( $this->user->user_id );
    }

    public function golf()
    {
        if (empty($this->user->rounds))
            $this->rounds( $this->user->user_id );
    }

    public function stats($id)
    {
        return $this->fetch_as_object( 'SELECT * FROM StatsCoach.golf_stats WHERE stats_id = ?', $id );
    }

    public function rounds($id)
    {
        $stmt = $this->db->prepare( 'SELECT count(user_id) FROM StatsCoach.golf_rounds WHERE user_id = ? LIMIT 1' );
        $stmt->execute([$id]);

        $this->user->rounds[] = ($stmt->fetchColumn() ? $this->fetch_as_object(
            'SELECT round_id,course_name,creation_date,score_total,score_total_ffs,score_total_gnr,score_total_putts,par_tot 
         FROM ((StatsCoach.golf_rounds INNER JOIN StatsCoach.golf_course ON StatsCoach.golf_rounds.course_id = StatsCoach.golf_course.course_id)
         INNER JOIN StatsCoach.entity_tag ON entity_id = round_id) 
         WHERE StatsCoach.golf_rounds.user_id = ? LIMIT 3', $id) : null);
    }
    
    public function course($id)
    {
        $this->course = $this->fetch_as_object( 'SELECT * FROM StatsCoach.golf_course INNER JOIN StatsCoach.entity_location ON entity_id = course_id WHERE course_id = ?', $id );
        if (!is_object( $this->course )) throw new \Exception( 'invalid course id' );
        $this->course->course_par = unserialize( $this->course->course_par );
        $this->course->course_handicap = unserialize( $this->course->course_handicap );
    }

    public function postScore($state, $courseId, $boxColor)
    {
        // Insert into database
        if (!empty($this->newScore) && is_array( $this->newScore ))
        {
            $score_out = $score_in = $score_tot = $gnr_tot = $ffs_tot = $putts_tot = 0;
            for ($i = 0; $i < 8; $i++) $score_out += $this->newScore[$i];
            for ($i = 9; $i < 18; $i++) $score_in += $this->newScore[$i];
            $score_tot = $score_in + $score_out;

            for ($i = 0; $i < 18; $i++)
            {
                $gnr_tot += $this->gnr[$i];
                $ffs_tot += $this->ffs[$i];
                $putts_tot += $this->putts[$i];
            }

            if (!is_object( $this->course )) $this->course( $this->courseId );

            ################# Add Round ################
            $roundId = $this->new_entity( 8 );

            $sql = "INSERT INTO StatsCoach.golf_rounds (round_id, round_public, score_date, user_id, course_id, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts) VALUES (:round_id, :round_public, :score_date, :user_id, :course_id, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts)";
            $stmt = $this->db->prepare( $sql );

            $stmt->bindValue( ':round_id', $roundId );
            $stmt->bindValue( ':round_public', 1 );
            $stmt->bindValue( ':score_date', $this->roundDate );                  // TODO
            $stmt->bindValue( ':user_id', $this->user->user_id );
            $stmt->bindValue( ':course_id', $this->course->course_id );     // While pro at this TODO - we shouldn't assume that a course is serialized
            $stmt->bindValue( ':score', serialize( $this->newScore ) );
            $stmt->bindValue( ':score_gnr', serialize( $this->gnr ) );
            $stmt->bindValue( ':score_ffs', serialize( $this->ffs ) );
            $stmt->bindValue( ':score_putts', serialize( $this->putts ) );
            $stmt->bindValue( ':score_out', $score_out );
            $stmt->bindValue( ':score_in', $score_in );
            $stmt->bindValue( ':score_total', $score_tot );
            $stmt->bindValue( ':score_total_gnr', $gnr_tot );
            $stmt->bindValue( ':score_total_ffs', $ffs_tot );
            $stmt->bindValue( ':score_total_putts', $putts_tot );

            if (!$stmt->execute())
                throw new PublicAlert( "We could not process your request. Please try again.", 'warning' );

            $sql = "UPDATE StatsCoach.golf_stats SET stats_rounds = stats_rounds + 1, stats_strokes = stats_strokes + ?, stats_putts = stats_putts + ?, stats_ffs = stats_ffs + ?, stats_gnr = stats_gnr + ? WHERE stats_id = ?";
            $stmt = $this->db->prepare( $sql );

            if (!$stmt->execute( [$score_tot, $putts_tot, $ffs_tot, $gnr_tot, $this->user->user_id] ))
                throw new \Exception( 'stats update failed' );

            $this->alert['success'] = "Score successfully added!";
            startApplication( 'Home/' );


        }

       // Get Course so we can display the tee box colors
        if (!empty($courseId) && (!is_object( $this->course ) || !isset($this->course->course_id) || $this->course->course_id != $this->courseId))
            $this->course( $courseId );

        // A tee box color is set, get the distances.
        // I dont like that the tee box color is stored twice
        if (!empty($boxColor)) {
            if (!is_object( $this->distance ) || !isset( $this->distance['course_id'] ) || $this->course->course_id != $this->distance->course_id || strtolower( $this->distance->distance_color ) != $this->boxColor) {
                $this->distance = $this->fetch_as_object( 'SELECT * FROM StatsCoach.golf_distance WHERE course_id = ? AND distance_color = ? LIMIT 1', $this->course->course_id, $boxColor );
                if (!is_object( $this->distance )) throw new \Exception( 'Distance Could not be fetched' );
                $this->distance->distance = unserialize( $this->distance->distance );
            } return null;
        }

        if (!empty($courseId))
            return $this->course_colors = [$this->course->box_color_1, $this->course->box_color_2, $this->course->box_color_3, $this->course->box_color_4, $this->course->box_color_5];

        if (!empty($this->state)) {

            $sql = "SELECT course_name, course_id FROM StatsCoach.golf_course LEFT JOIN StatsCoach.entity_location ON entity_id = course_id WHERE state = ?";
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( [$state] );
            $this->courses = $stmt->fetchAll();                 // setting to global
            if (empty($this->courses)) $this->courses = true;
        }
    }

    public function addCourse($course, $handicap)
    {
        $holes = $this->holes;
        $par = $this->par;
        $tee_boxes = $this->tee_boxes;
        $teeBox = $this->teeBox;
        $handicap_number = $this->handicap_number;
        $par = $this->par;

        $par_out = 0;
        $par_in = 0;
        for ($i = 0; $i < 9; $i++) $par_out += $par[$i];
        for ($i = 9; $i < 18; $i++) $par_in += $par[$i];
        $par_tot = $par_out + $par_in;

        $this->db->beginTransaction();
        
        $course_id = $this->new_entity( 9 );
        
        $sql = "INSERT INTO StatsCoach.golf_course (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, box_color_1, box_color_2, box_color_3, box_color_4, box_color_5, course_par, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, course_handicap, pga_professional, website)
                                VALUES (:course_id, :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :box_color_1, :box_color_2, :box_color_3, :box_color_4, :box_color_5, :course_par, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :course_handicap, :pga, :site)";
        $stmt = $this->db->prepare( $sql );
        $stmt->bindValue( ':course_id', $course_id );
        $stmt->bindValue( ':course_name', $course['name'] );
        $stmt->bindValue( ':pga', $this->pga_pro  );
        $stmt->bindValue( ':site', $this->course_website  );
        $stmt->bindValue( ':course_holes', $this->holes );
        $stmt->bindValue( ':course_phone', $this->phone );
        $stmt->bindValue( ':course_difficulty', null );
        $stmt->bindValue( ':course_rank', null );
        $stmt->bindValue( ':box_color_1', isset($teeBox[1]) ? $teeBox[1][0] : null );
        $stmt->bindValue( ':box_color_2', isset($teeBox[2]) ? $teeBox[2][0] : null );
        $stmt->bindValue( ':box_color_3', isset($teeBox[3]) ? $teeBox[3][0] : null );
        $stmt->bindValue( ':box_color_4', isset($teeBox[4]) ? $teeBox[4][0] : null );
        $stmt->bindValue( ':box_color_5', isset($teeBox[5]) ? $teeBox[5][0] : null );
        $stmt->bindValue( ':course_par', serialize( $par ) );
        $stmt->bindValue( ':course_par_out', $par_out );
        $stmt->bindValue( ':course_par_in', $par_in );
        $stmt->bindValue( ':par_tot', $par_tot );
        $stmt->bindValue( ':course_par_hcp', null );
        $stmt->bindValue( ':course_type', $course['style'] );
        $stmt->bindValue( ':course_access', $course['access'] );
        $stmt->bindValue( ':course_handicap', serialize( $handicap ) );
        if (!$stmt->execute()) throw new \Exception( "Failed inserting courses" );

        $sql = "INSERT INTO StatsCoach.entity_location (entity_id, latitude, longitude, street, city, state, elevation) VALUES (:entity_id, :latitude, :longitude, :street, :city, :state, :elevation)";
        $stmt = $this->db->prepare( $sql );
        $stmt->bindValue( ':entity_id', $course_id );
        $stmt->bindValue( ':latitude', null );
        $stmt->bindValue( ':longitude', null );
        $stmt->bindValue( ':elevation', null );
        $stmt->bindValue( ':street', $course['street'] );
        $stmt->bindValue( ':city', $course['city'] );
        $stmt->bindValue( ':state', $course['state'] );
        if (!$stmt->execute()) throw new \Exception( "Failed inserting courses" );


        $sql = "INSERT INTO StatsCoach.golf_distance (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES (:course_id, :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)";
        for ($i = 1; $i <= $tee_boxes; $i++) {
            $dist_out = $dist_in = 0;
            for ($j = 1; $j <= 9; $j++) $dist_out += $teeBox[$i][$j];
            for ($j = 10; $j < 19; $j++) $dist_in += $teeBox[$i][$j];
            $dist_tot = $dist_out + $dist_in;
            $color = array_shift( $teeBox[$i] );
            $stmt = $this->db->prepare( $sql );
            $stmt->bindValue( ':course_id', $course_id );
            $stmt->bindValue( ':tee_box', $i );
            $stmt->bindValue( ':distance', serialize( $teeBox[$i] ) );
            $stmt->bindValue( ':distance_color', $color );
            $stmt->bindValue( ':distance_general_slope', $this->slope[$i][0] );
            $stmt->bindValue( ':distance_general_difficulty', $this->difficulty[$i][0] );
            $stmt->bindValue( ':distance_womens_slope', $this->slope[$i][1] );
            $stmt->bindValue( ':distance_womens_difficulty', $this->difficulty[$i][1] );
            $stmt->bindValue( ':distance_out', $dist_out );
            $stmt->bindValue( ':distance_in', $dist_in );
            $stmt->bindValue( ':distance_tot', $dist_tot );
            if (!$stmt->execute()) throw new \Exception( "Failed to insert tee box $i. Critical Error id = $course_id  Please Contact Meh. 817-789-3294" );
        }
        $this->db->commit();

        PublicAlert::success( 'The course has been added!' );
        startApplication( 'Home/' );

    }

}



