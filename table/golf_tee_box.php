<?php
namespace Table;


use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;
use Psr\Log\InvalidArgumentException;


class golf_tee_box extends Entities implements iRest
{
    public const PRIMARY = [
    
    ];

    public const COLUMNS = [
        'course_id' => [ 'binary', '2', '16' ],'tee_box' => [ 'int', '2', '1' ],'distance' => [ 'blob', '2', '' ],'distance_color' => [ 'varchar', '2', '10' ],'distance_general_slope' => [ 'int', '2', '4' ],'distance_general_difficulty' => [ 'float', '2', '' ],'distance_womens_slope' => [ 'int', '2', '4' ],'distance_womens_difficulty' => [ 'float', '2', '' ],'distance_out' => [ 'int', '2', '7' ],'distance_in' => [ 'int', '2', '7' ],'distance_tot' => [ 'int', '2', '10' ],
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
        if (array_key_exists('course_id', $argv)) {
            $course_id = $argv['course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
        if (array_key_exists('tee_box', $argv)) {
            $tee_box = $argv['tee_box'];
            $stmt->bindParam(':tee_box',$tee_box, 2, 1);
        }
        if (array_key_exists('distance', $argv)) {
            $stmt->bindValue(':distance',$argv['distance'], 2);
        }
        if (array_key_exists('distance_color', $argv)) {
            $distance_color = $argv['distance_color'];
            $stmt->bindParam(':distance_color',$distance_color, 2, 10);
        }
        if (array_key_exists('distance_general_slope', $argv)) {
            $distance_general_slope = $argv['distance_general_slope'];
            $stmt->bindParam(':distance_general_slope',$distance_general_slope, 2, 4);
        }
        if (array_key_exists('distance_general_difficulty', $argv)) {
            $stmt->bindValue(':distance_general_difficulty',$argv['distance_general_difficulty'], 2);
        }
        if (array_key_exists('distance_womens_slope', $argv)) {
            $distance_womens_slope = $argv['distance_womens_slope'];
            $stmt->bindParam(':distance_womens_slope',$distance_womens_slope, 2, 4);
        }
        if (array_key_exists('distance_womens_difficulty', $argv)) {
            $stmt->bindValue(':distance_womens_difficulty',$argv['distance_womens_difficulty'], 2);
        }
        if (array_key_exists('distance_out', $argv)) {
            $distance_out = $argv['distance_out'];
            $stmt->bindParam(':distance_out',$distance_out, 2, 7);
        }
        if (array_key_exists('distance_in', $argv)) {
            $distance_in = $argv['distance_in'];
            $stmt->bindParam(':distance_in',$distance_in, 2, 7);
        }
        if (array_key_exists('distance_tot', $argv)) {
            $distance_tot = $argv['distance_tot'];
            $stmt->bindParam(':distance_tot',$distance_tot, 2, 10);
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
                    $order .= ' ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY  ASC LIMIT 100';
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
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |course_id|tee_box|distance|distance_color|distance_general_slope|distance_general_difficulty|distance_womens_slope|distance_womens_difficulty|distance_out|distance_in|distance_tot))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    /** @noinspection PhpUndefinedClassInspection */
                    throw new InvalidArgumentException('Arguments passed in SELECT failed the REGEX test!');
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.golf_tee_box';

        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
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
    $sql = 'INSERT INTO StatsCoach.golf_tee_box (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES ( UNHEX(:course_id), :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)';

    self::jsonSQLReporting(\func_get_args(), $sql);

    $stmt = self::database()->prepare($sql);

                
                    $course_id = $argv['course_id'];
                    $stmt->bindParam(':course_id',$course_id, 2, 16);
                        
                    $tee_box = $argv['tee_box'];
                    $stmt->bindParam(':tee_box',$tee_box, 2, 1);
                        $stmt->bindValue(':distance',$argv['distance'], 2);
                        
                    $distance_color = $argv['distance_color'];
                    $stmt->bindParam(':distance_color',$distance_color, 2, 10);
                        
                    $distance_general_slope =  $argv['distance_general_slope'] ?? null;
                    $stmt->bindParam(':distance_general_slope',$distance_general_slope, 2, 4);
                        $stmt->bindValue(':distance_general_difficulty',isset($argv['distance_general_difficulty']) ? $argv['distance_general_difficulty'] : null, 2);
                        
                    $distance_womens_slope =  $argv['distance_womens_slope'] ?? null;
                    $stmt->bindParam(':distance_womens_slope',$distance_womens_slope, 2, 4);
                        $stmt->bindValue(':distance_womens_difficulty',isset($argv['distance_womens_difficulty']) ? $argv['distance_womens_difficulty'] : null, 2);
                        
                    $distance_out =  $argv['distance_out'] ?? null;
                    $stmt->bindParam(':distance_out',$distance_out, 2, 7);
                        
                    $distance_in =  $argv['distance_in'] ?? null;
                    $stmt->bindParam(':distance_in',$distance_in, 2, 7);
                        
                    $distance_tot =  $argv['distance_tot'] ?? null;
                    $stmt->bindParam(':distance_tot',$distance_tot, 2, 10);
        



            return $stmt->execute();
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

        $sql = 'UPDATE StatsCoach.golf_tee_box ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

            if (!empty($argv['course_id'])) {
                $set .= 'course_id=UNHEX(:course_id),';
            }
            if (!empty($argv['tee_box'])) {
                $set .= 'tee_box=:tee_box,';
            }
            if (!empty($argv['distance'])) {
                $set .= 'distance=:distance,';
            }
            if (!empty($argv['distance_color'])) {
                $set .= 'distance_color=:distance_color,';
            }
            if (!empty($argv['distance_general_slope'])) {
                $set .= 'distance_general_slope=:distance_general_slope,';
            }
            if (!empty($argv['distance_general_difficulty'])) {
                $set .= 'distance_general_difficulty=:distance_general_difficulty,';
            }
            if (!empty($argv['distance_womens_slope'])) {
                $set .= 'distance_womens_slope=:distance_womens_slope,';
            }
            if (!empty($argv['distance_womens_difficulty'])) {
                $set .= 'distance_womens_difficulty=:distance_womens_difficulty,';
            }
            if (!empty($argv['distance_out'])) {
                $set .= 'distance_out=:distance_out,';
            }
            if (!empty($argv['distance_in'])) {
                $set .= 'distance_in=:distance_in,';
            }
            if (!empty($argv['distance_tot'])) {
                $set .= 'distance_tot=:distance_tot,';
            }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        

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
        self::$injection = [];
        /** @noinspection SqlResolve */
        $sql = 'DELETE FROM StatsCoach.golf_tee_box ';

        $pdo = self::database();

        if (null === $primary) {
        /**
        *   While useful, we've decided to disallow full
        *   table deletions through the rest api. For the
        *   n00bs and future self, "I got chu."
        */
        if (empty($argv)) {
            return false;
        }


        $sql .= ' WHERE ' . self::buildWhere($argv, $pdo);
        } 

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        $r = self::bind($stmt, $argv);

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $r and $remove = null;

        return $r;
    }
}