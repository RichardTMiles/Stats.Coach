<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class golf_tee_box extends Entities implements iRest
{
    const COLUMNS = [
            'course_id',
            'tee_box',
            'distance',
            'distance_color',
            'distance_general_slope',
            'distance_general_difficulty',
            'distance_womens_slope',
            'distance_womens_difficulty',
            'distance_out',
            'distance_in',
            'distance_tot',
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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.golf_tee_box';

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
        $sql = 'INSERT INTO statscoach.golf_tee_box (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES (:course_id, :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':course_id', isset($argv['course_id']) ? $argv['course_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':tee_box', isset($argv['tee_box']) ? $argv['tee_box'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance', isset($argv['distance']) ? $argv['distance'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_color', isset($argv['distance_color']) ? $argv['distance_color'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_general_slope', isset($argv['distance_general_slope']) ? $argv['distance_general_slope'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_general_difficulty', isset($argv['distance_general_difficulty']) ? $argv['distance_general_difficulty'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_womens_slope', isset($argv['distance_womens_slope']) ? $argv['distance_womens_slope'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_womens_difficulty', isset($argv['distance_womens_difficulty']) ? $argv['distance_womens_difficulty'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_out', isset($argv['distance_out']) ? $argv['distance_out'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_in', isset($argv['distance_in']) ? $argv['distance_in'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':distance_tot', isset($argv['distance_tot']) ? $argv['distance_tot'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.golf_tee_box ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['course_id'])) {
            $set .= 'course_id=:course_id,';
        }
        if (isset($argv['tee_box'])) {
            $set .= 'tee_box=:tee_box,';
        }
        if (isset($argv['distance'])) {
            $set .= 'distance=:distance,';
        }
        if (isset($argv['distance_color'])) {
            $set .= 'distance_color=:distance_color,';
        }
        if (isset($argv['distance_general_slope'])) {
            $set .= 'distance_general_slope=:distance_general_slope,';
        }
        if (isset($argv['distance_general_difficulty'])) {
            $set .= 'distance_general_difficulty=:distance_general_difficulty,';
        }
        if (isset($argv['distance_womens_slope'])) {
            $set .= 'distance_womens_slope=:distance_womens_slope,';
        }
        if (isset($argv['distance_womens_difficulty'])) {
            $set .= 'distance_womens_difficulty=:distance_womens_difficulty,';
        }
        if (isset($argv['distance_out'])) {
            $set .= 'distance_out=:distance_out,';
        }
        if (isset($argv['distance_in'])) {
            $set .= 'distance_in=:distance_in,';
        }
        if (isset($argv['distance_tot'])) {
            $set .= 'distance_tot=:distance_tot,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['course_id'])) {
            $stmt->bindValue(':course_id', $argv['course_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['tee_box'])) {
            $stmt->bindValue(':tee_box', $argv['tee_box'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance'])) {
            $stmt->bindValue(':distance', $argv['distance'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_color'])) {
            $stmt->bindValue(':distance_color', $argv['distance_color'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_general_slope'])) {
            $stmt->bindValue(':distance_general_slope', $argv['distance_general_slope'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_general_difficulty'])) {
            $stmt->bindValue(':distance_general_difficulty', $argv['distance_general_difficulty'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_womens_slope'])) {
            $stmt->bindValue(':distance_womens_slope', $argv['distance_womens_slope'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_womens_difficulty'])) {
            $stmt->bindValue(':distance_womens_difficulty', $argv['distance_womens_difficulty'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_out'])) {
            $stmt->bindValue(':distance_out', $argv['distance_out'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_in'])) {
            $stmt->bindValue(':distance_in', $argv['distance_in'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_tot'])) {
            $stmt->bindValue(':distance_tot', $argv['distance_tot'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.golf_tee_box ';

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