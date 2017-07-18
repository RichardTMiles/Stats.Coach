<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 */

namespace Modules\Helpers;

use Modules\Database;

abstract class QuickFetch
{
    const USER = 0;
    const USER_FOLLOWERS = 1;
    const USER_MESSAGES = 3;
    const USER_TASKS = 4;
    const TEAMS = 5;
    const TEAM_MEMBERS = 6;
    const GOLF_TOURNAMENTS = 7;
    const GOLF_ROUNDS = 8;
    const GOLF_COURSE = 9;
    const ENTITY_COMMENTS = 10;
    const ENTITY_PHOTOS = 11;

    protected $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function new_entity($tag_id)
    {
        do { try {
                $stmt = $this->db->prepare( 'INSERT INTO StatsCoach.entity (entity_pk) VALUE (?)' );
                $stmt->execute( [$key = Bcrypt::genRandomHex()] );
            } catch (\PDOException $e) {
                $stmt = false;
            }
        } while (!$stmt);
        $this->db->prepare( 'INSERT INTO StatsCoach.entity_tag (entity_id, user_id, tag_id, creation_date) VALUES (?,?,?,?)' )->execute([$key, (isset($_SESSION['id']) ? $_SESSION['id'] : $key), $tag_id, time()]);
        return $key;
    }

    public function fetch_into_current_class($array)
    {
        $object = get_object_vars( $this );
        foreach ($array as $key => $value)
            if (array_key_exists( $key, $object ))
                $this->$key = $value;
    }

    public function fetch_as_object($sql, ... $execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, \stdClass::class );
        if (!$stmt->execute( $execute )) return false;
        $stmt = $stmt->fetchAll();  // user obj
        return (is_array( $stmt ) && count( $stmt ) == 1 ? $stmt[0] : (is_array( $stmt ) ? $stmt : false));
    }

    public function fetch_as_array_object()
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Skeleton::class );
        $stmt->execute( $execute );
        return $stmt->fetchAll();  // user obj
    }

    public function fetch_to_global($sql, $execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Carbon::class );
        $stmt->execute( $execute );
        return $stmt->fetchAll();  // user obj

    }
}