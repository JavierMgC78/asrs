<?php
namespace App\Controllers;

class LayoutController {
    /**
     * Renderiza la plantilla asociada a la vista solicitada.
     */
    public function layout($layout_path) {
        // Variables que el layout 'site.php' consumirá
        $pathp = $layout_path;
        //$viewPath = __DIR__ . "/../Views/pages/" . $slug . ".php";

        // Carga el layout (el cual hará el include de $viewPath)
        //include __DIR__ . "/../Views/layouts/" . $route['layout'] . ".php";
    }
}