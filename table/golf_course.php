<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_course extends Entities implements iRest
{
    const PRIMARY = [
    'course_id',
    ];

    const COLUMNS = [
    'course_id','course_name','course_holes','course_phone','course_difficulty','course_rank','course_par','course_par_out','course_par_in','par_tot','course_par_hcp','course_type','course_access','course_handicap','pga_professional','website','course_input_completed','tee_boxes','created_by','handicap_count',
    ];

    const VALIDATION = [];

    const BINARY = [
    'course_id','created_by',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_course';

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
            $sql .= ' WHERE  course_id=UNHEX(' . $primary .')';
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

        
        if (empty($primary) && $argv['pagination']['limit'] !== 1 && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
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
        $sql = 'INSERT INTO statscoach.golf_course (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, course_par, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, course_handicap, pga_professional, website, course_input_completed, tee_boxes, created_by, handicap_count) VALUES ( UNHEX(:course_id), :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :course_par, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :course_handicap, :pga_professional, :website, :course_input_completed, :tee_boxes, UNHEX(:created_by), :handicap_count)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            $course_id = $id = isset($argv['course_id']) ? $argv['course_id'] : self::new_entity('golf_course');
            $stmt->bindParam(':course_id',$course_id, 2, 16);
            
                $course_name = $argv['course_name'];
                $stmt->bindParam(':course_name',$course_name, 2, 16);
                    
                $course_holes = isset($argv['course_holes']) ? $argv['course_holes'] : '18';
                $stmt->bindParam(':course_holes',$course_holes, 2, 2);
                    
                $course_phone = isset($argv['course_phone']) ? $argv['course_phone'] : '';
                $stmt->bindParam(':course_phone',$course_phone, 2, 20);
                    
                $course_difficulty = isset($argv['course_difficulty']) ? $argv['course_difficulty'] : null;
                $stmt->bindParam(':course_difficulty',$course_difficulty, 2, 10);
                    
                $course_rank = isset($argv['course_rank']) ? $argv['course_rank'] : '0';
                $stmt->bindParam(':course_rank',$course_rank, 2, 5);
                    $stmt->bindValue(':course_par',$argv['course_par'], 2);
                    
                $course_par_out = isset($argv['course_par_out']) ? $argv['course_par_out'] : '0';
                $stmt->bindParam(':course_par_out',$course_par_out, 2, 2);
                    
                $course_par_in = isset($argv['course_par_in']) ? $argv['course_par_in'] : '0';
                $stmt->bindParam(':course_par_in',$course_par_in, 2, 2);
                    
                $par_tot = isset($argv['par_tot']) ? $argv['par_tot'] : '0';
                $stmt->bindParam(':par_tot',$par_tot, 2, 2);
                    
                $course_par_hcp = isset($argv['course_par_hcp']) ? $argv['course_par_hcp'] : '0';
                $stmt->bindParam(':course_par_hcp',$course_par_hcp, 2, 4);
                    
                $course_type = isset($argv['course_type']) ? $argv['course_type'] : '';
                $stmt->bindParam(':course_type',$course_type, 2, 30);
                    
                $course_access = isset($argv['course_access']) ? $argv['course_access'] : '';
                $stmt->bindParam(':course_access',$course_access, 2, 10);
                    $stmt->bindValue(':course_handicap',$argv['course_handicap'], 2);
                    
                $pga_professional = isset($argv['pga_professional']) ? $argv['pga_professional'] : '';
                $stmt->bindParam(':pga_professional',$pga_professional, 2, 25);
                    
                $website = isset($argv['website']) ? $argv['website'] : '';
                $stmt->bindParam(':website',$website, 2, 30);
                    
                $course_input_completed = isset($argv['course_input_completed']) ? $argv['course_input_completed'] : '0';
                $stmt->bindParam(':course_input_completed',$course_input_completed, 0, 1);
                    
                $tee_boxes = isset($argv['tee_boxes']) ? $argv['tee_boxes'] : null;
                $stmt->bindParam(':tee_boxes',$tee_boxes, 2, 1);
                    
                $created_by = isset($argv['created_by']) ? $argv['created_by'] : null;
                $stmt->bindParam(':created_by',$created_by, 2, 16);
                    
                $handicap_count = isset($argv['handicap_count']) ? $argv['handicap_count'] : null;
                $stmt->bindParam(':handicap_count',$handicap_count, 2, 1);
        
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

        $sql = 'UPDATE statscoach.golf_course ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (isset($argv['course_name'])) {
            $set .= 'course_name=:course_name,';
        }
        if (isset($argv['course_holes'])) {
            $set .= 'course_holes=:course_holes,';
        }
        if (isset($argv['course_phone'])) {
            $set .= 'course_phone=:course_phone,';
        }
        if (isset($argv['course_difficulty'])) {
            $set .= 'course_difficulty=:course_difficulty,';
        }
        if (isset($argv['course_rank'])) {
            $set .= 'course_rank=:course_rank,';
        }
        if (isset($argv['course_par'])) {
            $set .= 'course_par=:course_par,';
        }
        if (isset($argv['course_par_out'])) {
            $set .= 'course_par_out=:course_par_out,';
        }
        if (isset($argv['course_par_in'])) {
            $set .= 'course_par_in=:course_par_in,';
        }
        if (isset($argv['par_tot'])) {
            $set .= 'par_tot=:par_tot,';
        }
        if (isset($argv['course_par_hcp'])) {
            $set .= 'course_par_hcp=:course_par_hcp,';
        }
        if (isset($argv['course_type'])) {
            $set .= 'course_type=:course_type,';
        }
        if (isset($argv['course_access'])) {
            $set .= 'course_access=:course_access,';
        }
        if (isset($argv['course_handicap'])) {
            $set .= 'course_handicap=:course_handicap,';
        }
        if (isset($argv['pga_professional'])) {
            $set .= 'pga_professional=:pga_professional,';
        }
        if (isset($argv['website'])) {
            $set .= 'website=:website,';
        }
        if (isset($argv['course_input_completed'])) {
            $set .= 'course_input_completed=:course_input_completed,';
        }
        if (isset($argv['tee_boxes'])) {
            $set .= 'tee_boxes=:tee_boxes,';
        }
        if (isset($argv['created_by'])) {
            $set .= 'created_by=UNHEX(:created_by),';
        }
        if (isset($argv['handicap_count'])) {
            $set .= 'handicap_count=:handicap_count,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  course_id=UNHEX(' . $primary .')';

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
        if (isset($argv['course_name'])) {
            $course_name = $argv['course_name'];
            $stmt->bindParam(':course_name',$course_name, 2, 16);
        }
        if (isset($argv['course_holes'])) {
            $course_holes = $argv['course_holes'];
            $stmt->bindParam(':course_holes',$course_holes, 2, 2);
        }
        if (isset($argv['course_phone'])) {
            $course_phone = $argv['course_phone'];
            $stmt->bindParam(':course_phone',$course_phone, 2, 20);
        }
        if (isset($argv['course_difficulty'])) {
            $course_difficulty = $argv['course_difficulty'];
            $stmt->bindParam(':course_difficulty',$course_difficulty, 2, 10);
        }
        if (isset($argv['course_rank'])) {
            $course_rank = $argv['course_rank'];
            $stmt->bindParam(':course_rank',$course_rank, 2, 5);
        }
        if (isset($argv['course_par'])) {
            $stmt->bindValue(':course_par',$argv['course_par'], 2);
        }
        if (isset($argv['course_par_out'])) {
            $course_par_out = $argv['course_par_out'];
            $stmt->bindParam(':course_par_out',$course_par_out, 2, 2);
        }
        if (isset($argv['course_par_in'])) {
            $course_par_in = $argv['course_par_in'];
            $stmt->bindParam(':course_par_in',$course_par_in, 2, 2);
        }
        if (isset($argv['par_tot'])) {
            $par_tot = $argv['par_tot'];
            $stmt->bindParam(':par_tot',$par_tot, 2, 2);
        }
        if (isset($argv['course_par_hcp'])) {
            $course_par_hcp = $argv['course_par_hcp'];
            $stmt->bindParam(':course_par_hcp',$course_par_hcp, 2, 4);
        }
        if (isset($argv['course_type'])) {
            $course_type = $argv['course_type'];
            $stmt->bindParam(':course_type',$course_type, 2, 30);
        }
        if (isset($argv['course_access'])) {
            $course_access = $argv['course_access'];
            $stmt->bindParam(':course_access',$course_access, 2, 10);
        }
        if (isset($argv['course_handicap'])) {
            $stmt->bindValue(':course_handicap',$argv['course_handicap'], 2);
        }
        if (isset($argv['pga_professional'])) {
            $pga_professional = $argv['pga_professional'];
            $stmt->bindParam(':pga_professional',$pga_professional, 2, 25);
        }
        if (isset($argv['website'])) {
            $website = $argv['website'];
            $stmt->bindParam(':website',$website, 2, 30);
        }
        if (isset($argv['course_input_completed'])) {
            $course_input_completed = $argv['course_input_completed'];
            $stmt->bindParam(':course_input_completed',$course_input_completed, 0, 1);
        }
        if (isset($argv['tee_boxes'])) {
            $tee_boxes = $argv['tee_boxes'];
            $stmt->bindParam(':tee_boxes',$tee_boxes, 2, 1);
        }
        if (isset($argv['created_by'])) {
            $created_by = 'UNHEX('.$argv['created_by'].')';
            $stmt->bindParam(':created_by', $created_by, 2, 16);
        }
        if (isset($argv['handicap_count'])) {
            $handicap_count = $argv['handicap_count'];
            $stmt->bindParam(':handicap_count',$handicap_count, 2, 1);
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