<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class team_members extends Entities implements iRest
{
    const PRIMARY = "team_id";

    const COLUMNS = [
    'member_id','team_id','user_id','accepted',
    ];

    const BINARY = [
    'member_id','team_id','user_id',
    ];

    /**
     * @param array $return
     * @param string|null $primary
     * @param array $argv
     * @return bool
     */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        if (isset($argv['limit'])){
            if ($argv['limit'] !== '') {
                $pos = strrpos($argv['limit'], "><");
                if ($pos !== false) { // note: three equal signs
                    substr_replace($argv['limit'],',',$pos, 2);
                }
                $limit = ' LIMIT ' . $argv['limit'];
            } else {
                $limit = '';
            }
        } else {
            $limit = ' LIMIT 100';
        }

        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $sql = '';
        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
            } else {
                $sql .= $column;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.team_members';

        $pdo = Database::database();

        if ($primary === null) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                        } else {
                            if (in_array($column, self::BINARY)) {
                                $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                            } else {
                                $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                            }
                        }
                    }
                    return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
        } else if (!empty(self::PRIMARY)){
            $sql .= ' WHERE ' . self::PRIMARY . '=UNHEX(' . $pdo->quote($primary) . ')';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.team_members (member_id, team_id, user_id, accepted) VALUES ( :member_id, :team_id, :user_id, :accepted)';
        $stmt = Database::database()->prepare($sql);
            
                $member_id = isset($argv['member_id']) ? $argv['member_id'] : null;
                $stmt->bindParam(':member_id',$member_id, \PDO::PARAM_STR, 16);
                    $team_id = $id = self::new_entity('team_members');
            $stmt->bindParam(':team_id',$team_id, \PDO::PARAM_STR, 16);
            
                $user_id = isset($argv['user_id']) ? $argv['user_id'] : null;
                $stmt->bindParam(':user_id',$user_id, \PDO::PARAM_STR, 16);
                    
                $accepted = isset($argv['accepted']) ? $argv['accepted'] : '0';
                $stmt->bindParam(':accepted',$accepted, \PDO::PARAM_NULL, 1);
        
        return $stmt->execute() ? $id : false;

    }

    /**
    * @param array $return
    * @param string $id
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $id, array $argv) : bool
    {
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.team_members ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['member_id'])) {
            $set .= 'member_id=UNHEX(:member_id),';
        }
        if (isset($argv['team_id'])) {
            $set .= 'team_id=UNHEX(:team_id),';
        }
        if (isset($argv['user_id'])) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (isset($argv['accepted'])) {
            $set .= 'accepted=:accepted,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['member_id'])) {
            $member_id = 'UNHEX('.$argv['member_id'].')';
            $stmt->bindParam(':member_id', $member_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['team_id'])) {
            $team_id = 'UNHEX('.$argv['team_id'].')';
            $stmt->bindParam(':team_id', $team_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['user_id'])) {
            $user_id = 'UNHEX('.$argv['user_id'].')';
            $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['accepted'])) {
            $accepted = $argv['accepted'];
            $stmt->bindParam(':accepted',$accepted, \PDO::PARAM_NULL, 1 );
        }

        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        $sql = 'DELETE FROM statscoach.team_members ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if ($primary === null) {
            /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                return false;
            }
            $sql .= ' WHERE ';
            foreach ($argv as $column => $value) {
                if (in_array($column, self::BINARY)) {
                    $sql .= " $column =UNHEX(" . Database::database()->quote($value) . ') AND ';
                } else {
                    $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)) {
            $sql .= ' WHERE ' . self::PRIMARY . '=UNHEX(' . Database::database()->quote($primary) . ')';
        }

        $remove = null;

        return self::execute($sql);
    }
}