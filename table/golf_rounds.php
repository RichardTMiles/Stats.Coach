<?php
namespace Table;


use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;
use Psr\Log\InvalidArgumentException;


class golf_rounds extends Entities implements iRest
{
    public const PRIMARY = [
    
    ];

    public const COLUMNS = [
        'user_id' => [ 'binary', '2', '16' ],'round_id' => [ 'binary', '2', '16' ],'course_id' => [ 'binary', '2', '16' ],'round_public' => [ 'int', '2', '1' ],'score' => [ 'text', '2', '' ],'score_gnr' => [ 'text', '2', '' ],'score_ffs' => [ 'text', '2', '' ],'score_putts' => [ 'text', '2', '' ],'score_out' => [ 'int', '2', '2' ],'score_in' => [ 'int', '2', '3' ],'score_total' => [ 'int', '2', '3' ],'score_total_gnr' => [ 'int', '2', '11' ],'score_total_ffs' => [ 'int', '2', '3' ],'score_total_putts' => [ 'int', '2', '11' ],'score_date' => [ 'text,', '2', '' ],
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
        if (!empty($argv['user_id'])) {
            $user_id = $argv['user_id'];
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
        if (!empty($argv['round_id'])) {
            $round_id = $argv['round_id'];
            $stmt->bindParam(':round_id',$round_id, 2, 16);
        }
        if (!empty($argv['course_id'])) {
            $course_id = $argv['course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
        if (!empty($argv['round_public'])) {
            $round_public = $argv['round_public'];
            $stmt->bindParam(':round_public',$round_public, 2, 1);
        }
        if (!empty($argv['score'])) {
            $stmt->bindValue(':score',$argv['score'], 2);
        }
        if (!empty($argv['score_gnr'])) {
            $stmt->bindValue(':score_gnr',$argv['score_gnr'], 2);
        }
        if (!empty($argv['score_ffs'])) {
            $stmt->bindValue(':score_ffs',$argv['score_ffs'], 2);
        }
        if (!empty($argv['score_putts'])) {
            $stmt->bindValue(':score_putts',$argv['score_putts'], 2);
        }
        if (!empty($argv['score_out'])) {
            $score_out = $argv['score_out'];
            $stmt->bindParam(':score_out',$score_out, 2, 2);
        }
        if (!empty($argv['score_in'])) {
            $score_in = $argv['score_in'];
            $stmt->bindParam(':score_in',$score_in, 2, 3);
        }
        if (!empty($argv['score_total'])) {
            $score_total = $argv['score_total'];
            $stmt->bindParam(':score_total',$score_total, 2, 3);
        }
        if (!empty($argv['score_total_gnr'])) {
            $score_total_gnr = $argv['score_total_gnr'];
            $stmt->bindParam(':score_total_gnr',$score_total_gnr, 2, 11);
        }
        if (!empty($argv['score_total_ffs'])) {
            $score_total_ffs = $argv['score_total_ffs'];
            $stmt->bindParam(':score_total_ffs',$score_total_ffs, 2, 3);
        }
        if (!empty($argv['score_total_putts'])) {
            $score_total_putts = $argv['score_total_putts'];
            $stmt->bindParam(':score_total_putts',$score_total_putts, 2, 11);
        }
        if (!empty($argv['score_date'])) {
            $stmt->bindValue(':score_date',$argv['score_date'], 2);
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
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |user_id|round_id|course_id|round_public|score|score_gnr|score_ffs|score_putts|score_out|score_in|score_total|score_total_gnr|score_total_ffs|score_total_putts|score_date))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    /** @noinspection PhpUndefinedClassInspection */
                    throw new InvalidArgumentException('Arguments passed in SELECT failed the REGEX test!');
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.golf_rounds';

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

        $return = $stmt->fetchAll();

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
    /** @noinspection SqlResolve */
    $sql = 'INSERT INTO StatsCoach.golf_rounds (user_id, round_id, course_id, round_public, score, score_gnr, score_ffs, score_putts, score_out, score_in, score_total, score_total_gnr, score_total_ffs, score_total_putts, score_date) VALUES ( UNHEX(:user_id), UNHEX(:round_id), UNHEX(:course_id), :round_public, :score, :score_gnr, :score_ffs, :score_putts, :score_out, :score_in, :score_total, :score_total_gnr, :score_total_ffs, :score_total_putts, :score_date)';

    self::jsonSQLReporting(\func_get_args(), $sql);

    $stmt = self::database()->prepare($sql);

                
                    $user_id = $argv['user_id'];
                    $stmt->bindParam(':user_id',$user_id, 2, 16);
                        
                    $round_id = $argv['round_id'];
                    $stmt->bindParam(':round_id',$round_id, 2, 16);
                        
                    $course_id = $argv['course_id'];
                    $stmt->bindParam(':course_id',$course_id, 2, 16);
                        
                    $round_public =  $argv['round_public'] ?? '1';
                    $stmt->bindParam(':round_public',$round_public, 2, 1);
                        $stmt->bindValue(':score',$argv['score'], 2);
                        $stmt->bindValue(':score_gnr',$argv['score_gnr'], 2);
                        $stmt->bindValue(':score_ffs',$argv['score_ffs'], 2);
                        $stmt->bindValue(':score_putts',$argv['score_putts'], 2);
                        
                    $score_out = $argv['score_out'];
                    $stmt->bindParam(':score_out',$score_out, 2, 2);
                        
                    $score_in = $argv['score_in'];
                    $stmt->bindParam(':score_in',$score_in, 2, 3);
                        
                    $score_total = $argv['score_total'];
                    $stmt->bindParam(':score_total',$score_total, 2, 3);
                        
                    $score_total_gnr =  $argv['score_total_gnr'] ?? '0';
                    $stmt->bindParam(':score_total_gnr',$score_total_gnr, 2, 11);
                        
                    $score_total_ffs =  $argv['score_total_ffs'] ?? '0';
                    $stmt->bindParam(':score_total_ffs',$score_total_ffs, 2, 3);
                        
                    $score_total_putts =  $argv['score_total_putts'] ?? null;
                    $stmt->bindParam(':score_total_putts',$score_total_putts, 2, 11);
                        $stmt->bindValue(':score_date',$argv['score_date'], 2);
        



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
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!\in_array($key, self::COLUMNS, true)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE StatsCoach.golf_rounds ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

            if (!empty($argv['user_id'])) {
                $set .= 'user_id=UNHEX(:user_id),';
            }
            if (!empty($argv['round_id'])) {
                $set .= 'round_id=UNHEX(:round_id),';
            }
            if (!empty($argv['course_id'])) {
                $set .= 'course_id=UNHEX(:course_id),';
            }
            if (!empty($argv['round_public'])) {
                $set .= 'round_public=:round_public,';
            }
            if (!empty($argv['score'])) {
                $set .= 'score=:score,';
            }
            if (!empty($argv['score_gnr'])) {
                $set .= 'score_gnr=:score_gnr,';
            }
            if (!empty($argv['score_ffs'])) {
                $set .= 'score_ffs=:score_ffs,';
            }
            if (!empty($argv['score_putts'])) {
                $set .= 'score_putts=:score_putts,';
            }
            if (!empty($argv['score_out'])) {
                $set .= 'score_out=:score_out,';
            }
            if (!empty($argv['score_in'])) {
                $set .= 'score_in=:score_in,';
            }
            if (!empty($argv['score_total'])) {
                $set .= 'score_total=:score_total,';
            }
            if (!empty($argv['score_total_gnr'])) {
                $set .= 'score_total_gnr=:score_total_gnr,';
            }
            if (!empty($argv['score_total_ffs'])) {
                $set .= 'score_total_ffs=:score_total_ffs,';
            }
            if (!empty($argv['score_total_putts'])) {
                $set .= 'score_total_putts=:score_total_putts,';
            }
            if (!empty($argv['score_date'])) {
                $set .= 'score_date=:score_date,';
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
        /** @noinspection SqlResolve */
        $sql = 'DELETE FROM StatsCoach.golf_rounds ';

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