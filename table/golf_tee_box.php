<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_tee_box extends Entities implements iRest
{
    const PRIMARY = [
    
    ];

    const COLUMNS = [
    'course_id','tee_box','distance','distance_color','distance_general_slope','distance_general_difficulty','distance_womens_slope','distance_womens_difficulty','distance_out','distance_in','distance_tot',
    ];

    const VALIDATION = [];

    const BINARY = [
    'course_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_tee_box';

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

        

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.golf_tee_box (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES ( UNHEX(:course_id), :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            
                $course_id = $argv['course_id'];
                $stmt->bindParam(':course_id',$course_id, 2, 16);
                    
                $tee_box = $argv['tee_box'];
                $stmt->bindParam(':tee_box',$tee_box, 2, 1);
                    $stmt->bindValue(':distance',$argv['distance'], 2);
                    
                $distance_color = $argv['distance_color'];
                $stmt->bindParam(':distance_color',$distance_color, 2, 10);
                    
                $distance_general_slope = isset($argv['distance_general_slope']) ? $argv['distance_general_slope'] : null;
                $stmt->bindParam(':distance_general_slope',$distance_general_slope, 2, 4);
                    $stmt->bindValue(':distance_general_difficulty',isset($argv['distance_general_difficulty']) ? $argv['distance_general_difficulty'] : null, 2);
                    
                $distance_womens_slope = isset($argv['distance_womens_slope']) ? $argv['distance_womens_slope'] : null;
                $stmt->bindParam(':distance_womens_slope',$distance_womens_slope, 2, 4);
                    $stmt->bindValue(':distance_womens_difficulty',isset($argv['distance_womens_difficulty']) ? $argv['distance_womens_difficulty'] : null, 2);
                    
                $distance_out = isset($argv['distance_out']) ? $argv['distance_out'] : null;
                $stmt->bindParam(':distance_out',$distance_out, 2, 7);
                    
                $distance_in = isset($argv['distance_in']) ? $argv['distance_in'] : null;
                $stmt->bindParam(':distance_in',$distance_in, 2, 7);
                    
                $distance_tot = isset($argv['distance_tot']) ? $argv['distance_tot'] : null;
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
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.golf_tee_box ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (isset($argv['tee_box'])) {
            $set .= 'tee_box=:tee_box,';
        }
        if (isset($argv['distance'])) {
            $set .= 'distance=:distance,';
        }
        if (isset($argv['distance_color'])) {
            $set .= 'distance_color=:distance_color,';
        }
        if (isset($argv['distance_general_slope'])) {
            $set .= 'distance_general_slope=:distance_general_slope,';
        }
        if (isset($argv['distance_general_difficulty'])) {
            $set .= 'distance_general_difficulty=:distance_general_difficulty,';
        }
        if (isset($argv['distance_womens_slope'])) {
            $set .= 'distance_womens_slope=:distance_womens_slope,';
        }
        if (isset($argv['distance_womens_difficulty'])) {
            $set .= 'distance_womens_difficulty=:distance_womens_difficulty,';
        }
        if (isset($argv['distance_out'])) {
            $set .= 'distance_out=:distance_out,';
        }
        if (isset($argv['distance_in'])) {
            $set .= 'distance_in=:distance_in,';
        }
        if (isset($argv['distance_tot'])) {
            $set .= 'distance_tot=:distance_tot,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        

        $stmt = $db->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;


        if (isset($argv['course_id'])) {
            $course_id = 'UNHEX('.$argv['course_id'].')';
            $stmt->bindParam(':course_id', $course_id, 2, 16);
        }
        if (isset($argv['tee_box'])) {
            $tee_box = $argv['tee_box'];
            $stmt->bindParam(':tee_box',$tee_box, 2, 1);
        }
        if (isset($argv['distance'])) {
            $stmt->bindValue(':distance',$argv['distance'], 2);
        }
        if (isset($argv['distance_color'])) {
            $distance_color = $argv['distance_color'];
            $stmt->bindParam(':distance_color',$distance_color, 2, 10);
        }
        if (isset($argv['distance_general_slope'])) {
            $distance_general_slope = $argv['distance_general_slope'];
            $stmt->bindParam(':distance_general_slope',$distance_general_slope, 2, 4);
        }
        if (isset($argv['distance_general_difficulty'])) {
            $stmt->bindValue(':distance_general_difficulty',$argv['distance_general_difficulty'], 2);
        }
        if (isset($argv['distance_womens_slope'])) {
            $distance_womens_slope = $argv['distance_womens_slope'];
            $stmt->bindParam(':distance_womens_slope',$distance_womens_slope, 2, 4);
        }
        if (isset($argv['distance_womens_difficulty'])) {
            $stmt->bindValue(':distance_womens_difficulty',$argv['distance_womens_difficulty'], 2);
        }
        if (isset($argv['distance_out'])) {
            $distance_out = $argv['distance_out'];
            $stmt->bindParam(':distance_out',$distance_out, 2, 7);
        }
        if (isset($argv['distance_in'])) {
            $distance_in = $argv['distance_in'];
            $stmt->bindParam(':distance_in',$distance_in, 2, 7);
        }
        if (isset($argv['distance_tot'])) {
            $distance_tot = $argv['distance_tot'];
            $stmt->bindParam(':distance_tot',$distance_tot, 2, 10);
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
        $sql = 'DELETE FROM statscoach.golf_tee_box ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if (empty($primary)) {
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
        } 

        $remove = null;

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        return self::execute($sql);
    }
}