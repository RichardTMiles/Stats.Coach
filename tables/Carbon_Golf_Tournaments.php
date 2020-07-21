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


class Carbon_Golf_Tournaments extends Rest implements iRest
{
    
    public const TABLE_NAME = 'carbon_golf_tournaments';
    public const TOURNAMENT_ID = 'carbon_golf_tournaments.tournament_id'; 
    public const TOURNAMENT_NAME = 'carbon_golf_tournaments.tournament_name'; 
    public const TOURNAMENT_CREATED_BY_USER_ID = 'carbon_golf_tournaments.tournament_created_by_user_id'; 
    public const TOURNAMENT_COURSE_ID = 'carbon_golf_tournaments.tournament_course_id'; 
    public const TOURNAMENT_HOST_ID = 'carbon_golf_tournaments.tournament_host_id'; 
    public const TOURNAMENT_HOST_NAME = 'carbon_golf_tournaments.tournament_host_name'; 
    public const TOURNAMENT_STYLE = 'carbon_golf_tournaments.tournament_style'; 
    public const TOURNAMENT_TEAM_PRICE = 'carbon_golf_tournaments.tournament_team_price'; 
    public const TOURNAMENT_PAID = 'carbon_golf_tournaments.tournament_paid'; 
    public const TOURNAMENT_DATE = 'carbon_golf_tournaments.tournament_date'; 

    public const PRIMARY = [
        'carbon_golf_tournaments.tournament_id',
    ];

    public const COLUMNS = [
        'carbon_golf_tournaments.tournament_id' => 'tournament_id','carbon_golf_tournaments.tournament_name' => 'tournament_name','carbon_golf_tournaments.tournament_created_by_user_id' => 'tournament_created_by_user_id','carbon_golf_tournaments.tournament_course_id' => 'tournament_course_id','carbon_golf_tournaments.tournament_host_id' => 'tournament_host_id','carbon_golf_tournaments.tournament_host_name' => 'tournament_host_name','carbon_golf_tournaments.tournament_style' => 'tournament_style','carbon_golf_tournaments.tournament_team_price' => 'tournament_team_price','carbon_golf_tournaments.tournament_paid' => 'tournament_paid','carbon_golf_tournaments.tournament_date' => 'tournament_date',
    ];

    public const PDO_VALIDATION = [
        'carbon_golf_tournaments.tournament_id' => ['binary', '2', '16'],'carbon_golf_tournaments.tournament_name' => ['varchar', '2', '225'],'carbon_golf_tournaments.tournament_created_by_user_id' => ['binary', '2', '16'],'carbon_golf_tournaments.tournament_course_id' => ['binary', '2', '16'],'carbon_golf_tournaments.tournament_host_id' => ['binary', '2', '16'],'carbon_golf_tournaments.tournament_host_name' => ['varchar', '2', '225'],'carbon_golf_tournaments.tournament_style' => ['varchar', '2', '20'],'carbon_golf_tournaments.tournament_team_price' => ['int', '2', '11'],'carbon_golf_tournaments.tournament_paid' => ['int', '2', '1'],'carbon_golf_tournaments.tournament_date' => ['date', '2', ''],
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
        $inject = ':injection' . count(self::$injection) . 'carbon_golf_tournaments';
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
        $sql = 'INSERT INTO StatsCoach.carbon_golf_tournaments (tournament_id, tournament_name, tournament_created_by_user_id, tournament_course_id, tournament_host_id, tournament_host_name, tournament_style, tournament_team_price, tournament_paid, tournament_date) VALUES ( UNHEX(:tournament_id), :tournament_name, UNHEX(:tournament_created_by_user_id), UNHEX(:tournament_course_id), UNHEX(:tournament_host_id), :tournament_host_name, :tournament_style, :tournament_team_price, :tournament_paid, :tournament_date)';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

    
        $tournament_id = $id = $argv['carbon_golf_tournaments.tournament_id'] ?? self::beginTransaction('carbon_golf_tournaments', $dependantEntityId);
        $stmt->bindParam(':tournament_id',$tournament_id, 2, 16);
    
        $tournament_name = $argv['carbon_golf_tournaments.tournament_name'];
        $stmt->bindParam(':tournament_name',$tournament_name, 2, 225);
    
        $tournament_created_by_user_id =  $argv['carbon_golf_tournaments.tournament_created_by_user_id'] ?? null;
        $stmt->bindParam(':tournament_created_by_user_id',$tournament_created_by_user_id, 2, 16);
    
        $tournament_course_id =  $argv['carbon_golf_tournaments.tournament_course_id'] ?? null;
        $stmt->bindParam(':tournament_course_id',$tournament_course_id, 2, 16);
    
        $tournament_host_id =  $argv['carbon_golf_tournaments.tournament_host_id'] ?? null;
        $stmt->bindParam(':tournament_host_id',$tournament_host_id, 2, 16);
    
        $tournament_host_name = $argv['carbon_golf_tournaments.tournament_host_name'];
        $stmt->bindParam(':tournament_host_name',$tournament_host_name, 2, 225);
    
        $tournament_style = $argv['carbon_golf_tournaments.tournament_style'];
        $stmt->bindParam(':tournament_style',$tournament_style, 2, 20);
    
        $tournament_team_price =  $argv['carbon_golf_tournaments.tournament_team_price'] ?? null;
        $stmt->bindParam(':tournament_team_price',$tournament_team_price, 2, 11);
    
        $tournament_paid =  $argv['carbon_golf_tournaments.tournament_paid'] ?? '1';
        $stmt->bindParam(':tournament_paid',$tournament_paid, 2, 1);
    
        $stmt->bindValue(':tournament_date',array_key_exists('carbon_golf_tournaments.tournament_date',$argv) ? $argv['carbon_golf_tournaments.tournament_date'] : null, 2);



        return $stmt->execute() ? $id : false;
    
    }
     
    public static function subSelect(string $primary = null, array $argv, PDO $pdo = null): string
    {
        return 'C6SUB461' . self::buildSelectQuery($primary, $argv, $pdo, true);
    }
    
    public static function validateSelectColumn($column) : bool {
        return (bool) preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|-|/| |carbon_golf_tournaments\.tournament_id|carbon_golf_tournaments\.tournament_name|carbon_golf_tournaments\.tournament_created_by_user_id|carbon_golf_tournaments\.tournament_course_id|carbon_golf_tournaments\.tournament_host_id|carbon_golf_tournaments\.tournament_host_name|carbon_golf_tournaments\.tournament_style|carbon_golf_tournaments\.tournament_team_price|carbon_golf_tournaments\.tournament_paid|carbon_golf_tournaments\.tournament_date))+\)*)+ *(as [a-z]+)?#i', $column);
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
                    $order .= 'tournament_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY tournament_id ASC LIMIT 100';
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

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.carbon_golf_tournaments ' . $join;
       
        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
            $sql .= ' WHERE  tournament_id=UNHEX('.self::addInjection($primary, $pdo).')';
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

        $sql = 'UPDATE StatsCoach.carbon_golf_tournaments ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (array_key_exists('carbon_golf_tournaments.tournament_id', $argv)) {
            $set .= 'tournament_id=UNHEX(:tournament_id),';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_name', $argv)) {
            $set .= 'tournament_name=:tournament_name,';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_created_by_user_id', $argv)) {
            $set .= 'tournament_created_by_user_id=UNHEX(:tournament_created_by_user_id),';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_course_id', $argv)) {
            $set .= 'tournament_course_id=UNHEX(:tournament_course_id),';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_host_id', $argv)) {
            $set .= 'tournament_host_id=UNHEX(:tournament_host_id),';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_host_name', $argv)) {
            $set .= 'tournament_host_name=:tournament_host_name,';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_style', $argv)) {
            $set .= 'tournament_style=:tournament_style,';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_team_price', $argv)) {
            $set .= 'tournament_team_price=:tournament_team_price,';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_paid', $argv)) {
            $set .= 'tournament_paid=:tournament_paid,';
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_date', $argv)) {
            $set .= 'tournament_date=:tournament_date,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  tournament_id=UNHEX('.self::addInjection($primary, $pdo).')';
        

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (array_key_exists('carbon_golf_tournaments.tournament_id', $argv)) {
            $tournament_id = $argv['carbon_golf_tournaments.tournament_id'];
            $stmt->bindParam(':tournament_id',$tournament_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_name', $argv)) {
            $tournament_name = $argv['carbon_golf_tournaments.tournament_name'];
            $stmt->bindParam(':tournament_name',$tournament_name, 2, 225);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_created_by_user_id', $argv)) {
            $tournament_created_by_user_id = $argv['carbon_golf_tournaments.tournament_created_by_user_id'];
            $stmt->bindParam(':tournament_created_by_user_id',$tournament_created_by_user_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_course_id', $argv)) {
            $tournament_course_id = $argv['carbon_golf_tournaments.tournament_course_id'];
            $stmt->bindParam(':tournament_course_id',$tournament_course_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_host_id', $argv)) {
            $tournament_host_id = $argv['carbon_golf_tournaments.tournament_host_id'];
            $stmt->bindParam(':tournament_host_id',$tournament_host_id, 2, 16);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_host_name', $argv)) {
            $tournament_host_name = $argv['carbon_golf_tournaments.tournament_host_name'];
            $stmt->bindParam(':tournament_host_name',$tournament_host_name, 2, 225);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_style', $argv)) {
            $tournament_style = $argv['carbon_golf_tournaments.tournament_style'];
            $stmt->bindParam(':tournament_style',$tournament_style, 2, 20);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_team_price', $argv)) {
            $tournament_team_price = $argv['carbon_golf_tournaments.tournament_team_price'];
            $stmt->bindParam(':tournament_team_price',$tournament_team_price, 2, 11);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_paid', $argv)) {
            $tournament_paid = $argv['carbon_golf_tournaments.tournament_paid'];
            $stmt->bindParam(':tournament_paid',$tournament_paid, 2, 1);
        }
        if (array_key_exists('carbon_golf_tournaments.tournament_date', $argv)) {
            $stmt->bindValue(':tournament_date',$argv['carbon_golf_tournaments.tournament_date'], 2);
        }

        self::bind($stmt);

        if (!$stmt->execute()) {
            return false;
        }
        
        $argv = array_combine(
            array_map(
                static function($k) { return str_replace('carbon_golf_tournaments.', '', $k); },
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
                JOIN StatsCoach.carbon_golf_tournaments on c.entity_pk = carbon_golf_tournaments.tournament_id';

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
