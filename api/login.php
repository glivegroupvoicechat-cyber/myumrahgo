<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

require_method('POST');
$data = json_input();
$email = trim((string)($data['email'] ?? ''));
$password = (string)($data['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    respond(['error' => 'Valid email and password are required'], 422);
}
$user = login_user($pdo, $email, $password);
respond(['ok' => true, 'user' => $user]);
