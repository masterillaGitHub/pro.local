<?php
namespace Core\Utils;

use \PDO;

class DB
{

    private static $db = null;


    private function __construct()
    {
    }

    public static function register()
    {
        $dbConf = include('dbconfig.php');
        self::$db = new PDO('mysql:host='.$dbConf['dbhost'].';dbname='.$dbConf['dbname'].';charset='.$dbConf['dbcharset'], $dbConf['dbuser'], $dbConf['dbpass']);
    }


    public static function insert($tableName, $fields = [])
    {

        $sql = "INSERT INTO " . $tableName . " (" . implode(array_keys($fields), ", ") . ") VALUES (:" . implode(array_keys($fields), ", :") . ");";

        $dbstmnt = self::get()->prepare($sql);

        foreach ($fields as $field => &$value) {
            $dbstmnt->bindParam(':'.$field, $value);
        }

        if ($dbstmnt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function update($tableName, $fields, $find)
    {
        $sql = 'UPDATE '.$tableName.' SET ';

        foreach ($fields as $name => $value) {
            if ($name == 'Id') {
                continue;
            }
            $sql.= '`'.$name.'` = :'.$name.',';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ' WHERE ';

        foreach ($find as $name => $value) {
            $sql.= $name.' = \''.$value.'\' ';
        }
        $dbstmnt = self::get()->prepare($sql);

        foreach ($fields as $field => $value) {
            if ($field == 'Id') {
                continue;
            }
            $dbstmnt->bindValue(':'.$field, $value);
        }

        if ($dbstmnt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function lastInsertId()
    {
        return self::$db->lastInsertId();
    }

    public static function get()
    {
        return self::$db;
    }
}
