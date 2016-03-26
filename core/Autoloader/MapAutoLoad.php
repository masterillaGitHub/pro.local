<?php


namespace Core;
require_once 'Autoloader.php';



use Core\Autoloader;

/**
*
*/
class MapAutoLoad extends Autoloader
{

    public function __construct($mapClass)
    {
        $this->register();
        $this->mapClass = $mapClass;
        $this->loadClassMap($mapClass);
    }

    protected function loadClassMap($mapClass)
    {
        echo "<pre>";
        var_dump($mapClass);
        foreach ($mapClass as $loadNameSpace => $pathClass) {
            echo "$loadNameSpace";
            $this->addNamespace($loadNameSpace, $pathClass);
        }
    }
}
