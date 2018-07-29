<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_tournaments extends Entities implements iRest
{
    const PRIMARY = [
    'tournament_id',
    ];

    const COLUMNS = [
    'tournament_id','tournament_name','course_id','host_name','tournament_style','tournament_team_price','tournament_paid','tournament_date',
    ];

    const BINARY = [
    'tournament_id','tournament_name','course_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_tournaments';

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
        } else {
            $primary = $pdo->quote($primary);
            $sql .= ' WHERE  tournament_id=UNHEX(' . $primary .')';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        if ($primary === null && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
            $return = [$return];
        }        if ($primary === null && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
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
        $sql = 'INSERT INTO statscoach.golf_tournaments (tournament_id, tournament_name, course_id, host_name, tournament_style, tournament_team_price, tournament_paid, tournament_date) VALUES ( UNHEX(:tournament_id), :tournament_name, :course_id, :host_name, :tournament_style, :tournament_team_price, :tournament_paid, :tournament_date)';
        $stmt = Database::database()->prepare($sql);
            $tournament_id = $id = isset($argv['tournament_id']) ? $argv['tournament_id'] : self::new_entity('golf_tournaments');
            $stmt->bindParam(':tournament_id',$tournament_id, \PDO::PARAM_STR, 16);
            
                $tournament_name = $argv['tournament_name'];
                $stmt->bindParam(':tournament_name',$tournament_name, \PDO::PARAM_STR, 16);
                    
                $course_id = isset($argv['course_id']) ? $argv['course_id'] : null;
                $stmt->bindParam(':course_id',$course_id, \PDO::PARAM_STR, 16);
                    
                $host_name = $argv['host_name'];
                $stmt->bindParam(':host_name',$host_name, \PDO::PARAM_STR, 225);
                    
                $tournament_style = $argv['tournament_style'];
                $stmt->bindParam(':tournament_style',$tournament_style, \PDO::PARAM_STR, 11);
                    
                $tournament_team_price = isset($argv['tournament_team_price']) ? $argv['tournament_team_price'] : null;
                $stmt->bindParam(':tournament_team_price',$tournament_team_price, \PDO::PARAM_STR, 11);
                    
                $tournament_paid = isset($argv['tournament_paid']) ? $argv['tournament_paid'] : '1';
                $stmt->bindParam(':tournament_paid',$tournament_paid, \PDO::PARAM_STR, 1);
                    $stmt->bindValue(':tournament_date',isset($argv['tournament_date']) ? $argv['tournament_date'] : null, \PDO::PARAM_STR);
        
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

        $sql = 'UPDATE statscoach.golf_tournaments ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['tournament_id'])) {
            $set .= 'tournament_id=UNHEX(:tournament_id),';
        }
        if (isset($argv['tournament_name'])) {
            $set .= 'tournament_name=UNHEX(:tournament_name),';
        }
        if (isset($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (isset($argv['host_name'])) {
            $set .= 'host_name=:host_name,';
        }
        if (isset($argv['tournament_style'])) {
            $set .= 'tournament_style=:tournament_style,';
        }
        if (isset($argv['tournament_team_price'])) {
            $set .= 'tournament_team_price=:tournament_team_price,';
        }
        if (isset($argv['tournament_paid'])) {
            $set .= 'tournament_paid=:tournament_paid,';
        }
        if (isset($argv['tournament_date'])) {
            $set .= 'tournament_date=:tournament_date,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  tournament_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        if (isset($argv['tournament_id'])) {
            $tournament_id = 'UNHEX('.$argv['tournament_id'].')';
            $stmt->bindParam(':tournament_id', $tournament_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['tournament_name'])) {
            $tournament_name = 'UNHEX('.$argv['tournament_name'].')';
            $stmt->bindParam(':tournament_name', $tournament_name, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['course_id'])) {
            $course_id = 'UNHEX('.$argv['course_id'].')';
            $stmt->bindParam(':course_id', $course_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['host_name'])) {
            $host_name = $argv['host_name'];
            $stmt->bindParam(':host_name',$host_name, \PDO::PARAM_STR, 225);
        }
        if (isset($argv['tournament_style'])) {
            $tournament_style = $argv['tournament_style'];
            $stmt->bindParam(':tournament_style',$tournament_style, \PDO::PARAM_STR, 11);
        }
        if (isset($argv['tournament_team_price'])) {
            $tournament_team_price = $argv['tournament_team_price'];
            $stmt->bindParam(':tournament_team_price',$tournament_team_price, \PDO::PARAM_STR, 11);
        }
        if (isset($argv['tournament_paid'])) {
            $tournament_paid = $argv['tournament_paid'];
            $stmt->bindParam(':tournament_paid',$tournament_paid, \PDO::PARAM_STR, 1);
        }
        if (isset($argv['tournament_date'])) {
            $stmt->bindValue(':tournament_date',$argv['tournament_date'], \PDO::PARAM_STR);
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