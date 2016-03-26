<?php

require_once ROOT_DIR.'core/Autoloader/Autoloader.php';


$autoload = new \Core\AutoLoader();


$autoload->register();

$autoload->addNamespace('Core\\', ROOT_DIR.'core');
$autoload->addNamespace('App\\', ROOT_DIR.'src');
