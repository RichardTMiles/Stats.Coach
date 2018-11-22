<?php
namespace Table;


use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;
use Psr\Log\InvalidArgumentException;


class golf_course extends Entities implements iRest
{
    public const PRIMARY = [
    'course_id',
    ];

    public const COLUMNS = [
        'course_id' => [ 'binary', '2', '16' ],'course_name' => [ 'varchar', '2', '16' ],'course_holes' => [ 'int', '2', '2' ],'course_phone' => [ 'varchar', '2', '15' ],'course_difficulty' => [ 'int', '2', '10' ],'course_rank' => [ 'int', '2', '5' ],'course_tee_boxes' => [ 'json', '2', '' ],'course_par' => [ 'json', '2', '' ],'course_handicap' => [ 'json', '2', '' ],'course_par_out' => [ 'int', '2', '2' ],'course_par_in' => [ 'int', '2', '2' ],'par_tot' => [ 'int', '2', '2' ],'course_par_hcp' => [ 'int', '2', '4' ],'course_type' => [ 'char', '2', '30' ],'course_access' => [ 'varchar', '2', '120' ],'pga_professional' => [ 'varchar', '2', '40' ],'website' => [ 'varchar', '2', '20' ],'created_by' => [ 'binary', '2', '16' ],'course_input_completed' => [ 'varchar', '2', '10' ],'tee_boxes' => [ 'int', '2', '11' ],'handicap_count' => [ 'int', '2', '11' ],
    ];

    public const VALIDATION = [];


    public static $injection = [];


    public static function jsonSQLReporting($argv, $sql) : void {
        global $json;
        if (!\is_array($json)) {
            $json = [];
        }
        if (!isset($json['sql'])) {
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
        if (array_key_exists('course_name', $argv)) {
            $course_name = $argv['course_name'];
            $stmt->bindParam(':course_name',$course_name, 2, 16);
        }
        if (array_key_exists('course_holes', $argv)) {
            $course_holes = $argv['course_holes'];
            $stmt->bindParam(':course_holes',$course_holes, 2, 2);
        }
        if (array_key_exists('course_phone', $argv)) {
            $course_phone = $argv['course_phone'];
            $stmt->bindParam(':course_phone',$course_phone, 2, 15);
        }
        if (array_key_exists('course_difficulty', $argv)) {
            $course_difficulty = $argv['course_difficulty'];
            $stmt->bindParam(':course_difficulty',$course_difficulty, 2, 10);
        }
        if (array_key_exists('course_rank', $argv)) {
            $course_rank = $argv['course_rank'];
            $stmt->bindParam(':course_rank',$course_rank, 2, 5);
        }
        if (array_key_exists('course_tee_boxes', $argv)) {
            $stmt->bindValue(':course_tee_boxes',json_encode($argv['course_tee_boxes']), 2);
        }
        if (array_key_exists('course_par', $argv)) {
            $stmt->bindValue(':course_par',json_encode($argv['course_par']), 2);
        }
        if (array_key_exists('course_handicap', $argv)) {
            $stmt->bindValue(':course_handicap',json_encode($argv['course_handicap']), 2);
        }
        if (array_key_exists('course_par_out', $argv)) {
            $course_par_out = $argv['course_par_out'];
            $stmt->bindParam(':course_par_out',$course_par_out, 2, 2);
        }
        if (array_key_exists('course_par_in', $argv)) {
            $course_par_in = $argv['course_par_in'];
            $stmt->bindParam(':course_par_in',$course_par_in, 2, 2);
        }
        if (array_key_exists('par_tot', $argv)) {
            $par_tot = $argv['par_tot'];
            $stmt->bindParam(':par_tot',$par_tot, 2, 2);
        }
        if (array_key_exists('course_par_hcp', $argv)) {
            $course_par_hcp = $argv['course_par_hcp'];
            $stmt->bindParam(':course_par_hcp',$course_par_hcp, 2, 4);
        }
        if (array_key_exists('course_type', $argv)) {
            $course_type = $argv['course_type'];
            $stmt->bindParam(':course_type',$course_type, 2, 30);
        }
        if (array_key_exists('course_access', $argv)) {
            $course_access = $argv['course_access'];
            $stmt->bindParam(':course_access',$course_access, 2, 120);
        }
        if (array_key_exists('pga_professional', $argv)) {
            $pga_professional = $argv['pga_professional'];
            $stmt->bindParam(':pga_professional',$pga_professional, 2, 40);
        }
        if (array_key_exists('website', $argv)) {
            $website = $argv['website'];
            $stmt->bindParam(':website',$website, 2, 20);
        }
        if (array_key_exists('created_by', $argv)) {
            $created_by = $argv['created_by'];
            $stmt->bindParam(':created_by',$created_by, 2, 16);
        }
        if (array_key_exists('course_input_completed', $argv)) {
            $course_input_completed = $argv['course_input_completed'];
            $stmt->bindParam(':course_input_completed',$course_input_completed, 2, 10);
        }
        if (array_key_exists('tee_boxes', $argv)) {
            $tee_boxes = $argv['tee_boxes'];
            $stmt->bindParam(':tee_boxes',$tee_boxes, 2, 11);
        }
        if (array_key_exists('handicap_count', $argv)) {
            $handicap_count = $argv['handicap_count'];
            $stmt->bindParam(':handicap_count',$handicap_count, 2, 11);
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
                    $order .= 'course_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY course_id ASC LIMIT 100';
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
                if (!preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|\-|\/| |course_id|course_name|course_holes|course_phone|course_difficulty|course_rank|course_tee_boxes|course_par|course_handicap|course_par_out|course_par_in|par_tot|course_par_hcp|course_type|course_access|pga_professional|website|created_by|course_input_completed|tee_boxes|handicap_count))+\)*)+ *(as [a-z]+)?#i', $column)) {
                    /** @noinspection PhpUndefinedClassInspection */
                    throw new InvalidArgumentException('Arguments passed in SELECT failed the REGEX test!');
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.golf_course';

        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
        $sql .= ' WHERE  course_id=UNHEX('.self::addInjection($primary, $pdo).')';
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

        
        if ($primary !== null || (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] === 1 && \count($return) === 1)) {
            $return = \is_array($return[0] ?? false) ? $return[0] : $return;
            // promise this is needed and will still return the desired array except for a single record will not be an array
        if (array_key_exists('course_tee_boxes', $return)) {
                $return['course_tee_boxes'] = json_decode($return['course_tee_boxes'], true);
            }
        if (array_key_exists('course_par', $return)) {
                $return['course_par'] = json_decode($return['course_par'], true);
            }
        if (array_key_exists('course_handicap', $return)) {
                $return['course_handicap'] = json_decode($return['course_handicap'], true);
            }
        
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
    $sql = 'INSERT INTO StatsCoach.golf_course (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, course_tee_boxes, course_par, course_handicap, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, pga_professional, website, created_by, course_input_completed, tee_boxes, handicap_count) VALUES ( UNHEX(:course_id), :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :course_tee_boxes, :course_par, :course_handicap, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :pga_professional, :website, UNHEX(:created_by), :course_input_completed, :tee_boxes, :handicap_count)';

    self::jsonSQLReporting(\func_get_args(), $sql);

    $stmt = self::database()->prepare($sql);

                $course_id = $id = $argv['course_id'] ?? self::beginTransaction('golf_course');
                $stmt->bindParam(':course_id',$course_id, 2, 16);
                
                    $course_name = $argv['course_name'];
                    $stmt->bindParam(':course_name',$course_name, 2, 16);
                        
                    $course_holes =  $argv['course_holes'] ?? '18';
                    $stmt->bindParam(':course_holes',$course_holes, 2, 2);
                        
                    $course_phone =  $argv['course_phone'] ?? null;
                    $stmt->bindParam(':course_phone',$course_phone, 2, 15);
                        
                    $course_difficulty =  $argv['course_difficulty'] ?? null;
                    $stmt->bindParam(':course_difficulty',$course_difficulty, 2, 10);
                        
                    $course_rank =  $argv['course_rank'] ?? null;
                    $stmt->bindParam(':course_rank',$course_rank, 2, 5);
                        $stmt->bindValue(':course_tee_boxes',json_encode($argv['course_tee_boxes']), 2);
                        $stmt->bindValue(':course_par',json_encode($argv['course_par']), 2);
                        $stmt->bindValue(':course_handicap',json_encode($argv['course_handicap']), 2);
                        
                    $course_par_out =  $argv['course_par_out'] ?? '0';
                    $stmt->bindParam(':course_par_out',$course_par_out, 2, 2);
                        
                    $course_par_in =  $argv['course_par_in'] ?? '0';
                    $stmt->bindParam(':course_par_in',$course_par_in, 2, 2);
                        
                    $par_tot =  $argv['par_tot'] ?? '0';
                    $stmt->bindParam(':par_tot',$par_tot, 2, 2);
                        
                    $course_par_hcp =  $argv['course_par_hcp'] ?? '0';
                    $stmt->bindParam(':course_par_hcp',$course_par_hcp, 2, 4);
                        
                    $course_type =  $argv['course_type'] ?? null;
                    $stmt->bindParam(':course_type',$course_type, 2, 30);
                        
                    $course_access =  $argv['course_access'] ?? null;
                    $stmt->bindParam(':course_access',$course_access, 2, 120);
                        
                    $pga_professional =  $argv['pga_professional'] ?? null;
                    $stmt->bindParam(':pga_professional',$pga_professional, 2, 40);
                        
                    $website =  $argv['website'] ?? null;
                    $stmt->bindParam(':website',$website, 2, 20);
                        
                    $created_by =  $argv['created_by'] ?? null;
                    $stmt->bindParam(':created_by',$created_by, 2, 16);
                        
                    $course_input_completed =  $argv['course_input_completed'] ?? 'NO';
                    $stmt->bindParam(':course_input_completed',$course_input_completed, 2, 10);
                        
                    $tee_boxes =  $argv['tee_boxes'] ?? null;
                    $stmt->bindParam(':tee_boxes',$tee_boxes, 2, 11);
                        
                    $handicap_count =  $argv['handicap_count'] ?? null;
                    $stmt->bindParam(':handicap_count',$handicap_count, 2, 11);
        


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
            if (!\array_key_exists($key, self::COLUMNS)){
                throw new PublicAlert('The key {' . $key . '} does not exist.');
                #unset($argv[$key]);
            }
        }

        $sql = 'UPDATE StatsCoach.golf_course ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

            if (array_key_exists('course_id', $argv)) {
                $set .= 'course_id=UNHEX(:course_id),';
            }
            if (array_key_exists('course_name', $argv)) {
                $set .= 'course_name=:course_name,';
            }
            if (array_key_exists('course_holes', $argv)) {
                $set .= 'course_holes=:course_holes,';
            }
            if (array_key_exists('course_phone', $argv)) {
                $set .= 'course_phone=:course_phone,';
            }
            if (array_key_exists('course_difficulty', $argv)) {
                $set .= 'course_difficulty=:course_difficulty,';
            }
            if (array_key_exists('course_rank', $argv)) {
                $set .= 'course_rank=:course_rank,';
            }
            if (array_key_exists('course_tee_boxes', $argv)) {
                $set .= 'course_tee_boxes=:course_tee_boxes,';
            }
            if (array_key_exists('course_par', $argv)) {
                $set .= 'course_par=:course_par,';
            }
            if (array_key_exists('course_handicap', $argv)) {
                $set .= 'course_handicap=:course_handicap,';
            }
            if (array_key_exists('course_par_out', $argv)) {
                $set .= 'course_par_out=:course_par_out,';
            }
            if (array_key_exists('course_par_in', $argv)) {
                $set .= 'course_par_in=:course_par_in,';
            }
            if (array_key_exists('par_tot', $argv)) {
                $set .= 'par_tot=:par_tot,';
            }
            if (array_key_exists('course_par_hcp', $argv)) {
                $set .= 'course_par_hcp=:course_par_hcp,';
            }
            if (array_key_exists('course_type', $argv)) {
                $set .= 'course_type=:course_type,';
            }
            if (array_key_exists('course_access', $argv)) {
                $set .= 'course_access=:course_access,';
            }
            if (array_key_exists('pga_professional', $argv)) {
                $set .= 'pga_professional=:pga_professional,';
            }
            if (array_key_exists('website', $argv)) {
                $set .= 'website=:website,';
            }
            if (array_key_exists('created_by', $argv)) {
                $set .= 'created_by=UNHEX(:created_by),';
            }
            if (array_key_exists('course_input_completed', $argv)) {
                $set .= 'course_input_completed=:course_input_completed,';
            }
            if (array_key_exists('tee_boxes', $argv)) {
                $set .= 'tee_boxes=:tee_boxes,';
            }
            if (array_key_exists('handicap_count', $argv)) {
                $set .= 'handicap_count=:handicap_count,';
            }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  course_id=UNHEX('.self::addInjection($primary, $pdo).')';

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
        return carbon::Delete($remove, $primary, $argv);
    }
}