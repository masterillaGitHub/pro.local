<?php

namespace Core\Controllers;

use \Core\Models\Entity\Users;

/**
*
*/
class Controller
{

    protected $autification = false;


    public function __construct($params = null)
    {
        if ($this->autification == true) {
            // $this->checkAuntif();
        }
        Request::set('url', $params);
        Request::set('request', $_POST);
        Request::set('query', $_GET);
    }

    public function autification()
    {
        return $this->autification;
    }

    public function checkAuntif()
    {

        if (!isset($_COOKIE['user']) && empty($_COOKIE['user'])) {
                $this->redirect();
        } else {
            if (!isset($_SESSION['user']) && empty($_SESSION['user'])) {
                $_SESSION['user'] = $_COOKIE['user'] ;
            }
        }
    }

    public function redirect()
    {
        $str = 'Location: http://train.local/login';
        header($str);
    }

    public function rememberUser()
    {
        if ($this->checkAuntif()) {
            $user = unserialize($_SESSION['user']);
            setcookie('user', serialize($user), time()+60*60*24*30, '/');
        } else {
            $this->redirect();
        }
    }

    public function getUser()
    {
        if ($this->checkAuntif()) {
            $user = unserialize($_COOKIE['user']);
            return $user;
        }
    }
}
