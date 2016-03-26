<?php

/**
 * This file is part of the  Silex Sceleton package.
 *
 * Wrote by Volodymyr Panenko <rem.kvant@gmail.com>
 *
 */

namespace Core\Models;

use \Core\Utils\DB;
use PDO;

/**
 * This class connects business objects and database tables to create a persistable domain
 * model where logic and data are presented in one wrapping.
 *
 * It‘s an implementation of the object-relational mapping (ORM). A model represents the
 * information (data) of the * application and the rules to manipulate that data. Models are
 * primarily used for managing the rules of interaction with a corresponding database table.
 *
 * In most cases, each table in database will correspond to one model in application.
 * The bulk of your application’s business logic will be concentrated in the models.
 *
 * @author Vovanushka <rem.kvant@gmail.com>
 *
 * @package Silex Sceleton
 */

abstract class Model
{
    protected static function tableName()
    {
        return end(explode('\\', get_called_class()));
    }

    /**
     * Trunsforms array to obj
     *
     * @param  array $arr
     * @return object
     */
    protected static function arrToObj(array $arr)
    {
        $n = '\Core\Models\Entity\\'.self::tableName();
        $ob = new $n;
        foreach ($arr as $key => $value) {
            if ($key == 'ID') {
                $ob->setID($value);
            } else {
                $ob->{'_' . lcfirst($key)} = $value;
            }
        }
        return $ob;
    }

    /**
     * Fetching object by params
     *
     * @param  null|string $field      Field name
     * @param  null|string $value      Fired value
     * @param  null|string $orderField Field name to order
     * @param  string      $orderKey   Ordering key ASK OR DESK
     * @param  null|string $limit      Limit count
     * @return array
     */
    protected static function search($data = [], $orderField = null, $orderKey = 'ASK', $limit = null)
    {
        $qOrder = '';
        if (!empty($orderField)) {
            $qOrder = "ORDER BY $orderField $orderKey";
        }

        $qLimit = '';
        if (!empty($limit)) {
            $qLimit = "LIMIT 0, $limit";
        }

        $qFilter = '';
        if (!empty($data)) {
            foreach ($data as $key => $field) {
                $qFilter .= $field['condition']." (`" . $field['name'] . "` = :value".$key.") ";
            }
        }

        $query = "SELECT * FROM `" . self::tableName() . "` WHERE 1 $qFilter $qOrder $qLimit";
        $stmt = DB::register();
        $stmt = DB::get()->prepare($query);
        foreach ($data as $key => $field) {
            $stmt->bindValue("value".$key, $field['value']);
        }

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];

        foreach ($rows as $row) {
            $data[] = self::arrToObj($row);
        }

        return $data;
    }
}
