<?php
namespace Tables;


use PDO;
use PDOStatement;

use function array_key_exists;
use function count;
use function is_array;
use CarbonPHP\Rest;
use CarbonPHP\Interfaces\iRest;
use CarbonPHP\Interfaces\iRestfulReferences;
use CarbonPHP\Error\PublicAlert;


class Carbon_Golf_Courses extends Rest implements iRest
{
    
    public const TABLE_NAME = 'carbon_golf_courses';
    public const COURSE_ID = 'carbon_golf_courses.course_id'; 
    public const COURSE_NAME = 'carbon_golf_courses.course_name'; 
    public const COURSE_HOLES = 'carbon_golf_courses.course_holes'; 
    public const COURSE_PHONE = 'carbon_golf_courses.course_phone'; 
    public const COURSE_DIFFICULTY = 'carbon_golf_courses.course_difficulty'; 
    public const COURSE_RANK = 'carbon_golf_courses.course_rank'; 
    public const COURSE_TEE_BOXES = 'carbon_golf_courses.course_tee_boxes'; 
    public const COURSE_PAR = 'carbon_golf_courses.course_par'; 
    public const COURSE_HANDICAP = 'carbon_golf_courses.course_handicap'; 
    public const COURSE_PAR_OUT = 'carbon_golf_courses.course_par_out'; 
    public const COURSE_PAR_IN = 'carbon_golf_courses.course_par_in'; 
    public const PAR_TOT = 'carbon_golf_courses.par_tot'; 
    public const COURSE_PAR_HCP = 'carbon_golf_courses.course_par_hcp'; 
    public const COURSE_TYPE = 'carbon_golf_courses.course_type'; 
    public const COURSE_ACCESS = 'carbon_golf_courses.course_access'; 
    public const PGA_PROFESSIONAL = 'carbon_golf_courses.pga_professional'; 
    public const WEBSITE = 'carbon_golf_courses.website'; 
    public const CREATED_BY = 'carbon_golf_courses.created_by'; 
    public const COURSE_INPUT_COMPLETED = 'carbon_golf_courses.course_input_completed'; 
    public const TEE_BOXES = 'carbon_golf_courses.tee_boxes'; 
    public const HANDICAP_COUNT = 'carbon_golf_courses.handicap_count'; 

    public const PRIMARY = [
        'carbon_golf_courses.course_id',
    ];

    public const COLUMNS = [
        'carbon_golf_courses.course_id' => 'course_id','carbon_golf_courses.course_name' => 'course_name','carbon_golf_courses.course_holes' => 'course_holes','carbon_golf_courses.course_phone' => 'course_phone','carbon_golf_courses.course_difficulty' => 'course_difficulty','carbon_golf_courses.course_rank' => 'course_rank','carbon_golf_courses.course_tee_boxes' => 'course_tee_boxes','carbon_golf_courses.course_par' => 'course_par','carbon_golf_courses.course_handicap' => 'course_handicap','carbon_golf_courses.course_par_out' => 'course_par_out','carbon_golf_courses.course_par_in' => 'course_par_in','carbon_golf_courses.par_tot' => 'par_tot','carbon_golf_courses.course_par_hcp' => 'course_par_hcp','carbon_golf_courses.course_type' => 'course_type','carbon_golf_courses.course_access' => 'course_access','carbon_golf_courses.pga_professional' => 'pga_professional','carbon_golf_courses.website' => 'website','carbon_golf_courses.created_by' => 'created_by','carbon_golf_courses.course_input_completed' => 'course_input_completed','carbon_golf_courses.tee_boxes' => 'tee_boxes','carbon_golf_courses.handicap_count' => 'handicap_count',
    ];

    public const PDO_VALIDATION = [
        'carbon_golf_courses.course_id' => ['binary', '2', '16'],'carbon_golf_courses.course_name' => ['varchar', '2', '16'],'carbon_golf_courses.course_holes' => ['int', '2', '2'],'carbon_golf_courses.course_phone' => ['varchar', '2', '15'],'carbon_golf_courses.course_difficulty' => ['int', '2', '10'],'carbon_golf_courses.course_rank' => ['int', '2', '5'],'carbon_golf_courses.course_tee_boxes' => ['json', '2', ''],'carbon_golf_courses.course_par' => ['json', '2', ''],'carbon_golf_courses.course_handicap' => ['json', '2', ''],'carbon_golf_courses.course_par_out' => ['int', '2', '2'],'carbon_golf_courses.course_par_in' => ['int', '2', '2'],'carbon_golf_courses.par_tot' => ['int', '2', '2'],'carbon_golf_courses.course_par_hcp' => ['int', '2', '4'],'carbon_golf_courses.course_type' => ['char', '2', '30'],'carbon_golf_courses.course_access' => ['varchar', '2', '120'],'carbon_golf_courses.pga_professional' => ['varchar', '2', '40'],'carbon_golf_courses.website' => ['varchar', '2', '20'],'carbon_golf_courses.created_by' => ['binary', '2', '16'],'carbon_golf_courses.course_input_completed' => ['varchar', '2', '10'],'carbon_golf_courses.tee_boxes' => ['int', '2', '11'],'carbon_golf_courses.handicap_count' => ['int', '2', '11'],
    ];
    
    public const VALIDATION = [];

    public static array $injection = [];

    public static function jsonSQLReporting($argv, $sql) : void {
        global $json;
        if (!is_array($json)) {
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
    
    public static function buildWhere(array $set, PDO $pdo, $join = 'AND') : string
    {
        $sql = '(';
        $bump = false;
        foreach ($set as $column => $value) {
            if (is_array($value)) {
                if ($bump) {
                    $sql .= " $join ";
                }
                $bump = true;
                $sql .= self::buildWhere($value, $pdo, $join === 'AND' ? 'OR' : 'AND');
            } else if (array_key_exists($column, self::PDO_VALIDATION)) {
                $bump = false;
                /** @noinspection SubStrUsedAsStrPosInspection */
                if (substr($value, 0, '8') === 'C6SUB461') {
                    $subQuery = substr($value, '8');
                    $sql .= "($column = $subQuery ) $join ";
                } else if (self::PDO_VALIDATION[$column][0] === 'binary') {
                    $sql .= "($column = UNHEX(" . self::addInjection($value, $pdo) . ")) $join ";
                } else {
                    $sql .= "($column = " . self::addInjection($value, $pdo) . ") $join ";
                }
            } else {
                $bump = false;
                $sql .= "($column = " . self::addInjection($value, $pdo) . ") $join ";
            }
        }
        return rtrim($sql, " $join") . ')';
    }

    public static function addInjection($value, PDO $pdo, $quote = false): string
    {
        $inject = ':injection' . count(self::$injection) . 'carbon_golf_courses';
        self::$injection[$inject] = $quote ? $pdo->quote($value) : $value;
        return $inject;
    }

    public static function bind(PDOStatement $stmt): void 
    {
        foreach (self::$injection as $key => $value) {
            $stmt->bindValue($key,$value);
        }
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
    */
    public static function Get(array &$return, string $primary = null, array $argv): bool
    {
        $pdo = self::database();

        $sql = self::buildSelectQuery($primary, $argv, $pdo);
        
        $stmt = $pdo->prepare($sql);

        self::bind($stmt);

        if (!$stmt->execute()) {
            return false;
        }

        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::PDO_VALIDATION
        */

        
        if ($primary !== null || (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] === 1 && count($return) === 1)) {
            $return = isset($return[0]) && is_array($return[0]) ? $return[0] : $return;
            // promise this is needed and will still return the desired array except for a single record will not be an array
        if (array_key_exists('carbon_golf_courses.course_tee_boxes', $return)) {
                $return['course_tee_boxes'] = json_decode($return['course_tee_boxes'], true);
            }
        if (array_key_exists('carbon_golf_courses.course_par', $return)) {
                $return['course_par'] = json_decode($return['course_par'], true);
            }
        if (array_key_exists('carbon_golf_courses.course_handicap', $return)) {
                $return['course_handicap'] = json_decode($return['course_handicap'], true);
            }
        
        }

        return true;
    }

    /**
     * @param array $argv
     * @param string|null $dependantEntityId - a C6 Hex entity key 
     * @return bool|string
     * @throws PublicAlert
     */
    public static function Post(array $argv, string $dependantEntityId = null)
    {
        self::$injection = [];
        /** @noinspection SqlResolve */
        $sql = 'INSERT INTO StatsCoach.carbon_golf_courses (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, course_tee_boxes, course_par, course_handicap, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, pga_professional, website, created_by, course_input_completed, tee_boxes, handicap_count) VALUES ( UNHEX(:course_id), :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :course_tee_boxes, :course_par, :course_handicap, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :pga_professional, :website, UNHEX(:created_by), :course_input_completed, :tee_boxes, :handicap_count)';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

    
        $course_id = $id = $argv['carbon_golf_courses.course_id'] ?? self::beginTransaction('carbon_golf_courses', $dependantEntityId);
        $stmt->bindParam(':course_id',$course_id, 2, 16);
    
        $course_name = $argv['carbon_golf_courses.course_name'];
        $stmt->bindParam(':course_name',$course_name, 2, 16);
    
        $course_holes =  $argv['carbon_golf_courses.course_holes'] ?? '18';
        $stmt->bindParam(':course_holes',$course_holes, 2, 2);
    
        $course_phone =  $argv['carbon_golf_courses.course_phone'] ?? null;
        $stmt->bindParam(':course_phone',$course_phone, 2, 15);
    
        $course_difficulty =  $argv['carbon_golf_courses.course_difficulty'] ?? null;
        $stmt->bindParam(':course_difficulty',$course_difficulty, 2, 10);
    
        $course_rank =  $argv['carbon_golf_courses.course_rank'] ?? null;
        $stmt->bindParam(':course_rank',$course_rank, 2, 5);
    
        $stmt->bindValue(':course_tee_boxes',json_encode($argv['carbon_golf_courses.course_tee_boxes']), 2);

        $stmt->bindValue(':course_par',json_encode($argv['carbon_golf_courses.course_par']), 2);

        $stmt->bindValue(':course_handicap',json_encode($argv['carbon_golf_courses.course_handicap']), 2);

        $course_par_out =  $argv['carbon_golf_courses.course_par_out'] ?? '0';
        $stmt->bindParam(':course_par_out',$course_par_out, 2, 2);
    
        $course_par_in =  $argv['carbon_golf_courses.course_par_in'] ?? '0';
        $stmt->bindParam(':course_par_in',$course_par_in, 2, 2);
    
        $par_tot =  $argv['carbon_golf_courses.par_tot'] ?? '0';
        $stmt->bindParam(':par_tot',$par_tot, 2, 2);
    
        $course_par_hcp =  $argv['carbon_golf_courses.course_par_hcp'] ?? '0';
        $stmt->bindParam(':course_par_hcp',$course_par_hcp, 2, 4);
    
        $course_type =  $argv['carbon_golf_courses.course_type'] ?? null;
        $stmt->bindParam(':course_type',$course_type, 2, 30);
    
        $course_access =  $argv['carbon_golf_courses.course_access'] ?? null;
        $stmt->bindParam(':course_access',$course_access, 2, 120);
    
        $pga_professional =  $argv['carbon_golf_courses.pga_professional'] ?? null;
        $stmt->bindParam(':pga_professional',$pga_professional, 2, 40);
    
        $website =  $argv['carbon_golf_courses.website'] ?? null;
        $stmt->bindParam(':website',$website, 2, 20);
    
        $created_by =  $argv['carbon_golf_courses.created_by'] ?? null;
        $stmt->bindParam(':created_by',$created_by, 2, 16);
    
        $course_input_completed =  $argv['carbon_golf_courses.course_input_completed'] ?? 'NO';
        $stmt->bindParam(':course_input_completed',$course_input_completed, 2, 10);
    
        $tee_boxes =  $argv['carbon_golf_courses.tee_boxes'] ?? null;
        $stmt->bindParam(':tee_boxes',$tee_boxes, 2, 11);
    
        $handicap_count =  $argv['carbon_golf_courses.handicap_count'] ?? null;
        $stmt->bindParam(':handicap_count',$handicap_count, 2, 11);
    


        return $stmt->execute() ? $id : false;
    
    }
     
    public static function subSelect(string $primary = null, array $argv, PDO $pdo = null): string
    {
        return 'C6SUB461' . self::buildSelectQuery($primary, $argv, $pdo, true);
    }
    
    public static function validateSelectColumn($column) : bool {
        return (bool) preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|-|/| |carbon_golf_courses\.course_id|carbon_golf_courses\.course_name|carbon_golf_courses\.course_holes|carbon_golf_courses\.course_phone|carbon_golf_courses\.course_difficulty|carbon_golf_courses\.course_rank|carbon_golf_courses\.course_tee_boxes|carbon_golf_courses\.course_par|carbon_golf_courses\.course_handicap|carbon_golf_courses\.course_par_out|carbon_golf_courses\.course_par_in|carbon_golf_courses\.par_tot|carbon_golf_courses\.course_par_hcp|carbon_golf_courses\.course_type|carbon_golf_courses\.course_access|carbon_golf_courses\.pga_professional|carbon_golf_courses\.website|carbon_golf_courses\.created_by|carbon_golf_courses\.course_input_completed|carbon_golf_courses\.tee_boxes|carbon_golf_courses\.handicap_count))+\)*)+ *(as [a-z]+)?#i', $column);
    }
    
    public static function buildSelectQuery(string $primary = null, array $argv, PDO $pdo = null, bool $noHEX = false) : string 
    {
        if ($pdo === null) {
            $pdo = self::database();
        }
        self::$injection = [];
        $aggregate = false;
        $group = [];
        $sql = '';
        $get = $argv['select'] ?? array_keys(self::PDO_VALIDATION);
        $where = $argv['where'] ?? [];

        // pagination
        if (array_key_exists('pagination',$argv)) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (array_key_exists('limit',$argv['pagination']) && $argv['pagination']['limit'] !== null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                $order = ' ORDER BY ';

                if (array_key_exists('order',$argv['pagination']) && $argv['pagination']['order'] !== null) {
                    if (is_array($argv['pagination']['order'])) {
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

        // join 
        $join = ''; 
        $tableList = [];
        if (array_key_exists('join', $argv)) {
            foreach ($argv['join'] as $by => $tables) {
                $buildJoin = static function ($method) use ($tables, &$join, &$tableList) {
                    foreach ($tables as $table => $stmt) {
                        $tableList[] = $table;
                        switch (count($stmt)) {
                            case 2: 
                                if (is_string($stmt[0]) && is_string($stmt[1])) {
                                    $join .= $method . $table . ' ON ' . $stmt[0] . '=' . $stmt[1];
                                } else {
                                    return false; // todo debugging
                                }
                                break;
                            case 3:
                                if (is_string($stmt[0]) && is_string($stmt[1]) && is_string($stmt[2])) {
                                    $join .= $method . $table . ' ON ' . $stmt[0] . $stmt[1] . $stmt[2]; 
                                } else {
                                    return false; // todo debugging
                                }
                                break;
                            default:
                                return false; // todo debug check, common when joins are not a list of values
                        }
                    }
                    return true;
                };
                switch ($by) {
                    case 'inner':
                        if (!$buildJoin(' INNER JOIN ')) {
                            return false; 
                        }
                        break;
                    case 'left':
                        if (!$buildJoin(' LEFT JOIN ')) {
                            return false; 
                        }
                        break;
                    case 'right':
                        if (!$buildJoin(' RIGHT JOIN ')) {
                            return false; 
                        }
                        break;
                    default:
                        return false; // todo - debugging stmts
                }
            }
        }

        // Select
        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
            }
            $columnExists = array_key_exists($column, self::PDO_VALIDATION);
            if ($columnExists) {
                if (!$noHEX && self::PDO_VALIDATION[$column][0] === 'binary') {
                    $asShort = trim($column, self::TABLE_NAME . '.');
                    $prefix = self::TABLE_NAME . '.';
                    if (strpos($column, $prefix) === 0) {
                        $asShort = substr($column, strlen($prefix));
                    }
                    $sql .= "HEX($column) as $asShort";
                    $group[] = $column;
                } elseif ($columnExists) {
                    $sql .= $column;
                    $group[] = $column;  
                }  
            } else if (self::validateSelectColumn($column)) {
                $sql .= $column;
                $group[] = $column;
                $aggregate = true;
            } else {  
                $valid = false;
                $tablesReffrenced = $tableList;
                while (!empty($tablesReffrenced)) {
                     $table = __NAMESPACE__ . '\\' . array_pop($tablesReffrenced);
                     
                     if (!class_exists($table)){
                         continue;
                     }
                     $imp = array_map('strtolower', array_keys(class_implements($table)));
                    
                   
                     /** @noinspection ClassConstantUsageCorrectnessInspection */
                     if (!in_array(strtolower(iRest::class), $imp, true) && 
                         !in_array(strtolower(iRestfulReferences::class), $imp, true)) {
                         continue;
                     }
                     /** @noinspection PhpUndefinedMethodInspection */
                     if ($table::validateSelectColumn($column)) { 
                        $group[] = $column;
                        $valid = true;
                        break; 
                     }
                }
                if (!$valid) {
                    return false;
                }
                $sql .= $column;
                $aggregate = true;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.carbon_golf_courses ' . $join;
       
        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
            $sql .= ' WHERE  course_id=UNHEX('.self::addInjection($primary, $pdo).')';
        }

        if ($aggregate  && !empty($group)) {
            $sql .= ' GROUP BY ' . implode(', ', $group). ' ';
        }

        $sql .= $limit;

        self::jsonSQLReporting(\func_get_args(), $sql);

        return '(' . $sql . ')';
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
        
        if (array_key_exists(self::UPDATE, $argv)) {
            $argv = $argv[self::UPDATE];
        }
        
        foreach ($argv as $key => $value) {
            if (!array_key_exists($key, self::PDO_VALIDATION)){
                return false;
            }
        }

        $sql = 'UPDATE StatsCoach.carbon_golf_courses ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (array_key_exists('carbon_golf_courses.course_id', $argv)) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (array_key_exists('carbon_golf_courses.course_name', $argv)) {
            $set .= 'course_name=:course_name,';
        }
        if (array_key_exists('carbon_golf_courses.course_holes', $argv)) {
            $set .= 'course_holes=:course_holes,';
        }
        if (array_key_exists('carbon_golf_courses.course_phone', $argv)) {
            $set .= 'course_phone=:course_phone,';
        }
        if (array_key_exists('carbon_golf_courses.course_difficulty', $argv)) {
            $set .= 'course_difficulty=:course_difficulty,';
        }
        if (array_key_exists('carbon_golf_courses.course_rank', $argv)) {
            $set .= 'course_rank=:course_rank,';
        }
        if (array_key_exists('carbon_golf_courses.course_tee_boxes', $argv)) {
            $set .= 'course_tee_boxes=:course_tee_boxes,';
        }
        if (array_key_exists('carbon_golf_courses.course_par', $argv)) {
            $set .= 'course_par=:course_par,';
        }
        if (array_key_exists('carbon_golf_courses.course_handicap', $argv)) {
            $set .= 'course_handicap=:course_handicap,';
        }
        if (array_key_exists('carbon_golf_courses.course_par_out', $argv)) {
            $set .= 'course_par_out=:course_par_out,';
        }
        if (array_key_exists('carbon_golf_courses.course_par_in', $argv)) {
            $set .= 'course_par_in=:course_par_in,';
        }
        if (array_key_exists('carbon_golf_courses.par_tot', $argv)) {
            $set .= 'par_tot=:par_tot,';
        }
        if (array_key_exists('carbon_golf_courses.course_par_hcp', $argv)) {
            $set .= 'course_par_hcp=:course_par_hcp,';
        }
        if (array_key_exists('carbon_golf_courses.course_type', $argv)) {
            $set .= 'course_type=:course_type,';
        }
        if (array_key_exists('carbon_golf_courses.course_access', $argv)) {
            $set .= 'course_access=:course_access,';
        }
        if (array_key_exists('carbon_golf_courses.pga_professional', $argv)) {
            $set .= 'pga_professional=:pga_professional,';
        }
        if (array_key_exists('carbon_golf_courses.website', $argv)) {
            $set .= 'website=:website,';
        }
        if (array_key_exists('carbon_golf_courses.created_by', $argv)) {
            $set .= 'created_by=UNHEX(:created_by),';
        }
        if (array_key_exists('carbon_golf_courses.course_input_completed', $argv)) {
            $set .= 'course_input_completed=:course_input_completed,';
        }
        if (array_key_exists('carbon_golf_courses.tee_boxes', $argv)) {
            $set .= 'tee_boxes=:tee_boxes,';
        }
        if (array_key_exists('carbon_golf_courses.handicap_count', $argv)) {
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

        if (array_key_exists('carbon_golf_courses.course_id', $argv)) {
            $course_id = $argv['carbon_golf_courses.course_id'];
            $stmt->bindParam(':course_id',$course_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_courses.course_name', $argv)) {
            $course_name = $argv['carbon_golf_courses.course_name'];
            $stmt->bindParam(':course_name',$course_name, 2, 16);
        }
        if (array_key_exists('carbon_golf_courses.course_holes', $argv)) {
            $course_holes = $argv['carbon_golf_courses.course_holes'];
            $stmt->bindParam(':course_holes',$course_holes, 2, 2);
        }
        if (array_key_exists('carbon_golf_courses.course_phone', $argv)) {
            $course_phone = $argv['carbon_golf_courses.course_phone'];
            $stmt->bindParam(':course_phone',$course_phone, 2, 15);
        }
        if (array_key_exists('carbon_golf_courses.course_difficulty', $argv)) {
            $course_difficulty = $argv['carbon_golf_courses.course_difficulty'];
            $stmt->bindParam(':course_difficulty',$course_difficulty, 2, 10);
        }
        if (array_key_exists('carbon_golf_courses.course_rank', $argv)) {
            $course_rank = $argv['carbon_golf_courses.course_rank'];
            $stmt->bindParam(':course_rank',$course_rank, 2, 5);
        }
        if (array_key_exists('carbon_golf_courses.course_tee_boxes', $argv)) {
            $stmt->bindValue(':course_tee_boxes',json_encode($argv['carbon_golf_courses.course_tee_boxes']), 2);
        }
        if (array_key_exists('carbon_golf_courses.course_par', $argv)) {
            $stmt->bindValue(':course_par',json_encode($argv['carbon_golf_courses.course_par']), 2);
        }
        if (array_key_exists('carbon_golf_courses.course_handicap', $argv)) {
            $stmt->bindValue(':course_handicap',json_encode($argv['carbon_golf_courses.course_handicap']), 2);
        }
        if (array_key_exists('carbon_golf_courses.course_par_out', $argv)) {
            $course_par_out = $argv['carbon_golf_courses.course_par_out'];
            $stmt->bindParam(':course_par_out',$course_par_out, 2, 2);
        }
        if (array_key_exists('carbon_golf_courses.course_par_in', $argv)) {
            $course_par_in = $argv['carbon_golf_courses.course_par_in'];
            $stmt->bindParam(':course_par_in',$course_par_in, 2, 2);
        }
        if (array_key_exists('carbon_golf_courses.par_tot', $argv)) {
            $par_tot = $argv['carbon_golf_courses.par_tot'];
            $stmt->bindParam(':par_tot',$par_tot, 2, 2);
        }
        if (array_key_exists('carbon_golf_courses.course_par_hcp', $argv)) {
            $course_par_hcp = $argv['carbon_golf_courses.course_par_hcp'];
            $stmt->bindParam(':course_par_hcp',$course_par_hcp, 2, 4);
        }
        if (array_key_exists('carbon_golf_courses.course_type', $argv)) {
            $course_type = $argv['carbon_golf_courses.course_type'];
            $stmt->bindParam(':course_type',$course_type, 2, 30);
        }
        if (array_key_exists('carbon_golf_courses.course_access', $argv)) {
            $course_access = $argv['carbon_golf_courses.course_access'];
            $stmt->bindParam(':course_access',$course_access, 2, 120);
        }
        if (array_key_exists('carbon_golf_courses.pga_professional', $argv)) {
            $pga_professional = $argv['carbon_golf_courses.pga_professional'];
            $stmt->bindParam(':pga_professional',$pga_professional, 2, 40);
        }
        if (array_key_exists('carbon_golf_courses.website', $argv)) {
            $website = $argv['carbon_golf_courses.website'];
            $stmt->bindParam(':website',$website, 2, 20);
        }
        if (array_key_exists('carbon_golf_courses.created_by', $argv)) {
            $created_by = $argv['carbon_golf_courses.created_by'];
            $stmt->bindParam(':created_by',$created_by, 2, 16);
        }
        if (array_key_exists('carbon_golf_courses.course_input_completed', $argv)) {
            $course_input_completed = $argv['carbon_golf_courses.course_input_completed'];
            $stmt->bindParam(':course_input_completed',$course_input_completed, 2, 10);
        }
        if (array_key_exists('carbon_golf_courses.tee_boxes', $argv)) {
            $tee_boxes = $argv['carbon_golf_courses.tee_boxes'];
            $stmt->bindParam(':tee_boxes',$tee_boxes, 2, 11);
        }
        if (array_key_exists('carbon_golf_courses.handicap_count', $argv)) {
            $handicap_count = $argv['carbon_golf_courses.handicap_count'];
            $stmt->bindParam(':handicap_count',$handicap_count, 2, 11);
        }

        self::bind($stmt);

        if (!$stmt->execute()) {
            return false;
        }
        
        $argv = array_combine(
            array_map(
                static function($k) { return str_replace('carbon_golf_courses.', '', $k); },
                array_keys($argv)
            ),
            array_values($argv)
        );

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
        if (null !== $primary) {
            return Carbons::Delete($remove, $primary, $argv);
        }

        /**
         *   While useful, we've decided to disallow full
         *   table deletions through the rest api. For the
         *   n00bs and future self, "I got chu."
         */
        if (empty($argv)) {
            return false;
        }

        self::$injection = []; 
        
        /** @noinspection SqlResolve */
        /** @noinspection SqlWithoutWhere */
        $sql = 'DELETE c FROM StatsCoach.carbons c 
                JOIN StatsCoach.carbon_golf_courses on c.entity_pk = carbon_golf_courses.course_id';

        $pdo = self::database();

        $sql .= ' WHERE ' . self::buildWhere($argv, $pdo);
        
        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        self::bind($stmt);

        $r = $stmt->execute();

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $r and $remove = [];

        return $r;
    }
}
