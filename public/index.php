<?php

// 1. Cargar el autoloader del núcleo
require_once __DIR__ . '/../asrsCore/Autoloader.php';

// 2. Registrar el autoloader
Asrs\Core\Autoloader::register();

// 3. A partir de este momento, cualquier clase del core se carga de forma automática:
// use Asrs\Core\Database;
// $db = Database::getConnection();

// 3. Cargar configuración del entorno
$config = require __DIR__ . '/../asrsCore/config.php';

// 4. Instanciar/invocar el gestor de caché de vistas (crea caches/asrs_views.php si no existe)
$viewsCache = new Asrs\Core\ViewsCacheManager(dirname(__DIR__));
$viewsMap   = $viewsCache->getViews(); // Array asociativo indexado por 'uri'

// 5. Despachar la ruta entrante (Router -> Middleware -> LoadView)
$router = new Asrs\Core\Router($viewsMap, $config);
$router->dispatch();