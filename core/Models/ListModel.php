<?php

namespace Core\Models;

use \Core\Utils\DB;

abstract class ListModel extends Model
{
    /**
     * Fetching all objects
     *
     * @return array
     */
    public static function fetchAll()
    {

        $query = "SELECT * FROM " . self::tableName();
        $stmt = DB::get()->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $data = [];
        foreach ($rows as $row) {
            $data[] = self::arrToObj($row);
        }

        return $data;
    }

    public static function fecthByField($field = null, $value = null, $orderField = null, $orderKey = 'ASK', $limit = null)
    {
        return parent::search($field, $value, $orderField, $orderKey, $limit);
    }
}
