<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_stats extends Entities implements iRest
{
    const PRIMARY = "stats_id";

    const COLUMNS = [
    'stats_id','stats_tournaments','stats_rounds','stats_handicap','stats_strokes','stats_ffs','stats_gnr','stats_putts',
    ];

    const BINARY = [
    'stats_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_stats';

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
        $sql = 'INSERT INTO statscoach.golf_stats (stats_id, stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_ffs, stats_gnr, stats_putts) VALUES ( :stats_id, :stats_tournaments, :stats_rounds, :stats_handicap, :stats_strokes, :stats_ffs, :stats_gnr, :stats_putts)';
        $stmt = Database::database()->prepare($sql);
            $stats_id = $id = self::new_entity('golf_stats');
            $stmt->bindParam(':stats_id',$stats_id, \PDO::PARAM_STR, 16);
            
                $stats_tournaments = isset($argv['stats_tournaments']) ? $argv['stats_tournaments'] : '0';
                $stmt->bindParam(':stats_tournaments',$stats_tournaments, \PDO::PARAM_STR, 11);
                    
                $stats_rounds = isset($argv['stats_rounds']) ? $argv['stats_rounds'] : '0';
                $stmt->bindParam(':stats_rounds',$stats_rounds, \PDO::PARAM_STR, 11);
                    
                $stats_handicap = isset($argv['stats_handicap']) ? $argv['stats_handicap'] : '0';
                $stmt->bindParam(':stats_handicap',$stats_handicap, \PDO::PARAM_STR, 11);
                    
                $stats_strokes = isset($argv['stats_strokes']) ? $argv['stats_strokes'] : '0';
                $stmt->bindParam(':stats_strokes',$stats_strokes, \PDO::PARAM_STR, 11);
                    
                $stats_ffs = isset($argv['stats_ffs']) ? $argv['stats_ffs'] : '0';
                $stmt->bindParam(':stats_ffs',$stats_ffs, \PDO::PARAM_STR, 11);
                    
                $stats_gnr = isset($argv['stats_gnr']) ? $argv['stats_gnr'] : '0';
                $stmt->bindParam(':stats_gnr',$stats_gnr, \PDO::PARAM_STR, 11);
                    
                $stats_putts = isset($argv['stats_putts']) ? $argv['stats_putts'] : '0';
                $stmt->bindParam(':stats_putts',$stats_putts, \PDO::PARAM_STR, 11);
        
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

        $sql = 'UPDATE statscoach.golf_stats ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['stats_id'])) {
            $set .= 'stats_id=UNHEX(:stats_id),';
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
            $stats_id = 'UNHEX('.$argv['stats_id'].')';
            $stmt->bindParam(':stats_id', $stats_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['stats_tournaments'])) {
            $stats_tournaments = $argv['stats_tournaments'];
            $stmt->bindParam(':stats_tournaments',$stats_tournaments, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['stats_rounds'])) {
            $stats_rounds = $argv['stats_rounds'];
            $stmt->bindParam(':stats_rounds',$stats_rounds, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['stats_handicap'])) {
            $stats_handicap = $argv['stats_handicap'];
            $stmt->bindParam(':stats_handicap',$stats_handicap, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['stats_strokes'])) {
            $stats_strokes = $argv['stats_strokes'];
            $stmt->bindParam(':stats_strokes',$stats_strokes, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['stats_ffs'])) {
            $stats_ffs = $argv['stats_ffs'];
            $stmt->bindParam(':stats_ffs',$stats_ffs, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['stats_gnr'])) {
            $stats_gnr = $argv['stats_gnr'];
            $stmt->bindParam(':stats_gnr',$stats_gnr, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['stats_putts'])) {
            $stats_putts = $argv['stats_putts'];
            $stmt->bindParam(':stats_putts',$stats_putts, \PDO::PARAM_STR, 11 );
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