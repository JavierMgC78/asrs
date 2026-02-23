<h1>Cargando Index.php en public_html</h1>
<?php
// Temporalmente cargamos las clases a mano hasta instalar Composer en la Entrada 3
require_once __DIR__ . '/../App/Core/Database.php';
require_once __DIR__ . '/../App/Core/RouteCompiler.php';

use App\Core\RouteCompiler;

// --- INICIO DE LÓGICA DE AUTO-GENERACIÓN ---
// En desarrollo, compilamos el caché en cada carga para ver reflejados los cambios de la DB
$compiler = new RouteCompiler();

if (!$compiler->compile()) {
    // Si falla la creación del caché, mostramos un error para corregir permisos
    die("Error crítico: No se pudo generar el archivo de caché en App/Storage/Cache/");
}
// --- FIN DE LÓGICA DE AUTO-GENERACIÓN ---

// Aquí continuaría tu lógica del Router...
echo "Sistema ASRS: Caché de rutas actualizado correctamente.";


