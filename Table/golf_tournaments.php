<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class golf_tournaments extends Entities implements iRest
{
    const COLUMNS = [
            'tournament_id',
            'tournament_name',
            'host_name',
            'tournament_style',
            'tournament_team_price',
            'tournament_paid',
            'course_id',
            'tournament_date',
    ];

    const PRIMARY = "";

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

        $get = $where = [];
        foreach ($argv as $column => $value) {
            if (!is_int($column) && in_array($column, self::COLUMNS)) {
                if ($value !== '') {
                    $where[$column] = $value;
                } else {
                    $get[] = $column;
                }
            } elseif (in_array($value, self::COLUMNS)) {
                $get[] = $value;
            }
        }

        $get =  !empty($get) ? implode(", ", $get) : ' * ';

        $sql = 'SELECT ' .  $get . ' FROM statscoach.golf_tournaments';

        if ($primary === null) {
            $sql .= ' WHERE ';
            foreach ($where as $column => $value) {
                $sql .= "($column = " . Database::database()->quote($value) . ') AND ';
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)){
            $sql .= ' WHERE ' . self::PRIMARY . '=' . Database::database()->quote($primary);
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
        $sql = 'INSERT INTO statscoach.golf_tournaments (tournament_id, tournament_name, host_name, tournament_style, tournament_team_price, tournament_paid, course_id, tournament_date) VALUES (:tournament_id, :tournament_name, :host_name, :tournament_style, :tournament_team_price, :tournament_paid, :course_id, :tournament_date)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':tournament_id', isset($argv['tournament_id']) ? $argv['tournament_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_name', isset($argv['tournament_name']) ? $argv['tournament_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':host_name', isset($argv['host_name']) ? $argv['host_name'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_style', isset($argv['tournament_style']) ? $argv['tournament_style'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_team_price', isset($argv['tournament_team_price']) ? $argv['tournament_team_price'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_paid', isset($argv['tournament_paid']) ? $argv['tournament_paid'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_id', isset($argv['course_id']) ? $argv['course_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_date', isset($argv['tournament_date']) ? $argv['tournament_date'] : null, \PDO::PARAM_STR);
        return $stmt->execute();
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

        $sql = 'UPDATE statscoach.golf_tournaments ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['tournament_id'])) {
            $set .= 'tournament_id=:tournament_id,';
        }
        if (isset($argv['tournament_name'])) {
            $set .= 'tournament_name=:tournament_name,';
        }
        if (isset($argv['host_name'])) {
            $set .= 'host_name=:host_name,';
        }
        if (isset($argv['tournament_style'])) {
            $set .= 'tournament_style=:tournament_style,';
        }
        if (isset($argv['tournament_team_price'])) {
            $set .= 'tournament_team_price=:tournament_team_price,';
        }
        if (isset($argv['tournament_paid'])) {
            $set .= 'tournament_paid=:tournament_paid,';
        }
        if (isset($argv['course_id'])) {
            $set .= 'course_id=:course_id,';
        }
        if (isset($argv['tournament_date'])) {
            $set .= 'tournament_date=:tournament_date,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['tournament_id'])) {
            $stmt->bindValue(':tournament_id', $argv['tournament_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_name'])) {
            $stmt->bindValue(':tournament_name', $argv['tournament_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['host_name'])) {
            $stmt->bindValue(':host_name', $argv['host_name'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_style'])) {
            $stmt->bindValue(':tournament_style', $argv['tournament_style'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_team_price'])) {
            $stmt->bindValue(':tournament_team_price', $argv['tournament_team_price'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_paid'])) {
            $stmt->bindValue(':tournament_paid', $argv['tournament_paid'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_id'])) {
            $stmt->bindValue(':course_id', $argv['course_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_date'])) {
            $stmt->bindValue(':tournament_date', $argv['tournament_date'], \PDO::PARAM_STR);
        }


        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        $sql = 'DELETE FROM statscoach.golf_tournaments ';

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
                $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)) {
            $sql .= ' WHERE ' . self::PRIMARY . '=' . Database::database()->quote($primary);
        }

        $remove = null;

        return self::execute($sql);
    }

}