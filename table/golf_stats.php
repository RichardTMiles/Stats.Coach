<?php
namespace Table;


use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;
use Psr\Log\InvalidArgumentException;


class golf_stats extends Entities implements iRest
{
    public const PRIMARY = [
    'stats_id',
    ];

    public const COLUMNS = [
        'stats_id' => [ 'binary', '2', '16' ],'stats_tournaments' => [ 'int', '2', '11' ],'stats_rounds' => [ 'int', '2', '11' ],'stats_handicap' => [ 'int', '2', '11' ],'stats_strokes' => [ 'int', '2', '11' ],'stats_ffs' => [ 'int', '2', '11' ],'stats_gnr' => [ 'int', '2', '11' ],'stats_putts' => [ 'int', '2', '11' ],
    ];

    public const VALIDATION = [];


    public static $injection = [];


    public static function jsonSQLReporting($argv, $sql) : void {
        global $json;
        if (!\is_array($json)) {
            $json = [];
        } elseif (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = [
            $argv,
            $sql
        ];
    }

    public static function buildWhere(array $set, \PDO $pdo, $join = 'AND') : string
    {
        $sql = '(';
        foreach ($set as $column => $value) {
            if (\is_array($value)) {
                $sql .= self::buildWhere($value, $pdo, $join === 'AND' ? 'OR' : 'AND');
            } else if (isset(self::COLUMNS[$column])) {
                if (self::COLUMNS[$column][0] === 'binary') {
                    $sql .= "($column = UNHEX(:" . $column . ")) $join ";
                } else {
                    $sql .= "($column = :" . $column . ") $join ";
                }
            } else {
                $sql .= "($column = " . self::addInjection($value, $pdo) . ") $join ";
            }

        }
        return rtrim($sql, " $join") . ')';
    }

    public static function addInjection($value, \PDO $pdo, $quote = false) : string
    {
        $inject = ':injection' . \count(self::$injection) . 'buildWhere';
        self::$injection[$inject] = $quote ? $pdo->quote($value) : $value;
        return $inject;
    }

    public static function bind(\PDOStatement $stmt, array $argv) {
        if (array_key_exists('stats_id', $argv)) {
            $stats_id = $argv['stats_id'];
            $stmt->bindParam(':stats_id',$stats_id, 2, 16);
        }
        if (array_key_exists('stats_tournaments', $argv)) {
            $stats_tournaments = $argv['stats_tournaments'];
            $stmt->bindParam(':stats_tournaments',$stats_tournaments, 2, 11);
        }
        if (array_key_exists('stats_rounds', $argv)) {
            $stats_rounds = $argv['stats_rounds'];
            $stmt->bindParam(':stats_rounds',$stats_rounds, 2, 11);
        }
        if (array_key_exists('stats_handicap', $argv)) {
            $stats_handicap = $argv['stats_handicap'];
            $stmt->bindParam(':stats_handicap',$stats_handicap, 2, 11);
        }
        if (array_key_exists('stats_strokes', $argv)) {
            $stats_strokes = $argv['stats_strokes'];
            $stmt->bindParam(':stats_strokes',$stats_strokes, 2, 11);
        }
        if (array_key_exists('stats_ffs', $argv)) {
            $stats_ffs = $argv['stats_ffs'];
            $stmt->bindParam(':stats_ffs',$stats_ffs, 2, 11);
        }
        if (array_key_exists('stats_gnr', $argv)) {
            $stats_gnr = $argv['stats_gnr'];
            $stmt->bindParam(':stats_gnr',$stats_gnr, 2, 11);
        }
        if (array_key_exists('stats_putts', $argv)) {
            $stats_putts = $argv['stats_putts'];
            $stmt->bindParam(':stats_putts',$stats_putts, 2, 11);
        }

        foreach (self::$injection as $key => $value) {
            $stmt->bindValue($key,$value);
        }

        return $stmt->execute();
    }


    /**
    *
    *   $argv = [
    *       'select' => [
    *                          '*column name array*', 'etc..'
    *        ],
    *
    *       'where' => [
    *              'Column Name' => 'Value To Constrain',
    *              'Defaults to AND' => 'Nesting array switches to OR',
    *              [
    *                  'Column Name' => 'Value To Constrain',
    *                  'This array is OR'ed togeather' => 'Another sud array would `AND`'
    *                  [ etc... ]
    *              ]
    *        ],
    *
    *        'pagination' => [
    *              'limit' => (int) 90, // The maximum number of rows to return,
    *                       setting the limit explicitly to 1 will return a key pair array of only the
    *                       singular result. SETTING THE LIMIT TO NULL WILL ALLOW INFINITE RESULTS (NO LIMIT).
    *                       The limit defaults to 100 by design.
    *
    *              'order' => '*column name* [ASC|DESC]',  // i.e.  'username ASC' or 'username, email DESC'
    *
    *
    *         ],
    *
    *   ];
    *
    *
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @return bool
    * @throws \Exception
    */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        self::$injection = [];
        $aggregate = false;
        $group = $sql = '';
        $pdo = self::database();

        $get = $argv['select'] ?? array_keys(self::COLUMNS);
        $where = $argv['where'] ?? [];

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !\is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] !== null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                $order = ' ORDER BY ';

                if (isset($argv['pagination']['order']) && $argv['pagination']['order'] !== null) {
                    if (\is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= "$item $sort";
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= 'stats_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY stats_id ASC LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                if (!empty($group)) {
                    $group .= ', ';
                }
            }
            $columnExists = isset(self::COLUMNS[$column]);
            if ($columnExists && self::COLUMNS[$column][0] === 'binary') {
                $sql .= "HEX($column) as $column";
                $group .= $column;
            } elseif ($columnExists) {
                $sql .= $column;
                $group .= $column;
            } else {
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |stats_id|stats_tournaments|stats_rounds|stats_handicap|stats_strokes|stats_ffs|stats_gnr|stats_putts))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    /** @noinspection PhpUndefinedClassInspection */
                    throw new InvalidArgumentException('Arguments passed in SELECT failed the REGEX test!');
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.golf_stats';

        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
        $sql .= ' WHERE  stats_id=UNHEX('.self::addInjection($primary, $pdo).')';
        }

        if ($aggregate  && !empty($group)) {
            $sql .= ' GROUP BY ' . $group . ' ';
        }

        $sql .= $limit;

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (!self::bind($stmt, $argv['where'] ?? [])) {
            return false;
        }

        $return = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        
            if (!empty($primary) || (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] === 1)) {
            $return = (\count($return) === 1 ?
            (\is_array($return['0']) ? $return['0'] : $return) : $return);   // promise this is needed and will still return the desired array except for a single record will not be an array
            }

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        self::$injection = [];
    /** @noinspection SqlResolve */
    $sql = 'INSERT INTO StatsCoach.golf_stats (stats_id, stats_tournaments, stats_rounds, stats_handicap, stats_strokes, stats_ffs, stats_gnr, stats_putts) VALUES ( UNHEX(:stats_id), :stats_tournaments, :stats_rounds, :stats_handicap, :stats_strokes, :stats_ffs, :stats_gnr, :stats_putts)';

    self::jsonSQLReporting(\func_get_args(), $sql);

    $stmt = self::database()->prepare($sql);

                $stats_id = $id = $argv['stats_id'] ?? self::beginTransaction('golf_stats');
                $stmt->bindParam(':stats_id',$stats_id, 2, 16);
                
                    $stats_tournaments =  $argv['stats_tournaments'] ?? '0';
                    $stmt->bindParam(':stats_tournaments',$stats_tournaments, 2, 11);
                        
                    $stats_rounds =  $argv['stats_rounds'] ?? '0';
                    $stmt->bindParam(':stats_rounds',$stats_rounds, 2, 11);
                        
                    $stats_handicap =  $argv['stats_handicap'] ?? '0';
                    $stmt->bindParam(':stats_handicap',$stats_handicap, 2, 11);
                        
                    $stats_strokes =  $argv['stats_strokes'] ?? '0';
                    $stmt->bindParam(':stats_strokes',$stats_strokes, 2, 11);
                        
                    $stats_ffs =  $argv['stats_ffs'] ?? '0';
                    $stmt->bindParam(':stats_ffs',$stats_ffs, 2, 11);
                        
                    $stats_gnr =  $argv['stats_gnr'] ?? '0';
                    $stmt->bindParam(':stats_gnr',$stats_gnr, 2, 11);
                        
                    $stats_putts =  $argv['stats_putts'] ?? '0';
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
        self::$injection = [];
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!\in_array($key, self::COLUMNS, true)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE StatsCoach.golf_stats ';

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

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  stats_id=UNHEX('.self::addInjection($primary, $pdo).')';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (!self::bind($stmt, $argv)){
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