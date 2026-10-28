<?php
// Script de un solo uso: registra la ruta admin/apps en asrs_views y recompila cachés.
require_once __DIR__ . '/../../asrsCore/Core/Conection.php';
require_once __DIR__ . '/../../asrsCore/Core/CacheRoutesBuilder.php';
require_once __DIR__ . '/../../asrsCore/Core/CacheRolesBuilder.php';

use asrsCore\Core\Conection;
use asrsCore\Core\CacheRoutesBuilder;
use asrsCore\Core\CacheRolesBuilder;

$pdo = Conection::getInstance();
$ref = $pdo->query("SELECT * FROM asrs_views WHERE app='asrsAdmin' AND uri='admin/roles' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo "REF: " . json_encode($ref) . PHP_EOL;

$exists = $pdo->prepare("SELECT id FROM asrs_views WHERE app='asrsAdmin' AND uri='admin/apps' LIMIT 1");
$exists->execute();
if ($exists->fetchColumn()) {
    echo "Ya existe la ruta admin/apps" . PHP_EOL;
} else {
    $st = $pdo->prepare("INSERT INTO asrs_views (uri, app, type, controller, method, layout, content, roles, status, created_at)
        VALUES ('admin/apps', 'asrsAdmin', 'private', 'AdminController', 'appsIndex', :l, 'apps/index', :r, 1, NOW())");
    $st->execute([':l' => $ref['layout'] ?? 'default', ':r' => json_encode(['superadmin'])]);
    echo "Ruta insertada id=" . $pdo->lastInsertId() . PHP_EOL;
}

foreach ([CacheRoutesBuilder::DEFAULT_CACHE_FILE, CacheRolesBuilder::DEFAULT_CACHE_FILE] as $f) { @unlink($f); }
var_dump(CacheRoutesBuilder::buildIfNotExists(), CacheRolesBuilder::buildIfNotExists());
