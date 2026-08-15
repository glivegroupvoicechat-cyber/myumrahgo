<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

function start_secure_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_set_cookie_params([
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function current_user(PDO $pdo): ?array {
    start_secure_session();
    $uid = $_SESSION['user_id'] ?? null;
    if (!is_int($uid) && !ctype_digit((string)$uid)) return null;
    $stmt = $pdo->prepare('SELECT id, role, name, email, phone, status FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$uid]);
    $user = $stmt->fetch();
    if (!$user || $user['status'] !== 'active') return null;
    return $user;
}

function require_auth(PDO $pdo): array {
    $user = current_user($pdo);
    if (!$user) respond(['error' => 'Authentication required'], 401);
    return $user;
}

function require_roles(array $allowed, array $user): void {
    if (!in_array($user['role'], $allowed, true)) respond(['error' => 'Forbidden'], 403);
}

function login_user(PDO $pdo, string $email, string $password): array {
    start_secure_session();
    $stmt = $pdo->prepare('SELECT id, role, name, email, phone, password_hash, status FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([strtolower(trim($email))]);
    $user = $stmt->fetch();
    if (!$user || $user['status'] !== 'active' || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        respond(['error' => 'Invalid email or password'], 401);
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['login_at'] = time();
    unset($user['password_hash']);
    return $user;
}

function logout_user(): void {
    start_secure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], '', $params['secure'], $params['httponly']);
    }
    session_destroy();
}
