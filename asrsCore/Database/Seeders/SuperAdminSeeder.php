<?php
namespace asrsCore\Database\Seeders;

use asrsCore\Core\Conection;

/**
 * Siembra idempotente del superadmin de pruebas, su rol y el panel asrsAdmin.
 * Ejecutar: php asrsCore/Database/seed.php
 */
class SuperAdminSeeder
{
    public const EMAIL    = 'sadmin@asrs.test';
    public const PASSWORD = 'password_sadmin123';

    public static function run(): void
    {
        $pdo = Conection::getInstance();

        // Tablas de apoyo (si no existen)
        $pdo->exec("CREATE TABLE IF NOT EXISTS `asrs_user_roles` (
            `user_id` INT(11) NOT NULL,
            `role_id` INT(11) NOT NULL,
            PRIMARY KEY (`user_id`, `role_id`),
            CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `asrs_users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `asrs_user_apps` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT(11) NOT NULL,
            `app_id` INT(11) NOT NULL,
            `role_in_app` VARCHAR(50) NOT NULL DEFAULT 'user',
            `created_at` DATETIME NOT NULL,
            UNIQUE KEY `user_app_unique` (`user_id`, `app_id`),
            CONSTRAINT `fk_user_apps_user` FOREIGN KEY (`user_id`) REFERENCES `asrs_users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_user_apps_app` FOREIGN KEY (`app_id`) REFERENCES `asrs_apps` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // App asrsAdmin + vista privada del panel
        $pdo->prepare("INSERT IGNORE INTO asrs_apps (app_key, name, description, status, is_default, created_at)
                       VALUES (:k, 'asrsAdmin', 'Panel de administración de ASRS', 1, 0, NOW())")
            ->execute([':k' => bin2hex(random_bytes(10))]);

        $pdo->exec("DELETE FROM asrs_views WHERE app = 'asrsAdmin' AND uri = 'asrsadmin/panel'");
        $pdo->prepare("INSERT INTO asrs_views (uri, app, type, controller, method, layout, content, roles, status)
                       VALUES ('asrsadmin/dashboard', 'asrsAdmin', 'private', '', '', 'default', 'dashboard', :r, 1)
                       ON DUPLICATE KEY UPDATE type = 'private', content = 'dashboard', roles = VALUES(roles), status = 1")
            ->execute([':r' => json_encode(['superadmin'])]);

        // Usuario superadmin
        $hash = password_hash(self::PASSWORD, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO asrs_users (name, email, password_hash, status, created_at, updated_at)
                       VALUES ('Super Administrador', :e, :h, 1, NOW(), NOW())
                       ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), status = 1, updated_at = NOW()")
            ->execute([':e' => self::EMAIL, ':h' => $hash]);

        $pdo->prepare("INSERT IGNORE INTO asrs_user_roles (user_id, role_id)
                       SELECT u.id, r.id FROM asrs_users u, roles r WHERE u.email = :e AND r.name = 'superadmin'")
            ->execute([':e' => self::EMAIL]);

        // Invalida cachés para que reflejen las nuevas rutas/roles
        foreach (['routes.php', 'roles.php', 'apps.php'] as $file) {
            @unlink(__DIR__ . '/../../Cache/' . $file);
        }
    }
}
