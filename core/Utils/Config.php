<?php
namespace Core\Utils;


class Config
{

    private static $config = null;

    private function __construct()
    {
    }

    public static function register()
    {
        self::$config = $dbparams = include('dbconfig.php');
    }

    public static function get()
    {
        return self::$config;
    }
}
