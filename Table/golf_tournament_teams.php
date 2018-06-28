<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class golf_tournament_teams extends Entities implements iRest
{
    const COLUMNS = [
            'team_id',
            'tournament_id',
            'tournament_paid',
            'tournament_accepted',
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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.golf_tournament_teams';

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
        $sql = 'INSERT INTO statscoach.golf_tournament_teams (team_id, tournament_id, tournament_paid, tournament_accepted) VALUES (:team_id, :tournament_id, :tournament_paid, :tournament_accepted)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':team_id', isset($argv['team_id']) ? $argv['team_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_id', isset($argv['tournament_id']) ? $argv['tournament_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_paid', isset($argv['tournament_paid']) ? $argv['tournament_paid'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tournament_accepted', isset($argv['tournament_accepted']) ? $argv['tournament_accepted'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.golf_tournament_teams ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['team_id'])) {
            $set .= 'team_id=:team_id,';
        }
        if (isset($argv['tournament_id'])) {
            $set .= 'tournament_id=:tournament_id,';
        }
        if (isset($argv['tournament_paid'])) {
            $set .= 'tournament_paid=:tournament_paid,';
        }
        if (isset($argv['tournament_accepted'])) {
            $set .= 'tournament_accepted=:tournament_accepted,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['team_id'])) {
            $stmt->bindValue(':team_id', $argv['team_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_id'])) {
            $stmt->bindValue(':tournament_id', $argv['tournament_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_paid'])) {
            $stmt->bindValue(':tournament_paid', $argv['tournament_paid'], \PDO::PARAM_STR);
        }
        if (isset($argv['tournament_accepted'])) {
            $stmt->bindValue(':tournament_accepted', $argv['tournament_accepted'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.golf_tournament_teams ';

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