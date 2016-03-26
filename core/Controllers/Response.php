<?php

namespace Core\Controllers;

class Response
{
    private function __construct()
    {

    }

    public static function template($viewName, $params = [])
    {
        header("Content-type:text/html");

        $view = new \Core\Views\View();

        $view->render($viewName, $params);
    }

    public static function json($params = [], $reseponseCode = null)
    {
        if ($reseponseCode == null && !is_numeric($reseponseCode)) {
            header('Status: 200 OK');
        } else {
            header('Status: '.$reseponseCode.' OK');
        }
        header('Content-Type: application/json');
        echo json_encode($params);
    }
}
