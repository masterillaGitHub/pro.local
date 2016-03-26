<?php

namespace Core\Models;

use \Core\Utils\DB;
use \PDO;

abstract class EntityModel extends Model
{
    /**
     * Entity id
     * @var int
     */
    protected $_id;
    /**
     * Entity field list
     * @var array
     */
    private $dbFields = [];

    /**
     * Getting object id
     * @return int
     */
    public function ID()
    {
        return $this->_id;
    }
    /**
     * Setting object ID
     * @param int $id
     */
    public function setID($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Fetching object from db by id
     * @param  int $id entity id
     * @return object
     */

    public function save()
    {
        DB::register();
        foreach (get_object_vars($this) as $key => $value) {
            if ($key[0] == '_') {
                $this->dbFields[''.ucwords(substr($key, 1)).''] = $this->{$key};
            }

        }

        if (!empty($this->_id)) {
            $res = DB::update($this->tableName(), $this->dbFields, ['ID' => $this->_id]);
        } else {
            $res = DB::insert($this->tableName(), $this->dbFields);
            $this->_id = DB::get()->lastInsertId();
        }
    }

    public function delete($id = null)
    {
        DB::register();
        $query = "DELETE FROM " . $this->tableName(). " WHERE `ID` = :id";
        $stmt = DB::get()->prepare($query);

        $stmt->bindValue("id", empty($id) ? $this->_id : $id);

        return $stmt->execute();

    }

    public function rawSave()
    {
        DB::register();

        foreach (get_object_vars($this) as $key => $value) {
            if ($key == '_id') {
                $this->dbFields['ID']= $this->{$key};
            } elseif ($key[0] == '_') {
                $this->dbFields[''.ucwords(substr($key, 1)).''] = $this->{$key};
            }

        }

        $res = DB::insert($this->tableName(), $this->dbFields);
        $this->_id = DB::get()->lastInsertId();

    }

    public static function fetchByID($id)
    {
        DB::register();
        $query = "SELECT * FROM " . self::tableName(). " WHERE `ID` = :id";
        $stmt = DB::get()->prepare($query);
        $stmt->bindValue("id", $id);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return self::arrToObj($data);
    }

    public static function fetchByField($field = null, $value = null, $orderField = null, $orderKey = 'ASK', $limit = null)
    {
        $data = [[
                'condition' => 'AND',
                'name' => $field,
                'value' => $value
            ]];

        return parent::search($data, $orderField, $orderKey, $limit)[0];
    }

    public static function fetchAllByField($field = null, $value = null, $orderField = null, $orderKey = 'ASK', $limit = null)
    {
        $data = [[
                'condition' => 'AND',
                'name' => $field,
                'value' => $value
            ]];

        return parent::search($data, $orderField, $orderKey, $limit);
    }


    public static function fetchAll()
    {
        DB::register();
        $query = "SELECT * FROM " . self::tableName();
        $stmt = DB::get()->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $data[] = self::arrToObj($row);
        }

        return $data;
    }
}
