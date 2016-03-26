<?php

namespace Core\Routers;


/**
*
*/
class Router
{

    private $routes = [];

    private $controller;

    private $action;

    private $controllerNotFound = 'Home/pageNotFound';

    public $params = [];

    private $pattern = '/[a-zA-Z]*\{[a-zA-Z0-9]*\}/';

    private $requestedUrl;

    private $replace = '[A-z0-9]+';



    public function __construct($routesPath)
    {
        $this->routes = include($routesPath);
        $this->requestedUrl = $this->getUrl();
    }

    public function getUrl()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        if (isset($uri['query'])) {
        } else {
            $params = [];
        }
        return $uri['path'];
    }

    public function run()
    {
        $route = $this->findMatch($this->requestedUrl);
        if ($route === false) {

            $this->controllerConnect($this->controllerNotFound);
            $controller = new $this->controller($this->params); // ?? Зачем здесь параметры
            call_user_func_array([$controller, $this->action], $this->params); // ?? зачем дублируется код
        } else {
            $this->controllerConnect($route);
            $controller = new $this->controller($this->params);
            call_user_func_array([$controller, $this->action], $this->params);
        }
    }

    public function controllerConnect($route)
    {
        list($controllerName, $actionName) = explode('/', $route);

        $controllerName = '\\App\\Controller\\'.$controllerName;

        if (class_exists($controllerName) && method_exists($controllerName, $actionName)) {
            $this->controller = $controllerName;
            $this->action = $actionName;
        } else {
            $controllerNotFound = explode('/', $this->controllerNotFound);
            $pathController = $this->controllerPath.$controllerNotFound[0].'.php';
        }
    }

    public function findMatch($url)
    {
        foreach ($this->routes as $urlPath => $route) {
            $regexPath = $this->parseRegexRoute($urlPath);
            if (preg_match('~'.$regexPath.'~', $url)) {
                $this->parseRegexParam($urlPath, $url);
                return $route;
            }
        }

        return false;
    }

    public function parseRegexRoute($route)
    {
        $regexRoute = preg_replace('/\{[a-zA-Z0-9]+\}/', $this->replace, $route);
        $regexRoute.='$';
        return $regexRoute;
    }

    public function parseRegexParam($route, $url)
    {
        $splitRoutes = explode('/', $route);
        $splitUrl = explode('/', $url);

        if (count($splitRoutes)==count($splitUrl)) {
            for ($i=0; $i < count($splitUrl); $i++) {
                if (preg_match($this->pattern, $splitRoutes[$i])) {
                    $paramName = substr($splitRoutes[$i], strspn($splitRoutes[$i], $splitUrl[$i])+1, -1);
                    $paramVal = substr($splitUrl[$i], strspn($splitUrl[$i], $splitRoutes[$i]));
                    $this->params[$paramName] = $paramVal;
                }
            }
        }

    }
}
