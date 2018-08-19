<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_stats extends Entities implements iRest
{
    const PRIMARY = [
    'stats_id',
    ];

    const COLUMNS = [
    'stats_id','stats_tournaments','stats_rounds','stats_handicap','stats_strokes','stats_ffs','stats_gnr','stats_putts',
    ];

    const VALIDATION = [];

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
        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $group = $sql = '';

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] != null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                 $order = ' ORDER BY ';

                if (isset($argv['pagination']['order']) && $argv['pagination']['order'] != null) {
                    if (is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= $item .' '. $sort;
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= self::PRIMARY[0] . ' DESC';
                }
            }
            $limit = $order .' '. $limit;
        } else {
            $limit = ' ORDER BY ' . self::PRIMARY[0] . ' DESC LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                $group .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
                $group .= "$column";
            } else {
                $sql .= $column;
                $group .= $column;
            }
        }

        if (isset($argv['aggregate']) && (is_array($argv['aggregate']) || $argv['aggregate'] = json_decode($argv['aggregate'], true))) {
            foreach($argv['aggregate'] as $key => $value){
                switch ($key){
                    case 'count':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "COUNT($value) AS count ";
                        break;
                    case 'AVG':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "AVG($value) AS avg ";
                        break;
                    case 'MIN':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MIN($value) AS min ";
                        break;
                    case 'MAX':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MAX($value) AS max ";
                        break;
                }
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_stats';

        $pdo = Database::database();

        if (empty($primary)) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $sql .= $build_where($value, $join === 'AND' ? 'OR' : 'AND');
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
        } else {
            $primary = $pdo->quote($primary);
            $sql .= ' WHERE  stats_id=UNHEX(' . $primary .')';
        }

        if (isset($argv['aggregate'])) {
            $sql .= ' GROUP BY ' . $group . ' ';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        
        if (empty($primary) && ($argv['pagination']['limit'] ?? false) !== 1 && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
            $return = [$return];
        }

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.golf_stats (stats_id, stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_ffs, stats_gnr, stats_putts) VALUES ( UNHEX(:stats_id), :stats_tournaments, :stats_rounds, :stats_handicap, :stats_strokes, :stats_ffs, :stats_gnr, :stats_putts)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            $stats_id = $id = isset($argv['stats_id']) ? $argv['stats_id'] : self::new_entity('golf_stats');
            $stmt->bindParam(':stats_id',$stats_id, 2, 16);
            
                $stats_tournaments = isset($argv['stats_tournaments']) ? $argv['stats_tournaments'] : '0';
                $stmt->bindParam(':stats_tournaments',$stats_tournaments, 2, 11);
                    
                $stats_rounds = isset($argv['stats_rounds']) ? $argv['stats_rounds'] : '0';
                $stmt->bindParam(':stats_rounds',$stats_rounds, 2, 11);
                    
                $stats_handicap = isset($argv['stats_handicap']) ? $argv['stats_handicap'] : '0';
                $stmt->bindParam(':stats_handicap',$stats_handicap, 2, 11);
                    
                $stats_strokes = isset($argv['stats_strokes']) ? $argv['stats_strokes'] : '0';
                $stmt->bindParam(':stats_strokes',$stats_strokes, 2, 11);
                    
                $stats_ffs = isset($argv['stats_ffs']) ? $argv['stats_ffs'] : '0';
                $stmt->bindParam(':stats_ffs',$stats_ffs, 2, 11);
                    
                $stats_gnr = isset($argv['stats_gnr']) ? $argv['stats_gnr'] : '0';
                $stmt->bindParam(':stats_gnr',$stats_gnr, 2, 11);
                    
                $stats_putts = isset($argv['stats_putts']) ? $argv['stats_putts'] : '0';
                $stmt->bindParam(':stats_putts',$stats_putts, 2, 11);
        
        return $stmt->execute() ? $id : false;

    }

    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
    {
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.golf_stats ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (!empty($argv['stats_id'])) {
            $set .= 'stats_id=UNHEX(:stats_id),';
        }
        if (!empty($argv['stats_tournaments'])) {
            $set .= 'stats_tournaments=:stats_tournaments,';
        }
        if (!empty($argv['stats_rounds'])) {
            $set .= 'stats_rounds=:stats_rounds,';
        }
        if (!empty($argv['stats_handicap'])) {
            $set .= 'stats_handicap=:stats_handicap,';
        }
        if (!empty($argv['stats_strokes'])) {
            $set .= 'stats_strokes=:stats_strokes,';
        }
        if (!empty($argv['stats_ffs'])) {
            $set .= 'stats_ffs=:stats_ffs,';
        }
        if (!empty($argv['stats_gnr'])) {
            $set .= 'stats_gnr=:stats_gnr,';
        }
        if (!empty($argv['stats_putts'])) {
            $set .= 'stats_putts=:stats_putts,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  stats_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        global $json;

        if (empty($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        if (!empty($argv['stats_id'])) {
            $stats_id = $argv['stats_id'];
            $stmt->bindParam(':stats_id',$stats_id, 2, 16);
        }
        if (!empty($argv['stats_tournaments'])) {
            $stats_tournaments = $argv['stats_tournaments'];
            $stmt->bindParam(':stats_tournaments',$stats_tournaments, 2, 11);
        }
        if (!empty($argv['stats_rounds'])) {
            $stats_rounds = $argv['stats_rounds'];
            $stmt->bindParam(':stats_rounds',$stats_rounds, 2, 11);
        }
        if (!empty($argv['stats_handicap'])) {
            $stats_handicap = $argv['stats_handicap'];
            $stmt->bindParam(':stats_handicap',$stats_handicap, 2, 11);
        }
        if (!empty($argv['stats_strokes'])) {
            $stats_strokes = $argv['stats_strokes'];
            $stmt->bindParam(':stats_strokes',$stats_strokes, 2, 11);
        }
        if (!empty($argv['stats_ffs'])) {
            $stats_ffs = $argv['stats_ffs'];
            $stmt->bindParam(':stats_ffs',$stats_ffs, 2, 11);
        }
        if (!empty($argv['stats_gnr'])) {
            $stats_gnr = $argv['stats_gnr'];
            $stmt->bindParam(':stats_gnr',$stats_gnr, 2, 11);
        }
        if (!empty($argv['stats_putts'])) {
            $stats_putts = $argv['stats_putts'];
            $stmt->bindParam(':stats_putts',$stats_putts, 2, 11);
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
        return \Table\carbon::Delete($remove, $primary, $argv);
    }
}