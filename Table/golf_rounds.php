<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class golf_rounds extends Entities implements iRest
{
    const COLUMNS = [
            'user_id',
            'round_id',
            'course_id',
            'round_public',
            'score',
            'score_gnr',
            'score_ffs',
            'score_putts',
            'score_out',
            'score_in',
            'score_total',
            'score_total_gnr',
            'score_total_ffs',
            'score_total_putts',
            'score_date',
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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.golf_rounds';

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
        $sql = 'INSERT INTO statscoach.golf_rounds (user_id, round_id, course_id, round_public, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts, score_date) VALUES (:user_id, :round_id, :course_id, :round_public, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts, :score_date)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':user_id', isset($argv['user_id']) ? $argv['user_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':round_id', isset($argv['round_id']) ? $argv['round_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':course_id', isset($argv['course_id']) ? $argv['course_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':round_public', isset($argv['round_public']) ? $argv['round_public'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score', isset($argv['score']) ? $argv['score'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_gnr', isset($argv['score_gnr']) ? $argv['score_gnr'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_ffs', isset($argv['score_ffs']) ? $argv['score_ffs'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_putts', isset($argv['score_putts']) ? $argv['score_putts'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_out', isset($argv['score_out']) ? $argv['score_out'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_in', isset($argv['score_in']) ? $argv['score_in'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_total', isset($argv['score_total']) ? $argv['score_total'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_total_gnr', isset($argv['score_total_gnr']) ? $argv['score_total_gnr'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_total_ffs', isset($argv['score_total_ffs']) ? $argv['score_total_ffs'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_total_putts', isset($argv['score_total_putts']) ? $argv['score_total_putts'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':score_date', isset($argv['score_date']) ? $argv['score_date'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.golf_rounds ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['user_id'])) {
            $set .= 'user_id=:user_id,';
        }
        if (isset($argv['round_id'])) {
            $set .= 'round_id=:round_id,';
        }
        if (isset($argv['course_id'])) {
            $set .= 'course_id=:course_id,';
        }
        if (isset($argv['round_public'])) {
            $set .= 'round_public=:round_public,';
        }
        if (isset($argv['score'])) {
            $set .= 'score=:score,';
        }
        if (isset($argv['score_gnr'])) {
            $set .= 'score_gnr=:score_gnr,';
        }
        if (isset($argv['score_ffs'])) {
            $set .= 'score_ffs=:score_ffs,';
        }
        if (isset($argv['score_putts'])) {
            $set .= 'score_putts=:score_putts,';
        }
        if (isset($argv['score_out'])) {
            $set .= 'score_out=:score_out,';
        }
        if (isset($argv['score_in'])) {
            $set .= 'score_in=:score_in,';
        }
        if (isset($argv['score_total'])) {
            $set .= 'score_total=:score_total,';
        }
        if (isset($argv['score_total_gnr'])) {
            $set .= 'score_total_gnr=:score_total_gnr,';
        }
        if (isset($argv['score_total_ffs'])) {
            $set .= 'score_total_ffs=:score_total_ffs,';
        }
        if (isset($argv['score_total_putts'])) {
            $set .= 'score_total_putts=:score_total_putts,';
        }
        if (isset($argv['score_date'])) {
            $set .= 'score_date=:score_date,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['user_id'])) {
            $stmt->bindValue(':user_id', $argv['user_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['round_id'])) {
            $stmt->bindValue(':round_id', $argv['round_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_id'])) {
            $stmt->bindValue(':course_id', $argv['course_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['round_public'])) {
            $stmt->bindValue(':round_public', $argv['round_public'], \PDO::PARAM_STR);
        }
        if (isset($argv['score'])) {
            $stmt->bindValue(':score', $argv['score'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_gnr'])) {
            $stmt->bindValue(':score_gnr', $argv['score_gnr'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_ffs'])) {
            $stmt->bindValue(':score_ffs', $argv['score_ffs'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_putts'])) {
            $stmt->bindValue(':score_putts', $argv['score_putts'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_out'])) {
            $stmt->bindValue(':score_out', $argv['score_out'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_in'])) {
            $stmt->bindValue(':score_in', $argv['score_in'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_total'])) {
            $stmt->bindValue(':score_total', $argv['score_total'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_total_gnr'])) {
            $stmt->bindValue(':score_total_gnr', $argv['score_total_gnr'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_total_ffs'])) {
            $stmt->bindValue(':score_total_ffs', $argv['score_total_ffs'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_total_putts'])) {
            $stmt->bindValue(':score_total_putts', $argv['score_total_putts'], \PDO::PARAM_STR);
        }
        if (isset($argv['score_date'])) {
            $stmt->bindValue(':score_date', $argv['score_date'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.golf_rounds ';

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