<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;

class user_messages extends Entities implements iRest
{
    const COLUMNS = [
            'message_id',
            'to_user_id',
            'message',
            'message_read',
    ];

    const PRIMARY = "";

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

        $get = $where = [];
        foreach ($argv as $column => $value) {
            if (!is_int($column) && in_array($column, self::COLUMNS)) {
                if ($value !== '') {
                    $where[$column] = $value;
                } else {
                    $get[] = $column;
                }
            } elseif (in_array($value, self::COLUMNS)) {
                $get[] = $value;
            }
        }

        $get =  !empty($get) ? implode(", ", $get) : ' * ';

        $sql = 'SELECT ' .  $get . ' FROM statscoach.user_messages';

        if ($primary === null) {
            $sql .= ' WHERE ';
            foreach ($where as $column => $value) {
                $sql .= "($column = " . Database::database()->quote($value) . ') AND ';
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)){
            $sql .= ' WHERE ' . self::PRIMARY . '=' . Database::database()->quote($primary);
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.user_messages (message_id, to_user_id, message, message_read) VALUES (:message_id, :to_user_id, :message, :message_read)';
        $stmt = Database::database()->prepare($sql);
            $stmt->bindValue(':message_id', isset($argv['message_id']) ? $argv['message_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':to_user_id', isset($argv['to_user_id']) ? $argv['to_user_id'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':message', isset($argv['message']) ? $argv['message'] : null, \PDO::PARAM_STR);
            $stmt->bindValue(':message_read', isset($argv['message_read']) ? $argv['message_read'] : null, \PDO::PARAM_NULL);
        return $stmt->execute();
    }

    /**
    * @param array $return
    * @param string $id
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $id, array $argv) : bool
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
            $set .= 'message_id=:message_id,';
        }
        if (isset($argv['to_user_id'])) {
            $set .= 'to_user_id=:to_user_id,';
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

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['message_id'])) {
            $stmt->bindValue(':message_id', $argv['message_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['to_user_id'])) {
            $stmt->bindValue(':to_user_id', $argv['to_user_id'], \PDO::PARAM_STR);
        }
        if (isset($argv['message'])) {
            $stmt->bindValue(':message', $argv['message'], \PDO::PARAM_STR);
        }
        if (isset($argv['message_read'])) {
            $stmt->bindValue(':message_read', $argv['message_read'], \PDO::PARAM_NULL);
        }


        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        $sql = 'DELETE FROM statscoach.user_messages ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if ($primary === null) {
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
                $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)) {
            $sql .= ' WHERE ' . self::PRIMARY . '=' . Database::database()->quote($primary);
        }

        $remove = null;

        return self::execute($sql);
    }

}