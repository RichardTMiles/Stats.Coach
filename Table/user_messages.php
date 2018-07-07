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
    'message_id','to_user_id','message','message_read',
    ];

    const BINARY = [
    'message_id','to_user_id',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.user_messages';

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
            $sql .= ' WHERE  message_id=UNHEX(' . $primary .')';
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
        $sql = 'INSERT INTO statscoach.user_messages (message_id, to_user_id, message, message_read) VALUES ( UNHEX(:message_id), :to_user_id, :message, :message_read)';
        $stmt = Database::database()->prepare($sql);
            $message_id = $id = isset($argv['message_id']) ? $argv['message_id'] : self::new_entity('user_messages');
            $stmt->bindParam(':message_id',$message_id, \PDO::PARAM_STR, 16);
            
                $to_user_id = isset($argv['to_user_id']) ? $argv['to_user_id'] : null;
                $stmt->bindParam(':to_user_id',$to_user_id, \PDO::PARAM_STR, 16);
                    $stmt->bindValue(':message',$argv['message'], \PDO::PARAM_STR);
                    
                $message_read = isset($argv['message_read']) ? $argv['message_read'] : '0';
                $stmt->bindParam(':message_read',$message_read, \PDO::PARAM_NULL, 1);
        
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

        $sql = 'UPDATE statscoach.user_messages ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['message_id'])) {
            $set .= 'message_id=UNHEX(:message_id),';
        }
        if (isset($argv['to_user_id'])) {
            $set .= 'to_user_id=UNHEX(:to_user_id),';
        }
        if (isset($argv['message'])) {
            $set .= 'message=:message,';
        }
        if (isset($argv['message_read'])) {
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

        if (isset($argv['message_id'])) {
            $message_id = 'UNHEX('.$argv['message_id'].')';
            $stmt->bindParam(':message_id', $message_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['to_user_id'])) {
            $to_user_id = 'UNHEX('.$argv['to_user_id'].')';
            $stmt->bindParam(':to_user_id', $to_user_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['message'])) {
            $stmt->bindValue(':message',$argv['message'], \PDO::PARAM_STR);
        }
        if (isset($argv['message_read'])) {
            $message_read = $argv['message_read'];
            $stmt->bindParam(':message_read',$message_read, \PDO::PARAM_NULL, 1);
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