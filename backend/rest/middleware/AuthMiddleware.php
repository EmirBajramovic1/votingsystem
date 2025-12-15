<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../config.php';

class AuthMiddleware {
    public function verifyToken($header)
    {
        if (!$header) {
            Flight::halt(401, "Missing Authorization header");
        }

        if (strpos($header, "Bearer ") !== 0) {
            Flight::halt(401, "Invalid authorization format");
        }

        $token = trim(str_replace("Bearer", "", $header));

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
            Flight::set('user', $decoded->user);
        } catch (Exception $e) {
            Flight::halt(401, "Invalid token: " . $e->getMessage());
        }
    }

    public function authorizeRole($role) {
        $user = Flight::get('user');
        if (!isset($user->role) || $user->role !== $role) {
            Flight::halt(403, 'Access denied');
        }
    }

    public function authorizeRoles($roles) {
        $user = Flight::get('user');
        if (!isset($user->role) || !in_array($user->role, $roles)) {
            Flight::halt(403, 'Forbidden');
        }
    }
}
?>