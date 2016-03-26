<?php

namespace Core\Controllers;

class Request
{
    private static $data = [];

    public static function set($type, $value)
    {
        self::$data[$type] = $value;
    }

    public static function get($type)
    {
        return self::$data[$type];
    }
}
