<?php
namespace asrsCore\Controllers;

use asrsCore\Core\Conection;
use asrsCore\Core\Security;

class AuthController
{
    /** POST auth/login: valida credenciales, emite Split Token y devuelve el destino de redirección. */
    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        // El formulario usa el campo "username" para el email
        $email    = trim((string)($_POST['email'] ?? $_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->json(['ok' => false, 'error' => 'Credenciales incompletas'], 422);
        }

        $stmt = Conection::getInstance()->prepare(
            "SELECT id, password_hash FROM asrs_users WHERE email = :e AND status = 1 LIMIT 1"
        );
        $stmt->execute([':e' => $email]);
        $user = $stmt->fetch();

        // Hash señuelo para igualar tiempos cuando el usuario no existe
        $hash = $user['password_hash'] ?? password_hash('decoy', PASSWORD_DEFAULT);
        if (!password_verify($password, $hash) || !$user) {
            $this->json(['ok' => false, 'error' => 'Usuario o contraseña incorrectos'], 401);
        }

        $userId = (int)$user['id'];
        $roles  = Security::rolesOf($userId);
        $home   = Security::homeFor($userId, $roles);

        // Solo el superadmin accede a la zona administrativa: no se emite token para otros roles
        if ($home === null) {
            $this->json(['ok' => false, 'denied' => true, 'error' => Security::DENIED_MESSAGE], 403);
        }

        $token = Security::issueToken($userId);

        $this->json([
            'ok'        => true,
            'redirect'  => $home,
            'validator' => $token['validator'],   // Parte B para el cliente
            'expires'   => $token['expires'],
        ]);
    }

    /** POST auth/logout: borra el token en BD y limpia las cookies. */
    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        Security::destroySession();

        $wantsJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
        if ($wantsJson) {
            $this->json(['ok' => true, 'redirect' => Security::url(Security::LOGIN_URI)]);
        }
        header('Location: ' . Security::url(Security::LOGIN_URI), true, 303);
        exit;
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
