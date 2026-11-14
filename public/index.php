<?php

// 1. Cargar el autoloader del núcleo
require_once __DIR__ . '/../asrsCore/Autoloader.php';

// 2. Registrar el autoloader
Asrs\Core\Autoloader::register();

// 3. A partir de este momento, cualquier clase del core se carga de forma automática:
// use Asrs\Core\Database;
// $db = Database::getConnection();