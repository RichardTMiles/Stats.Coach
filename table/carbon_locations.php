<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_locations extends Entities implements iRest
{
    const PRIMARY = [
    'entity_id',
    ];

    const COLUMNS = [
    'entity_id','latitude','longitude','street','city','state','elevation',
    ];

    const VALIDATION = [];

    const BINARY = [
    'entity_id',
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
                $pos = strrpos($argv['pagination']['limit'], "><");
                if ($pos !== false) { // note: three equal signs
                    substr_replace($argv['pagination']['limit'],',',$pos, 2);
                }
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }
        } else {
            $limit = ' LIMIT 100';
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_locations';

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
            $sql .= ' WHERE  entity_id=UNHEX(' . $primary .')';
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

        
        if (empty($primary) && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
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
        $sql = 'INSERT INTO statscoach.carbon_locations (entity_id, latitude, longitude, street, city, state, elevation) VALUES ( UNHEX(:entity_id), :latitude, :longitude, :street, :city, :state, :elevation)';
        $stmt = sDatabaseelf::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            $entity_id = $id = isset($argv['entity_id']) ? $argv['entity_id'] : self::new_entity('carbon_locations');
            $stmt->bindParam(':entity_id',$entity_id, 2, 16);
            
                $latitude = isset($argv['latitude']) ? $argv['latitude'] : null;
                $stmt->bindParam(':latitude',$latitude, 2, 225);
                    
                $longitude = isset($argv['longitude']) ? $argv['longitude'] : null;
                $stmt->bindParam(':longitude',$longitude, 2, 225);
                    $stmt->bindValue(':street',$argv['street'], \2);
                    
                $city = isset($argv['city']) ? $argv['city'] : null;
                $stmt->bindParam(':city',$city, 2, 40);
                    
                $state = isset($argv['state']) ? $argv['state'] : null;
                $stmt->bindParam(':state',$state, 2, 10);
                    
                $elevation = isset($argv['elevation']) ? $argv['elevation'] : null;
                $stmt->bindParam(':elevation',$elevation, 2, 40);
        
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

        $sql = 'UPDATE statscoach.carbon_locations ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['entity_id'])) {
            $set .= 'entity_id=UNHEX(:entity_id),';
        }
        if (isset($argv['latitude'])) {
            $set .= 'latitude=:latitude,';
        }
        if (isset($argv['longitude'])) {
            $set .= 'longitude=:longitude,';
        }
        if (isset($argv['street'])) {
            $set .= 'street=:street,';
        }
        if (isset($argv['city'])) {
            $set .= 'city=:city,';
        }
        if (isset($argv['state'])) {
            $set .= 'state=:state,';
        }
        if (isset($argv['elevation'])) {
            $set .= 'elevation=:elevation,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  entity_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;


        if (isset($argv['entity_id'])) {
            $entity_id = 'UNHEX('.$argv['entity_id'].')';
            $stmt->bindParam(':entity_id', $entity_id, 2, 16);
        }
        if (isset($argv['latitude'])) {
            $latitude = $argv['latitude'];
            $stmt->bindParam(':latitude',$latitude, 2, 225);
        }
        if (isset($argv['longitude'])) {
            $longitude = $argv['longitude'];
            $stmt->bindParam(':longitude',$longitude, 2, 225);
        }
        if (isset($argv['street'])) {
            $stmt->bindValue(':street',$argv['street'], 2);
        }
        if (isset($argv['city'])) {
            $city = $argv['city'];
            $stmt->bindParam(':city',$city, 2, 40);
        }
        if (isset($argv['state'])) {
            $state = $argv['state'];
            $stmt->bindParam(':state',$state, 2, 10);
        }
        if (isset($argv['elevation'])) {
            $elevation = $argv['elevation'];
            $stmt->bindParam(':elevation',$elevation, 2, 40);
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