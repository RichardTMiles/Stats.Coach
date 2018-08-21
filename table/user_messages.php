<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class user_messages extends Entities implements iRest
{
    const PRIMARY = [
    'message_id',
    ];

    const COLUMNS = [
    'message_id','from_user_id','to_user_id','message','message_read',
    ];

    const VALIDATION = [];

    const BINARY = [
    'message_id','from_user_id','to_user_id',
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
                    $order .= self::PRIMARY[0] . ' ASC';
                }
            }
            $limit = $order .' '. $limit;
        } else {
            $limit = ' ORDER BY ' . self::PRIMARY[0] . ' ASC LIMIT 100';
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

        $sql = 'SELECT ' .  $sql . ' FROM StatsCoach.user_messages';

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
                    return rtrim($sql, " $join") . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
        } else {
            $primary = $pdo->quote($primary);
            $sql .= ' WHERE  message_id=UNHEX(' . $primary .')';
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

        
        if (empty($primary) && ($argv['pagination']['limit'] ?? false) !== 1 && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
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
        $sql = 'INSERT INTO StatsCoach.user_messages (message_id, from_user_id, to_user_id, message, message_read) VALUES ( UNHEX(:message_id), UNHEX(:from_user_id), UNHEX(:to_user_id), :message, :message_read)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            $message_id = $id = isset($argv['message_id']) ? $argv['message_id'] : self::new_entity('user_messages');
            $stmt->bindParam(':message_id',$message_id, 2, 16);
            
                $from_user_id = isset($argv['from_user_id']) ? $argv['from_user_id'] : null;
                $stmt->bindParam(':from_user_id',$from_user_id, 2, 16);
                    
                $to_user_id = isset($argv['to_user_id']) ? $argv['to_user_id'] : null;
                $stmt->bindParam(':to_user_id',$to_user_id, 2, 16);
                    $stmt->bindValue(':message',$argv['message'], 2);
                    
                $message_read = isset($argv['message_read']) ? $argv['message_read'] : '0';
                $stmt->bindParam(':message_read',$message_read, 0, 1);
        
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
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE StatsCoach.user_messages ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (!empty($argv['message_id'])) {
            $set .= 'message_id=UNHEX(:message_id),';
        }
        if (!empty($argv['from_user_id'])) {
            $set .= 'from_user_id=UNHEX(:from_user_id),';
        }
        if (!empty($argv['to_user_id'])) {
            $set .= 'to_user_id=UNHEX(:to_user_id),';
        }
        if (!empty($argv['message'])) {
            $set .= 'message=:message,';
        }
        if (!empty($argv['message_read'])) {
            $set .= 'message_read=:message_read,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  message_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        global $json;

        if (empty($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        if (!empty($argv['message_id'])) {
            $message_id = $argv['message_id'];
            $stmt->bindParam(':message_id',$message_id, 2, 16);
        }
        if (!empty($argv['from_user_id'])) {
            $from_user_id = $argv['from_user_id'];
            $stmt->bindParam(':from_user_id',$from_user_id, 2, 16);
        }
        if (!empty($argv['to_user_id'])) {
            $to_user_id = $argv['to_user_id'];
            $stmt->bindParam(':to_user_id',$to_user_id, 2, 16);
        }
        if (!empty($argv['message'])) {
            $stmt->bindValue(':message',$argv['message'], 2);
        }
        if (!empty($argv['message_read'])) {
            $message_read = $argv['message_read'];
            $stmt->bindParam(':message_read',$message_read, 0, 1);
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