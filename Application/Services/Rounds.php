<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/4/17
 * Time: 11:15 PM
 */

namespace Tables;


use Modules\Entities;
use Modules\Interfaces\iEntity;

class Rounds extends Entities implements iEntity
{
    static function get(&$object, $id)
    {
        return self::fetch_classes( 'SELECT round_id,par_tot,StatsCoach.golf_rounds.course_id,course_name,round_public,score_date,score_total,score_total_ffs,score_total_gnr,score_total_putts FROM StatsCoach.golf_rounds LEFT JOIN StatsCoach.golf_course ON StatsCoach.golf_rounds.course_id = StatsCoach.golf_course.course_id WHERE StatsCoach.golf_rounds.user_id = ? LIMIT 5', $id);
    }

    static function all(&$object, $id)
    {
        return self::fetch_classes( 'SELECT round_id,par_tot,StatsCoach.golf_rounds.course_id,course_name,round_public,score_date,score_total,score_total_ffs,score_total_gnr,score_total_putts FROM StatsCoach.golf_rounds LEFT JOIN StatsCoach.golf_course ON StatsCoach.golf_rounds.course_id = StatsCoach.golf_course.course_id WHERE StatsCoach.golf_rounds.user_id = ?', $id );
    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)
    {
        ################# Add Round ################
        $roundId = self::beginTransaction( Entities::GOLF_ROUNDS, $_SESSION['id']);
        $sql = "INSERT INTO StatsCoach.golf_rounds (round_id, round_public, score_date, user_id, course_id, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts) VALUES (:round_id, :round_public, :score_date, :user_id, :course_id, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts)";
        $stmt = self::database()->prepare( $sql );

        $stmt->bindValue( ':round_id',      $roundId );
        $stmt->bindValue( ':user_id',       $_SESSION['id'] );
        $stmt->bindValue( ':score_date',    $argv['roundDate'] );                  // TODO
        $stmt->bindValue( ':round_public',  1 );
        $stmt->bindValue( ':course_id',     $id );     // While pro at this TODO - we shouldn't assume that a course is serialized
        $stmt->bindValue( ':score',         serialize( $argv['newScore'] ) );
        $stmt->bindValue( ':score_gnr',     serialize( $argv['gnr'] ) );
        $stmt->bindValue( ':score_ffs',     serialize( $argv['ffs'] ) );
        $stmt->bindValue( ':score_putts',   serialize( $argv['putts'] ) );
        $stmt->bindValue( ':score_out',     $argv['score_out'] );
        $stmt->bindValue( ':score_in',      $argv['score_in'] );
        $stmt->bindValue( ':score_total',   $argv['score_tot'] );
        $stmt->bindValue( ':score_total_gnr', $argv['gnr_tot'] );
        $stmt->bindValue( ':score_total_ffs', $argv['ffs_tot'] );
        $stmt->bindValue( ':score_total_putts', $argv['putts_tot'] );

        return ($stmt->execute() ? self::commit() : self::verify());

    }

    static function remove(&$object, $id)
    {

    }
}