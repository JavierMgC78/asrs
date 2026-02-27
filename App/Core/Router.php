<?php
namespace App\Core;

use App\Controllers\SiteController;

class Router {
    private $routes = [];
    private $views_meta = [];

    public function __construct() {
        // Cargar el caché generado por el RouteCompiler
        $cachePath = __DIR__ . '/../Storage/Cache/routes_cache.php';
        if (file_exists($cachePath)) {
            $this->routes = include $cachePath;
        } else {
            // En caso de que el caché no exista, podríamos lanzar una excepción o compilarlo aquí
            throw new \Exception("Error crítico: No se encontró el archivo de caché de rutas en " . $cachePath);
        }
    }

    public function run() {
        // 1. Obtener la URI actual
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $slug = trim($uri, '/');
        
        // 2. Buscar el slug en la Matriz de Seguridad (Caché)
        if (array_key_exists($slug, $this->routes)) {
            $this->views_meta = $this->routes[$slug];

            $layout= new SiteController();
            $layout->render($this->views_meta);
        }else {
            // Si no se encuentra la ruta, renderizamos el 404
            $this->views_meta = [
                'view_namespace'    => 'Pages',
                'view_controller'   => 'WebSite',
                'view_path'         => 'Pages/WebSite',
                'view'              => '404',
                'layout_path'       => 'Layouts/',
                'layout_namespace'  => 'Site',
                'layout_controller' => 'SiteController',
                'layout'           => 'Site',
                'is_public'         => true,
                'access_level'      => 0,
                'specific_user'     => null,
                'status'            => 1
            ];

            $layout= new SiteController();
            $layout->render($this->views_meta);
        }
    }
}