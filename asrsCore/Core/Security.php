<?php
namespace asrsCore\Core;

use PDO;

/**
 * Seguridad perimetral ASRS (patrón Split Token).
 *
 *  - Parte A (selector): texto plano en asrs_user_tokens.selector + cookie HttpOnly.
 *  - Parte B (validator): sólo su hash SHA-256 se guarda en BD; el valor en claro viaja
 *    al cliente, que lo devuelve en el header `Authorization: Bearer <validator>`
 *    (o en la cookie legible `asrs_val` para navegaciones normales del navegador).
 */
class Security
{
    public const COOKIE_SELECTOR  = 'asrs_sel';
    public const COOKIE_VALIDATOR = 'asrs_val';
    public const TOKEN_TTL        = 28800; // 8 horas
    public const LOGIN_URI        = 'asrsweb/login';
    public const ADMIN_ROLE       = 'superadmin';
    public const ADMIN_HOME       = 'asrsadmin/dashboard';
    public const DENIED_MESSAGE   = '[ACCESO DENEGADO] Credenciales válidas, pero sin privilegios de superadministrador en este entorno';

    /** Ruta base del front controller ("" si public/ es la raíz, "/x/public" en subcarpeta). */
    public static function basePath(): string
    {
        return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }

    /** URL absoluta (de ruta) respetando la base: url('asrsweb/login') */
    public static function url(string $uri): string
    {
        return self::basePath() . '/' . ltrim($uri, '/');
    }

    private static ?array $currentUser = null;

    private static function isHttps(): bool
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }

    private static function cookieOptions(int $expires): array
    {
        return [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Strict',
        ];
    }

    /** Crea el token dividido, lo persiste y envía la cookie HttpOnly. Retorna la Parte B. */
    public static function issueToken(int $userId): array
    {
        $pdo = Conection::getInstance();

        // Limpieza de tokens vencidos
        $pdo->exec("DELETE FROM asrs_user_tokens WHERE expires_at < NOW()");

        $selector  = bin2hex(random_bytes(16));   // Parte A
        $validator = bin2hex(random_bytes(32));   // Parte B
        $expires   = time() + self::TOKEN_TTL;

        $pdo->prepare(
            "INSERT INTO asrs_user_tokens (user_id, selector, validator_hash, expires_at)
             VALUES (:u, :s, :h, :e)"
        )->execute([
            ':u' => $userId,
            ':s' => $selector,
            ':h' => hash('sha256', $validator),
            ':e' => date('Y-m-d H:i:s', $expires),
        ]);

        setcookie(self::COOKIE_SELECTOR, $selector, self::cookieOptions($expires));

        return ['validator' => $validator, 'expires' => $expires];
    }

    /** Valida cookie + validator contra asrs_user_tokens y asrs_users. Retorna el usuario o null. */
    public static function authenticate(): ?array
    {
        if (self::$currentUser !== null) {
            return self::$currentUser;
        }

        $selector  = (string)($_COOKIE[self::COOKIE_SELECTOR] ?? '');
        $validator = self::readValidator();

        if (!preg_match('/^[a-f0-9]{32}$/', $selector) || !preg_match('/^[a-f0-9]{64}$/', $validator)) {
            return null;
        }

        $stmt = Conection::getInstance()->prepare(
            "SELECT t.id AS token_id, t.validator_hash, u.id, u.name, u.email
               FROM asrs_user_tokens t
               JOIN asrs_users u ON u.id = t.user_id
              WHERE t.selector = :s AND t.expires_at > NOW() AND u.status = 1
              LIMIT 1"
        );
        $stmt->execute([':s' => $selector]);
        $row = $stmt->fetch();

        if (!$row || !hash_equals($row['validator_hash'], hash('sha256', $validator))) {
            return null;
        }

        $user = [
            'id'    => (int)$row['id'],
            'name'  => $row['name'],
            'email' => $row['email'],
            'roles' => self::rolesOf((int)$row['id']),
        ];
        return self::$currentUser = $user;
    }

    private static function readValidator(): string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if ($header === '' && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) {
                    $header = $v;
                }
            }
        }
        if (preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
            return $m[1];
        }
        return (string)($_COOKIE[self::COOKIE_VALIDATOR] ?? '');
    }

    /** Nombres de rol del usuario (tabla asrs_user_roles + roles activos). */
    public static function rolesOf(int $userId): array
    {
        $stmt = Conection::getInstance()->prepare(
            "SELECT r.name FROM asrs_user_roles ur
               JOIN roles r ON r.id = ur.role_id AND r.status = 1
              WHERE ur.user_id = :u"
        );
        $stmt->execute([':u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Destino post-login: sólo el superadmin accede (dashboard de asrsAdmin); otros roles retornan null. */
    public static function homeFor(int $userId, array $roles): ?string
    {
        return in_array(self::ADMIN_ROLE, $roles, true) ? self::url(self::ADMIN_HOME) : null;
    }

    /**
     * Interceptor del Kernel para rutas 'private'. Si no hay sesión válida corta el flujo
     * (redirect al login o 401 JSON); si no hay autorización responde 403.
     */
    public static function guard(array $route): array
    {
        $user = self::authenticate();

        if ($user === null) {
            self::destroySession(false);
            self::deny(401);
        }

        if (!in_array(self::ADMIN_ROLE, $user['roles'], true)) {
            $allowed = (array)($route['roles'] ?? []);
            if ($allowed && !array_intersect($allowed, $user['roles'])) {
                self::deny(403);
            }
            if (!self::hasAppAccess($user['id'], (string)($route['app'] ?? ''))) {
                self::deny(403);
            }
        }

        return $user;
    }

    private static function hasAppAccess(int $userId, string $appName): bool
    {
        $stmt = Conection::getInstance()->prepare(
            "SELECT 1 FROM asrs_user_apps ua
               JOIN asrs_apps a ON a.id = ua.app_id AND a.status = 1
              WHERE ua.user_id = :u AND a.name = :n LIMIT 1"
        );
        $stmt->execute([':u' => $userId, ':n' => $appName]);
        return (bool)$stmt->fetchColumn();
    }

    private static function deny(int $code): never
    {
        $wantsJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
            || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if ($code === 401 && !$wantsJson) {
            header('Location: ' . self::url(self::LOGIN_URI), true, 302);
            exit;
        }

        http_response_code($code);
        if ($wantsJson) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['ok' => false, 'error' => $code === 401 ? 'No autenticado' : 'Acceso denegado']);
        } else {
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>403</title></head>'
               . '<body style="font-family:sans-serif;text-align:center;padding:4rem">'
               . '<h1>403</h1><p>No tienes permiso para acceder a este recurso.</p></body></html>';
        }
        exit;
    }

    /** Elimina el registro del token en BD (si se conoce el selector) y limpia las cookies. */
    public static function destroySession(bool $deleteRecord = true): void
    {
        $selector = (string)($_COOKIE[self::COOKIE_SELECTOR] ?? '');
        if ($deleteRecord && preg_match('/^[a-f0-9]{32}$/', $selector)) {
            Conection::getInstance()
                ->prepare("DELETE FROM asrs_user_tokens WHERE selector = :s")
                ->execute([':s' => $selector]);
        }

        if (!headers_sent()) {
            setcookie(self::COOKIE_SELECTOR, '', self::cookieOptions(1));
            $opts = self::cookieOptions(1);
            $opts['httponly'] = false;
            setcookie(self::COOKIE_VALIDATOR, '', $opts);
        }
        unset($_COOKIE[self::COOKIE_SELECTOR], $_COOKIE[self::COOKIE_VALIDATOR]);
        self::$currentUser = null;
    }
}
