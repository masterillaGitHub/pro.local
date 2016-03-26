<?php
/*=== Распечатка переменных в удобной форме ===*/
// deBug($class, '$deBug', 1, 0, 0, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
function deBug($variable, $title, $dump, $echo, $stop, $file, $line, $method)
{
    if ($variable === 'debugBacktrace') {
        $variable = debug_backtrace();
        $title = 'debugBacktrace()';
    }
    echo '<div style="margin:10px; border: 1px dotted; padding: 3px;">';
    static $num = 0;
    $num++;
    if ($title) {
        echo '<br>File ('.$file.')<br>Line ('.$line.')<br>Class & Method ('.$method.')<br>Title: <b>'.$title.'</b> №'.$num.':<br>';
    }
    echo '<pre>';
    if (!$dump) {
        if (!$echo && (is_array($variable) || is_object($variable))) {
            print_r($variable);
        } else {
            echo $variable;
        }
    } else {
        var_dump($variable);
    }
    echo '</pre></div>';
    if ($stop) {
        exit;
    }
}

/*=== Распечатка переменных в удобной форме ===*/


session_start();
define('ROOT_DIR', __DIR__.'/../');

include_once ROOT_DIR.'core/autoload.php';


$router  = new Core\Routers\Router(ROOT_DIR.'config.php');
deBug('end', 'end', 0, 0, 1, __FILE__, __LINE__, __METHOD__); // [debugBacktrace], dump, echo, stop
$router->run();
