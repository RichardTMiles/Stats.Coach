<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/5/17
 * Time: 9:21 AM
 */

namespace Table;

use Carbon\Database;
use Carbon\Entities;

class Course extends Entities
{
    static function get(&$array, $id)
    {

    }

    static function all(&$object, $id)
    {

    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)
    {
        $course_id = self::beginTransaction( GOLF_COURSE );

        $sql = "INSERT INTO StatsCoach.golf_course (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, box_color_1, box_color_2, box_color_3, box_color_4, box_color_5, course_par, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, course_handicap, pga_professional, website)
                                VALUES (:course_id, :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :box_color_1, :box_color_2, :box_color_3, :box_color_4, :box_color_5, :course_par, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :course_handicap, :pga, :site)";
        $stmt = Database::database()->prepare( $sql );
        $stmt->bindValue( ':course_id',     $course_id );
        $stmt->bindValue( ':course_name',   $argv['course']['name'] );
        $stmt->bindValue( ':pga',           $argv['pga_pro'] );
        $stmt->bindValue( ':site',          $argv['course_website'] );
        $stmt->bindValue( ':course_holes',  $argv['holes'] );
        $stmt->bindValue( ':course_phone',  $argv['phone'] );
        $stmt->bindValue( ':course_difficulty', null );
        $stmt->bindValue( ':course_rank',   null );
        $stmt->bindValue( ':box_color_1',   $argv['teeBox'][1][0] ?? null );
        $stmt->bindValue( ':box_color_2',   $argv['teeBox'][2][0] ?? null );
        $stmt->bindValue( ':box_color_3',   $argv['teeBox'][3][0] ?? null );
        $stmt->bindValue( ':box_color_4',   $argv['teeBox'][4][0] ?? null );
        $stmt->bindValue( ':box_color_5',   $argv['teeBox'][5][0] ?? null );
        $stmt->bindValue( ':course_par',    serialize( $argv['par'] ) );
        $stmt->bindValue( ':course_par_out',$argv['par_out'] );
        $stmt->bindValue( ':course_par_in', $argv['par_in'] );
        $stmt->bindValue( ':par_tot',       $argv['par_tot'] );
        $stmt->bindValue( ':course_par_hcp', null );
        $stmt->bindValue( ':course_type',   $argv['course']['style'] );
        $stmt->bindValue( ':course_access', $argv['course']['access'] );
        $stmt->bindValue( ':course_handicap', serialize( $argv['handicap'] ) );
        if (!$stmt->execute()) throw new \Exception( 'Failed inserting courses' );

        $null = null;

        if (!Locations::Post([
            'id' => $course_id,
            'street' => $argv['course']['street'],
            'city' => $argv['course']['city'],
            'state' => $argv['course']['state']
        ])) throw new \Exception( 'Failed inserting courses' );


        $sql = "INSERT INTO StatsCoach.golf_tee_box (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES (:course_id, :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)";
        for ($i = 1; $i <= $argv['tee_boxes']; $i++) {
            $dist_out = $dist_in = 0;

            for ($j = 1; $j <= 9; $j++)
                $dist_out += $argv['teeBox'][$i][$j];

            for ($j = 10; $j < 19; $j++)
                $dist_in += $argv['teeBox'][$i][$j];

            $dist_tot = $dist_out + $dist_in;
            $color = array_shift( $argv['teeBox'][$i] );
            $stmt = Database::database()->prepare( $sql );
            $stmt->bindValue( ':course_id', $course_id );
            $stmt->bindValue( ':tee_box', $i );
            $stmt->bindValue( ':distance', serialize( $argv['teeBox'][$i] ) );
            $stmt->bindValue( ':distance_color', $color );
            $stmt->bindValue( ':distance_general_slope',        $argv['slope'][$i][0] ?? null);
            $stmt->bindValue( ':distance_general_difficulty',   $argv['difficulty'][$i][0] ?? null);
            $stmt->bindValue( ':distance_womens_slope',         $argv['slope'][$i][1] ?? null);
            $stmt->bindValue( ':distance_womens_difficulty',    $argv['difficulty'][$i][1] ?? null );
            $stmt->bindValue( ':distance_out', $dist_out );
            $stmt->bindValue( ':distance_in', $dist_in );
            $stmt->bindValue( ':distance_tot', $dist_tot );
            if (!$stmt->execute()) throw new \Exception( "Failed to insert tee box $i. Critical Error id = $course_id  Please Contact Meh. 817-789-3294" );
        }
        return self::commit();
    }

    static function remove(&$object, $id){

    }

}