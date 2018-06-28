<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class golf_stats extends Entities implements iRest
{
    const COLUMNS = [
            'stats_id',
            'stats_tournaments',
            'stats_rounds',
            'stats_handicap',
            'stats_strokes',
            'stats_ffs',
            'stats_gnr',
            'stats_putts',
    ];

    const PRIMARY = "stats_id";

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

        $sql = 'SELECT ' .  $get . ' FROM statscoach.golf_stats';

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
        $sql = 'INSERT INTO statscoach.golf_stats (stats_id, stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_ffs, stats_gnr, stats_putts) VALUES (:stats_id, :stats_tournaments, :stats_rounds, :stats_handicap, :stats_strokes, :stats_ffs, :stats_gnr, :stats_putts)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':stats_id', isset($argv['stats_id']) ? $argv['stats_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_tournaments', isset($argv['stats_tournaments']) ? $argv['stats_tournaments'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_rounds', isset($argv['stats_rounds']) ? $argv['stats_rounds'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_handicap', isset($argv['stats_handicap']) ? $argv['stats_handicap'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_strokes', isset($argv['stats_strokes']) ? $argv['stats_strokes'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_ffs', isset($argv['stats_ffs']) ? $argv['stats_ffs'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_gnr', isset($argv['stats_gnr']) ? $argv['stats_gnr'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':stats_putts', isset($argv['stats_putts']) ? $argv['stats_putts'] : null, \PDO::PARAM_STR);
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

        $sql = 'UPDATE statscoach.golf_stats ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';
        if (isset($argv['stats_id'])) {
            $set .= 'stats_id=:stats_id,';
        }
        if (isset($argv['stats_tournaments'])) {
            $set .= 'stats_tournaments=:stats_tournaments,';
        }
        if (isset($argv['stats_rounds'])) {
            $set .= 'stats_rounds=:stats_rounds,';
        }
        if (isset($argv['stats_handicap'])) {
            $set .= 'stats_handicap=:stats_handicap,';
        }
        if (isset($argv['stats_strokes'])) {
            $set .= 'stats_strokes=:stats_strokes,';
        }
        if (isset($argv['stats_ffs'])) {
            $set .= 'stats_ffs=:stats_ffs,';
        }
        if (isset($argv['stats_gnr'])) {
            $set .= 'stats_gnr=:stats_gnr,';
        }
        if (isset($argv['stats_putts'])) {
            $set .= 'stats_putts=:stats_putts,';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['stats_id'])) {
            $stmt->bindValue(':stats_id', $argv['stats_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_tournaments'])) {
            $stmt->bindValue(':stats_tournaments', $argv['stats_tournaments'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_rounds'])) {
            $stmt->bindValue(':stats_rounds', $argv['stats_rounds'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_handicap'])) {
            $stmt->bindValue(':stats_handicap', $argv['stats_handicap'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_strokes'])) {
            $stmt->bindValue(':stats_strokes', $argv['stats_strokes'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_ffs'])) {
            $stmt->bindValue(':stats_ffs', $argv['stats_ffs'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_gnr'])) {
            $stmt->bindValue(':stats_gnr', $argv['stats_gnr'], \PDO::PARAM_STR);
        }
        if (isset($argv['stats_putts'])) {
            $stmt->bindValue(':stats_putts', $argv['stats_putts'], \PDO::PARAM_STR);
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
        $sql = 'DELETE FROM statscoach.golf_stats ';

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