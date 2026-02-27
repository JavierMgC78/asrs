<?php
// public_html/index.php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\RouteCompiler;
use App\Core\Router;

$compiler = new RouteCompiler();
if ($compiler->compile()) {
    //echo "¡ASRS funcionando con Composer y Autoload!";
}

$router = new Router();
$router->run();

/*
$compiler = new RouteCompiler();
if ($compiler->compile()) {
    echo "¡ASRS funcionando con Composer y Autoload!";
}
*/