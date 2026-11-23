<?php

declare(strict_types=1);

namespace Asrs\Core;

/**
 * Middleware encargado de validar el acceso a la vista según `is_public`
 * y delegar el renderizado a LoadView cuando está permitido.
 */
class Middleware
{
    private LoadView $loadView;

    /**
     * @param LoadView|null $loadView Instancia de LoadView (opcional para inyección/pruebas).
     */
    public function __construct(?LoadView $loadView = null)
    {
        $this->loadView = $loadView ?? new LoadView();
    }

    /**
     * Evalúa si la ruta es pública (`is_public === 1`).
     * En esta etapa las vistas privadas (`is_public === 0`) se bloquean de forma segura.
     *
     * @param array<string, mixed> $route Registro de ruta resuelto por Router.
     *
     * @return bool True si se permitió y renderizó la vista; false si se bloqueó el acceso.
     */
    public function handle(array $route): bool
    {
        $isPublic = (int)($route['is_public'] ?? 0);

        if ($isPublic !== 1) {
            if (!headers_sent()) {
                http_response_code(403);
            }
            echo 'Acceso denegado: la vista solicitada es privada.';
            return false;
        }

        $this->loadView->render($route);

        return true;
    }
}
