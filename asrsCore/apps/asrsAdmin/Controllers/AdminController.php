<?php
namespace asrsCore\apps\asrsAdmin\Controllers;

use asrsCore\Core\Conection;
use asrsCore\Core\Security;
use asrsCore\Core\CacheRoutesBuilder;
use asrsCore\Core\CacheRolesBuilder;
use asrsCore\Core\CacheAppsBuilder;
use PDO;
use Throwable;

class AdminController
{
    /**
     * Catálogo completo de rutas y métricas de caché del sistema ASRS.
     * Acceso exclusivo para usuarios con privilegios 'superadmin'.
     *
     * @return array Matriz de rutas, estado de artefactos de caché y usuario.
     */
    public function routesIndex(): array
    {
        // 1. Verificación perimetral de superadmin
        $user = Security::authenticate();
        if ($user === null || !in_array(Security::ADMIN_ROLE, (array)($user['roles'] ?? []), true)) {
            if (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json' || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                http_response_code(403);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['ok' => false, 'error' => Security::DENIED_MESSAGE]);
                exit;
            }
        }

        // 2. Consulta del catálogo de rutas en BD
        $routes = [];
        try {
            $pdo = Conection::getInstance();
            $stmt = $pdo->query(
                "SELECT id, uri, app, type, controller, method, layout, content, roles, css_path_view, js_path_view, status, created_at
                 FROM asrs_views
                 ORDER BY app ASC, uri ASC"
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $decodedRoles = !empty($row['roles']) ? json_decode($row['roles'], true) : [];
                $row['roles_array'] = is_array($decodedRoles) ? $decodedRoles : [];
                $routes[] = $row;
            }
        } catch (Throwable $e) {
            // Respaldo desde archivo compilado de rutas O(1) si la BD no está disponible
            $cacheRoutesFile = dirname(__DIR__, 3) . '/Cache/routes.php';
            if (is_file($cacheRoutesFile)) {
                $cachedRoutes = require $cacheRoutesFile;
                foreach ($cachedRoutes as $key => $r) {
                    if (str_starts_with($key, '@') || !is_array($r)) continue;
                    $routes[] = [
                        'id'            => '-',
                        'uri'           => $r['uri'] ?? '',
                        'app'           => $r['app'] ?? '',
                        'type'          => $r['type'] ?? 'public',
                        'controller'    => $r['controller'] ?? '',
                        'method'        => $r['method'] ?? '',
                        'layout'        => $r['layout'] ?? '',
                        'content'       => $r['content'] ?? '',
                        'roles'         => json_encode($r['roles'] ?? []),
                        'roles_array'   => (array)($r['roles'] ?? []),
                        'status'        => 1,
                        'created_at'    => '-',
                    ];
                }
            }
        }

        // 3. Métricas de estado de los artefactos compilados de caché
        $cacheDir = dirname(__DIR__, 3) . '/Cache';
        $routesFile = $cacheDir . '/routes.php';
        $rolesFile  = $cacheDir . '/roles.php';
        $appsFile   = $cacheDir . '/apps.php';

        $routesCount = is_file($routesFile)
            ? count(array_filter(array_keys(require $routesFile), fn($k) => is_string($k) && $k[0] !== '@'))
            : 0;

        $rolesCount = 0;
        if (is_file($rolesFile)) {
            $rolesData = require $rolesFile;
            $rolesCount = count($rolesData['roles'] ?? []);
        }

        $appsCount = is_file($appsFile) ? count(require $appsFile) : 0;

        $cacheMetrics = [
            'routes' => [
                'file'        => 'routes.php',
                'title'       => 'RUTAS COMPILED O(1)',
                'path'        => 'asrsCore/Cache/routes.php',
                'exists'      => is_file($routesFile),
                'size'        => is_file($routesFile) ? filesize($routesFile) : 0,
                'mtime'       => is_file($routesFile) ? filemtime($routesFile) : null,
                'count'       => $routesCount,
                'status'      => is_file($routesFile) ? 'ACTIVO' : 'SIN COMPILAR',
                'builder'     => 'CacheRoutesBuilder.php',
                'description' => 'Búsqueda O(1) indexada por app|uri',
            ],
            'roles' => [
                'file'        => 'roles.php',
                'title'       => 'MATRIZ ACL & ROLES',
                'path'        => 'asrsCore/Cache/roles.php',
                'exists'      => is_file($rolesFile),
                'size'        => is_file($rolesFile) ? filesize($rolesFile) : 0,
                'mtime'       => is_file($rolesFile) ? filemtime($rolesFile) : null,
                'count'       => $rolesCount,
                'status'      => is_file($rolesFile) ? 'ACTIVO' : 'SIN COMPILAR',
                'builder'     => 'CacheRolesBuilder.php',
                'description' => 'Roles institucionales y control de acceso',
            ],
            'apps' => [
                'file'        => 'apps.php',
                'title'       => 'CATÁLOGO DE APPS',
                'path'        => 'asrsCore/Cache/apps.php',
                'exists'      => is_file($appsFile),
                'size'        => is_file($appsFile) ? filesize($appsFile) : 0,
                'mtime'       => is_file($appsFile) ? filemtime($appsFile) : null,
                'count'       => $appsCount,
                'status'      => is_file($appsFile) ? 'ACTIVO' : 'SIN COMPILAR',
                'builder'     => 'CacheAppsBuilder.php',
                'description' => 'Subsistemas perimetrales registrados',
            ],
        ];

        $payload = [
            'ok'           => true,
            'currentUser'  => $user,
            'routes'       => $routes,
            'cacheMetrics' => $cacheMetrics,
            'totalRoutes'  => count($routes),
            'timestamp'    => time(),
        ];

        // Respuesta en JSON si se solicita explícitamente vía API
        if (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json') {
            $this->json($payload);
        }

        return $payload;
    }

    /**
     * Endpoint POST para registrar una nueva ruta en asrs_views y recompilar la caché routes.php.
     * Acceso exclusivo para superadministradores.
     */
    public function storeRoute(): never
    {
        // 1. Verificación de método POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        // 2. Verificación de sesión de superadmin
        $user = Security::authenticate();
        if ($user === null || !in_array(Security::ADMIN_ROLE, (array)($user['roles'] ?? []), true)) {
            $this->json(['ok' => false, 'error' => Security::DENIED_MESSAGE], 403);
        }

        // 3. Obtener payload (JSON o form-data)
        $input = [];
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decodedJson = json_decode($raw, true);
            if (is_array($decodedJson)) {
                $input = $decodedJson;
            }
        }
        if (empty($input)) {
            $input = $_POST;
        }

        $uri        = trim((string)($input['uri'] ?? ''), " \t\n\r\0\x0B/");
        $app        = trim((string)($input['app'] ?? 'asrsAdmin'));
        $type       = in_array($input['type'] ?? '', ['public', 'private'], true) ? $input['type'] : 'private';
        $controller = trim((string)($input['controller'] ?? ''));
        $method     = trim((string)($input['method'] ?? ''));
        $layout     = trim((string)($input['layout'] ?? 'default'));
        $content    = trim((string)($input['content'] ?? ''));
        $rolesInput = $input['roles'] ?? [];

        if ($content === '') {
            $content = $uri !== '' ? basename($uri) : 'inicio';
        }
        if ($layout === '') {
            $layout = 'default';
        }

        // Procesar y validar array de roles
        $rolesArray = [];
        if (is_string($rolesInput)) {
            $decoded = json_decode($rolesInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rolesArray = $decoded;
            } else {
                $rolesArray = array_values(array_filter(array_map('trim', explode(',', $rolesInput))));
            }
        } elseif (is_array($rolesInput)) {
            $rolesArray = $rolesInput;
        }

        // Por seguridad, si es privada y no se definió rol, asignar superadmin
        if ($type === 'private' && empty($rolesArray)) {
            $rolesArray = [Security::ADMIN_ROLE];
        }

        $rolesJson = !empty($rolesArray) ? json_encode($rolesArray, JSON_UNESCAPED_SLASHES) : null;

        try {
            $pdo = Conection::getInstance();

            // Verificar si ya existe registro para app y uri
            $check = $pdo->prepare("SELECT id FROM asrs_views WHERE app = :a AND uri = :u LIMIT 1");
            $check->execute([':a' => $app, ':u' => $uri]);
            $existingId = $check->fetchColumn();

            if ($existingId) {
                $stmt = $pdo->prepare(
                    "UPDATE asrs_views
                        SET type = :t, controller = :c, method = :m, layout = :l, content = :cnt, roles = :r, status = 1
                      WHERE id = :id"
                );
                $stmt->execute([
                    ':t'   => $type,
                    ':c'   => $controller,
                    ':m'   => $method,
                    ':l'   => $layout,
                    ':cnt' => $content,
                    ':r'   => $rolesJson,
                    ':id'  => $existingId,
                ]);
                $routeId = (int)$existingId;
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO asrs_views (uri, app, type, controller, method, layout, content, roles, status, created_at)
                     VALUES (:u, :a, :t, :c, :m, :l, :cnt, :r, 1, NOW())"
                );
                $stmt->execute([
                    ':u'   => $uri,
                    ':a'   => $app,
                    ':t'   => $type,
                    ':c'   => $controller,
                    ':m'   => $method,
                    ':l'   => $layout,
                    ':cnt' => $content,
                    ':r'   => $rolesJson,
                ]);
                $routeId = (int)$pdo->lastInsertId();
            }

            // 4. Recompilación inmediata física del archivo de caché routes.php
            $cacheFile = CacheRoutesBuilder::DEFAULT_CACHE_FILE;
            @unlink($cacheFile);
            $cacheOk = CacheRoutesBuilder::buildIfNotExists($cacheFile);

            $newRoute = [
                'id'          => $routeId,
                'uri'         => $uri,
                'app'         => $app,
                'type'        => $type,
                'controller'  => $controller,
                'method'      => $method,
                'layout'      => $layout,
                'content'     => $content,
                'roles'       => $rolesJson,
                'roles_array' => $rolesArray,
                'status'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
            ];

            // Conteo actualizado de rutas en caché compilado
            $routesCount = is_file($cacheFile)
                ? count(array_filter(array_keys(require $cacheFile), fn($k) => is_string($k) && $k[0] !== '@'))
                : 0;

            $this->json([
                'ok'          => true,
                'message'     => "Ruta '{$uri}' guardada y caché recompilada exitosamente.",
                'route'       => $newRoute,
                'routesCount' => $routesCount,
                'cacheOk'     => $cacheOk,
            ], 201);

        } catch (Throwable $e) {
            $this->json([
                'ok'    => false,
                'error' => 'Fallo al persistir la ruta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Control perimetral para el Gestor de Usuarios.
     * Valida de forma estricta que el usuario activo posea el rol 'superadmin'.
     *
     * @return array Datos de sesión y contexto del módulo.
     */
    public function usersIndex(): array
    {
        // Si la petición es POST, delegar al endpoint de almacenamiento
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $this->storeUser();
        }

        $user = Security::authenticate();
        if ($user === null || !in_array(Security::ADMIN_ROLE, (array)($user['roles'] ?? []), true)) {
            if (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json' || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                $this->json(['ok' => false, 'error' => Security::DENIED_MESSAGE], 403);
            }
            header('Location: ' . Security::url(Security::LOGIN_URI), true, 302);
            exit;
        }

        $payload = [
            'ok'          => true,
            'currentUser' => $user,
            'title'       => 'GESTOR DE USUARIOS',
            'timestamp'   => time(),
        ];

        if (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json') {
            $this->json($payload);
        }

        return $payload;
    }

    /**
     * Endpoint POST para registrar un nuevo usuario en la tabla asrs_users
     * y asociar su rol institucional en asrs_user_roles.
     * Acceso exclusivo para superadministradores.
     */
    public function storeUser(): never
    {
        // 1. Verificación de método POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        // 2. Verificación de sesión de superadmin
        $user = Security::authenticate();
        if ($user === null || !in_array(Security::ADMIN_ROLE, (array)($user['roles'] ?? []), true)) {
            $this->json(['ok' => false, 'error' => Security::DENIED_MESSAGE], 403);
        }

        // 3. Obtener payload (JSON o form-data)
        $input = [];
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decodedJson = json_decode($raw, true);
            if (is_array($decodedJson)) {
                $input = $decodedJson;
            }
        }
        if (empty($input)) {
            $input = $_POST;
        }

        $name     = trim((string)($input['name'] ?? ''));
        $email    = trim((string)($input['email'] ?? ''));
        $role     = trim((string)($input['role'] ?? 'student'));
        $password = (string)($input['password'] ?? '');

        // 4. Validaciones obligatorias de campos clave
        if ($name === '') {
            $this->json(['ok' => false, 'error' => 'El nombre completo es obligatorio'], 422);
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['ok' => false, 'error' => 'Correo institucional inválido o vacío'], 422);
        }

        if ($password === '' || strlen($password) < 6) {
            $this->json(['ok' => false, 'error' => 'La contraseña debe contener al menos 6 caracteres'], 422);
        }

        $allowedRoles = ['superadmin', 'admin', 'cashregister', 'teacher', 'student'];
        if (!in_array($role, $allowedRoles, true)) {
            $this->json(['ok' => false, 'error' => 'Rol institucional no permitido'], 422);
        }

        try {
            $pdo = Conection::getInstance();

            // Verificar si el correo ya existe en asrs_users
            $checkEmail = $pdo->prepare("SELECT id FROM asrs_users WHERE email = :email LIMIT 1");
            $checkEmail->execute([':email' => $email]);
            if ($checkEmail->fetch()) {
                $this->json(['ok' => false, 'error' => "El correo institucional '{$email}' ya se encuentra registrado"], 409);
            }

            // Buscar ID del rol en la tabla roles
            $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = :role LIMIT 1");
            $stmtRole->execute([':role' => $role]);
            $roleId = $stmtRole->fetchColumn();

            // Respaldo de inserción si el rol no estuviera en la tabla roles
            if (!$roleId) {
                $stmtNewRole = $pdo->prepare("INSERT INTO roles (name, display_name, status) VALUES (:name, :display, 1)");
                $stmtNewRole->execute([':name' => $role, ':display' => ucfirst($role)]);
                $roleId = (int)$pdo->lastInsertId();
            }

            // Procesar hash seguro bcrypt (cost 12)
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            // Inserción en la tabla asrs_users
            $stmtUser = $pdo->prepare("
                INSERT INTO asrs_users (name, email, password_hash, status, created_at, updated_at)
                VALUES (:name, :email, :password_hash, 1, NOW(), NOW())
            ");
            $stmtUser->execute([
                ':name'          => $name,
                ':email'         => $email,
                ':password_hash' => $passwordHash,
            ]);
            $newUserId = (int)$pdo->lastInsertId();

            // Inserción de la asignación de rol en asrs_user_roles
            $stmtUserRole = $pdo->prepare("
                INSERT INTO asrs_user_roles (user_id, role_id)
                VALUES (:user_id, :role_id)
            ");
            $stmtUserRole->execute([
                ':user_id' => $newUserId,
                ':role_id' => (int)$roleId,
            ]);

            // Total de usuarios registrados
            $usersCount = (int)$pdo->query("SELECT COUNT(*) FROM asrs_users")->fetchColumn();

            $this->json([
                'ok'         => true,
                'message'    => "Usuario '{$name}' aprovisionado exitosamente en el núcleo ASRS.",
                'user'       => [
                    'id'         => $newUserId,
                    'name'       => $name,
                    'email'      => $email,
                    'role'       => $role,
                    'roles'      => $role,
                    'status'     => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ],
                'usersCount' => $usersCount,
            ], 201);

        } catch (Throwable $e) {
            $this->json([
                'ok'    => false,
                'error' => 'Fallo al persistir el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        echo json_encode($data);
        exit;
    }
}
