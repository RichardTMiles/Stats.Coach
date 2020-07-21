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


class Carbon_Teams extends Rest implements iRest
{
    
    public const TABLE_NAME = 'carbon_teams';
    public const TEAM_ID = 'carbon_teams.team_id'; 
    public const TEAM_COACH = 'carbon_teams.team_coach'; 
    public const PARENT_TEAM = 'carbon_teams.parent_team'; 
    public const TEAM_CODE = 'carbon_teams.team_code'; 
    public const TEAM_NAME = 'carbon_teams.team_name'; 
    public const TEAM_RANK = 'carbon_teams.team_rank'; 
    public const TEAM_SPORT = 'carbon_teams.team_sport'; 
    public const TEAM_DIVISION = 'carbon_teams.team_division'; 
    public const TEAM_SCHOOL = 'carbon_teams.team_school'; 
    public const TEAM_DISTRICT = 'carbon_teams.team_district'; 
    public const TEAM_MEMBERSHIP = 'carbon_teams.team_membership'; 
    public const TEAM_PHOTO = 'carbon_teams.team_photo'; 

    public const PRIMARY = [
        'carbon_teams.team_id',
    ];

    public const COLUMNS = [
        'carbon_teams.team_id' => 'team_id','carbon_teams.team_coach' => 'team_coach','carbon_teams.parent_team' => 'parent_team','carbon_teams.team_code' => 'team_code','carbon_teams.team_name' => 'team_name','carbon_teams.team_rank' => 'team_rank','carbon_teams.team_sport' => 'team_sport','carbon_teams.team_division' => 'team_division','carbon_teams.team_school' => 'team_school','carbon_teams.team_district' => 'team_district','carbon_teams.team_membership' => 'team_membership','carbon_teams.team_photo' => 'team_photo',
    ];

    public const PDO_VALIDATION = [
        'carbon_teams.team_id' => ['binary', '2', '16'],'carbon_teams.team_coach' => ['binary', '2', '16'],'carbon_teams.parent_team' => ['binary', '2', '16'],'carbon_teams.team_code' => ['varchar', '2', '225'],'carbon_teams.team_name' => ['varchar', '2', '225'],'carbon_teams.team_rank' => ['int', '2', '11'],'carbon_teams.team_sport' => ['varchar', '2', '225'],'carbon_teams.team_division' => ['varchar', '2', '225'],'carbon_teams.team_school' => ['varchar', '2', '225'],'carbon_teams.team_district' => ['varchar', '2', '225'],'carbon_teams.team_membership' => ['varchar', '2', '225'],'carbon_teams.team_photo' => ['binary', '2', '16'],
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
        $inject = ':injection' . count(self::$injection) . 'carbon_teams';
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
        $sql = 'INSERT INTO StatsCoach.carbon_teams (team_id, team_coach, parent_team, team_code, team_name, team_rank, team_sport, team_division, team_school, team_district, team_membership, team_photo) VALUES ( UNHEX(:team_id), UNHEX(:team_coach), UNHEX(:parent_team), :team_code, :team_name, :team_rank, :team_sport, :team_division, :team_school, :team_district, :team_membership, UNHEX(:team_photo))';

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

    
        $team_id = $id = $argv['carbon_teams.team_id'] ?? self::beginTransaction('carbon_teams', $dependantEntityId);
        $stmt->bindParam(':team_id',$team_id, 2, 16);
    
        $team_coach = $argv['carbon_teams.team_coach'];
        $stmt->bindParam(':team_coach',$team_coach, 2, 16);
    
        $parent_team =  $argv['carbon_teams.parent_team'] ?? null;
        $stmt->bindParam(':parent_team',$parent_team, 2, 16);
    
        $team_code = $argv['carbon_teams.team_code'];
        $stmt->bindParam(':team_code',$team_code, 2, 225);
    
        $team_name = $argv['carbon_teams.team_name'];
        $stmt->bindParam(':team_name',$team_name, 2, 225);
    
        $team_rank =  $argv['carbon_teams.team_rank'] ?? '0';
        $stmt->bindParam(':team_rank',$team_rank, 2, 11);
    
        $team_sport =  $argv['carbon_teams.team_sport'] ?? 'Golf';
        $stmt->bindParam(':team_sport',$team_sport, 2, 225);
    
        $team_division =  $argv['carbon_teams.team_division'] ?? null;
        $stmt->bindParam(':team_division',$team_division, 2, 225);
    
        $team_school =  $argv['carbon_teams.team_school'] ?? null;
        $stmt->bindParam(':team_school',$team_school, 2, 225);
    
        $team_district =  $argv['carbon_teams.team_district'] ?? null;
        $stmt->bindParam(':team_district',$team_district, 2, 225);
    
        $team_membership =  $argv['carbon_teams.team_membership'] ?? null;
        $stmt->bindParam(':team_membership',$team_membership, 2, 225);
    
        $team_photo =  $argv['carbon_teams.team_photo'] ?? null;
        $stmt->bindParam(':team_photo',$team_photo, 2, 16);
    


        return $stmt->execute() ? $id : false;
    
    }
     
    public static function subSelect(string $primary = null, array $argv, PDO $pdo = null): string
    {
        return 'C6SUB461' . self::buildSelectQuery($primary, $argv, $pdo, true);
    }
    
    public static function validateSelectColumn($column) : bool {
        return (bool) preg_match('#(((((hex|argv|count|sum|min|max) *\(+ *)+)|(distinct|\*|\+|-|/| |carbon_teams\.team_id|carbon_teams\.team_coach|carbon_teams\.parent_team|carbon_teams\.team_code|carbon_teams\.team_name|carbon_teams\.team_rank|carbon_teams\.team_sport|carbon_teams\.team_division|carbon_teams\.team_school|carbon_teams\.team_district|carbon_teams\.team_membership|carbon_teams\.team_photo))+\)*)+ *(as [a-z]+)?#i', $column);
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
                    $order .= 'team_id ASC';
                }
            }
            $limit = "$order $limit";
        } else {
            $limit = ' ORDER BY team_id ASC LIMIT 100';
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

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.carbon_teams ' . $join;
       
        if (null === $primary) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (!empty($where)) {
                $sql .= ' WHERE ' . self::buildWhere($where, $pdo);
            }
        } else {
            $sql .= ' WHERE  team_id=UNHEX('.self::addInjection($primary, $pdo).')';
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

        $sql = 'UPDATE StatsCoach.carbon_teams ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (array_key_exists('carbon_teams.team_id', $argv)) {
            $set .= 'team_id=UNHEX(:team_id),';
        }
        if (array_key_exists('carbon_teams.team_coach', $argv)) {
            $set .= 'team_coach=UNHEX(:team_coach),';
        }
        if (array_key_exists('carbon_teams.parent_team', $argv)) {
            $set .= 'parent_team=UNHEX(:parent_team),';
        }
        if (array_key_exists('carbon_teams.team_code', $argv)) {
            $set .= 'team_code=:team_code,';
        }
        if (array_key_exists('carbon_teams.team_name', $argv)) {
            $set .= 'team_name=:team_name,';
        }
        if (array_key_exists('carbon_teams.team_rank', $argv)) {
            $set .= 'team_rank=:team_rank,';
        }
        if (array_key_exists('carbon_teams.team_sport', $argv)) {
            $set .= 'team_sport=:team_sport,';
        }
        if (array_key_exists('carbon_teams.team_division', $argv)) {
            $set .= 'team_division=:team_division,';
        }
        if (array_key_exists('carbon_teams.team_school', $argv)) {
            $set .= 'team_school=:team_school,';
        }
        if (array_key_exists('carbon_teams.team_district', $argv)) {
            $set .= 'team_district=:team_district,';
        }
        if (array_key_exists('carbon_teams.team_membership', $argv)) {
            $set .= 'team_membership=:team_membership,';
        }
        if (array_key_exists('carbon_teams.team_photo', $argv)) {
            $set .= 'team_photo=UNHEX(:team_photo),';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  team_id=UNHEX('.self::addInjection($primary, $pdo).')';
        

        self::jsonSQLReporting(\func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (array_key_exists('carbon_teams.team_id', $argv)) {
            $team_id = $argv['carbon_teams.team_id'];
            $stmt->bindParam(':team_id',$team_id, 2, 16);
        }
        if (array_key_exists('carbon_teams.team_coach', $argv)) {
            $team_coach = $argv['carbon_teams.team_coach'];
            $stmt->bindParam(':team_coach',$team_coach, 2, 16);
        }
        if (array_key_exists('carbon_teams.parent_team', $argv)) {
            $parent_team = $argv['carbon_teams.parent_team'];
            $stmt->bindParam(':parent_team',$parent_team, 2, 16);
        }
        if (array_key_exists('carbon_teams.team_code', $argv)) {
            $team_code = $argv['carbon_teams.team_code'];
            $stmt->bindParam(':team_code',$team_code, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_name', $argv)) {
            $team_name = $argv['carbon_teams.team_name'];
            $stmt->bindParam(':team_name',$team_name, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_rank', $argv)) {
            $team_rank = $argv['carbon_teams.team_rank'];
            $stmt->bindParam(':team_rank',$team_rank, 2, 11);
        }
        if (array_key_exists('carbon_teams.team_sport', $argv)) {
            $team_sport = $argv['carbon_teams.team_sport'];
            $stmt->bindParam(':team_sport',$team_sport, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_division', $argv)) {
            $team_division = $argv['carbon_teams.team_division'];
            $stmt->bindParam(':team_division',$team_division, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_school', $argv)) {
            $team_school = $argv['carbon_teams.team_school'];
            $stmt->bindParam(':team_school',$team_school, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_district', $argv)) {
            $team_district = $argv['carbon_teams.team_district'];
            $stmt->bindParam(':team_district',$team_district, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_membership', $argv)) {
            $team_membership = $argv['carbon_teams.team_membership'];
            $stmt->bindParam(':team_membership',$team_membership, 2, 225);
        }
        if (array_key_exists('carbon_teams.team_photo', $argv)) {
            $team_photo = $argv['carbon_teams.team_photo'];
            $stmt->bindParam(':team_photo',$team_photo, 2, 16);
        }

        self::bind($stmt);

        if (!$stmt->execute()) {
            return false;
        }
        
        $argv = array_combine(
            array_map(
                static function($k) { return str_replace('carbon_teams.', '', $k); },
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
                JOIN StatsCoach.carbon_teams on c.entity_pk = carbon_teams.team_id';

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
