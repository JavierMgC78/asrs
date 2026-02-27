<?php
namespace App\Controllers;

class SiteController {
    private $view_content;
    private $layout_content;  
    private $final_content;  
    
    
    /******************************************************************************************
     * Renderiza una vista dentro del layout definido en los metadatos de la vista.
     * 
     */
    public function render($views_meta) {
        $this->view_content = $this->view_render($views_meta);
    
        $this->final_content = $this->layout_render($views_meta, $this->view_content);
        echo $this->final_content;
    }

    /*******************************************************************************************
     * RENDERIZA EL LAYOUT DE LA VISTA, RECIBIENDO LOS METADATOS DE LA VISTA Y EL CONTENIDO DE LA VISTA RENDERIZADA
     * 
     * @param mixed $views_meta
     * @param mixed $virtual_view
     * @return void
     */
    public function layout_render($views_meta, $view_content){
        $layout_file_path = __DIR__ . "/../Views/". $views_meta['layout_path'] . $views_meta['layout'] . ".php";
        echo "Intentando cargar layout desde: " . $layout_file_path . "<br>";
        if (file_exists($layout_file_path)) {
            //include $layout_file_path;
            // 1. Iniciamos el búfer de salida
            ob_start();

            // 2. Requerimos el layot (su contenido no se mostrará, se irá al búfer)
                include $layout_file_path;

            // 3. Capturamos el contenido del búfer en la variable $view_content y lo limpiamos
                return ob_get_clean();
        }else {
            echo "<h1>Error 404</h1><p>El layout no existe en la ruta: " . $layout_file_path . "</p>";
        }

        
    }
    /****************************************************************************************
     * RENDERIZA LA VISTA DEFINIDA EN LOS METADATOS DE LA RUTA, CAPTURANDO SU CONTENIDO EN UNA VARIABLE PARA LUEGO INYECTARLA EN EL LAYOUT
     * Summary of render_view
     * @param mixed $views_meta
     * @return void
     */
    public function view_render($views_meta): string {

        $view_file_path = __DIR__ . "/../Views/" . $views_meta['view_path'] ."/" . $views_meta['view'] . ".php";  
        if (file_exists($view_file_path)) {    
            //echo "vista encontrada: " . $view_file_path;

            // 1. Iniciamos el búfer de salida
            ob_start();

            // 2. Requerimos la vista (su contenido no se mostrará, se irá al búfer)
                require_once $view_file_path;

            // 3. Capturamos el contenido del búfer en la variable $view_content y lo limpiamos
                $content = ob_get_clean();
                
                return $content;
        } else {
            //AQUI VA LA LOGICA DEL 404 CONFIRMA O REFUTA
            // RETORNAMOS el string de error en lugar de imprimirlo
            return "<h1>Error 404</h1><p>La vista no existe en la ruta: " . $view_file_path . "</p>";
        }
    }
}