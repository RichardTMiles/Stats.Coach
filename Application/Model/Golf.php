<?php

namespace Model;

use Modules\Singleton;
use Model\Helpers\iSport;
use Model\Helpers\DataMap;
use Modules\Helpers\Reporting\PublicAlert;

class Golf extends DataMap implements iSport
{
    use Singleton;

    public function golf()
    {
        if (empty($this->user[$_SESSION['id']]->rounds))
            $this->rounds( $_SESSION['id'] );
    }

    public function stats($id)
    {
        $this->user[$id]->stats = $this->fetch_object( 'SELECT * FROM StatsCoach.golf_stats WHERE stats_id = ? LIMIT 1', $id );
        $this->rounds( $id );
    }

    public function rounds($id)
    {
        $stmt = $this->db->prepare( 'SELECT count(user_id) FROM StatsCoach.golf_rounds WHERE user_id = ?' );
        $stmt->execute([$id]);
        $this->user[$id]->rounds = ($stmt->fetchColumn() ? $this->fetch_classes( 'SELECT round_id,par_tot,StatsCoach.golf_rounds.course_id,course_name,round_public,score_date,score_total,score_total_ffs,score_total_gnr,score_total_putts FROM StatsCoach.golf_rounds LEFT JOIN StatsCoach.golf_course ON StatsCoach.golf_rounds.course_id = StatsCoach.golf_course.course_id WHERE StatsCoach.golf_rounds.user_id = ? LIMIT 5', $id) : []);
    }
    
    public function course($id)
    {
        $this->course[$id] = $this->fetch_object( 'SELECT * FROM StatsCoach.golf_course JOIN StatsCoach.entity_location ON entity_id = course_id WHERE course_id = ? LIMIT 1', $id );
        if (!is_object( $course = $this->course[$id] )) throw new \Exception( 'invalid course id' );
        $course->course_par = unserialize( $course->course_par );
        $course->course_handicap = unserialize( $course->course_handicap );
        return $course;
    }

    public function teeBox($id, $color)
    {
        if (!is_object($this->course[$id])) throw new \Exception('invalid distance lookup');
        $sql = "SELECT * FROM StatsCoach.golf_tee_box WHERE course_id = ? AND distance_color = ? LIMIT 1";
        $teeBox = $this->course[$id]->teeBox = $this->fetch_object( $sql, $id, $color);
        $teeBox->distance = unserialize( $teeBox->distance );
        $this->course[$id]->teeBox->distance_color = $color;
        return $teeBox;
    }

    public function postScore($state, $course_id, $boxColor)
    {
        global $course_colors, $courses;
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

            if (!isset($this->course[$course_id]) || !is_object( $this->course[$course_id] ))
                $course = $this->course( $course_id );

            ################# Add Round ################
            $roundId = $this->new_entity( 8 );

            $sql = "INSERT INTO StatsCoach.golf_rounds (round_id, round_public, score_date, user_id, course_id, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts) VALUES (:round_id, :round_public, :score_date, :user_id, :course_id, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts)";
            $stmt = $this->db->prepare( $sql );

            $stmt->bindValue( ':round_id', $roundId );
            $stmt->bindValue( ':user_id', $_SESSION['id'] );
            $stmt->bindValue( ':score_date', $this->roundDate );                  // TODO
            $stmt->bindValue( ':round_public', 1 );
            $stmt->bindValue( ':course_id', $course->course_id );     // While pro at this TODO - we shouldn't assume that a course is serialized
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

            if (!$stmt->execute()) throw new PublicAlert( "We could not process your request. Please try again.", 'warning' );

            $sql = "UPDATE StatsCoach.golf_stats SET stats_rounds = stats_rounds + 1, stats_strokes = stats_strokes + ?, stats_putts = stats_putts + ?, stats_ffs = stats_ffs + ?, stats_gnr = stats_gnr + ? WHERE stats_id = ?";
            $stmt = $this->db->prepare( $sql );

            if (!$stmt->execute( [$score_tot, $putts_tot, $ffs_tot, $gnr_tot, $_SESSION['id']] ))
                throw new \Exception( 'stats update failed' );

            PublicAlert::success("Score successfully added!");
            startApplication( 'Home/' );
        }
        
       // Get Course so we can display the tee box colors
        if (!empty($course_id) && (is_array($this->course) && !array_key_exists( $course_id, $this->course) || !is_object( $course = $this->course[$course_id] ) || !isset($course->course_id) || $course->course_id != $course_id))
            $course = $this->course( $course_id );

        // A tee box color is set, get the distances.
        // I dont like that the tee box color is stored twice
        if (!empty($boxColor)) {
            if (!isset($course->teeBox) || !is_object( $course->teeBox )) {
                $this->teeBox( $course_id, $boxColor );
            } return null;
        }

        if (!empty($course_id) && is_object( $course ))
            return $course_colors = [$course->box_color_1, $course->box_color_2, $course->box_color_3, $course->box_color_4, $course->box_color_5];

        if (!empty($state)) {
            $sql = "SELECT course_name, course_id FROM StatsCoach.golf_course LEFT JOIN StatsCoach.entity_location ON entity_id = course_id WHERE state = ?";
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( [$state] );
            $courses = $stmt->fetchAll();                 // setting to global
            if (empty($courses)) $courses = true;
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


        $sql = "INSERT INTO StatsCoach.golf_tee_box (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES (:course_id, :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)";
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



