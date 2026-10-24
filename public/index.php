<?php
// Kernel ASRS v1.0 - Front controller
require_once __DIR__ . '/../asrsCore/Helpers/global_functions.php';

use asrsCore\Core\Kernel;

(new Kernel())->handleRequest();