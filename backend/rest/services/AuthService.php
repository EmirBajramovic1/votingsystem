<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../data/Roles.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;

class AuthService extends BaseService {
    private $authDao;

    public function __construct() {
        $this->authDao = new AuthDao();
        parent::__construct($this->authDao);
    }

    public function register($data) {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email'];
        }

        $existing = $this->authDao->getUserByEmail($data['email']);
        if ($existing) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['is_verified'] = 1;

        $id = $this->create($data);
        $user = $this->authDao->getById($id);

        unset($user['password']);
        $user['role'] = Roles::USER;

        $token = $this->encodeToken($user);

        return ['success' => true, 'data' => array_merge($user, ['token' => $token])];
    }

    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'error' => 'Email and password are required'];
        }

        $user = $this->authDao->getUserByEmail($data['email']);
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        if (!password_verify($data['password'], $user['password'])) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        unset($user['password']);
        $user['role'] = $this->resolveRole($user);

        $token = $this->encodeToken($user);

        return ['success' => true, 'data' => array_merge($user, ['token' => $token])];
    }

    private function resolveRole($user) {
        if ($user['email'] === 'admin@gmail.com') {
            return Roles::ADMIN;
        }
        return Roles::USER;
    }

    private function encodeToken($user) {
        $payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24
        ];
        return JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
    }
}
?>