<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

require_method('POST');
$data = json_input();
$name = trim((string)($data['name'] ?? ''));
$email = strtolower(trim((string)($data['email'] ?? '')));
$phone = trim((string)($data['phone'] ?? ''));
$password = (string)($data['password'] ?? '');
$agencyName = trim((string)($data['agency_name'] ?? ''));

if ($name === '' || $agencyName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    respond(['error' => 'Name, agency name, valid email and password of at least 8 characters are required'], 422);
}

try {
    $pdo->beginTransaction();
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
        $pdo->rollBack();
        respond(['error' => 'An account with this email already exists'], 409);
    }

    $stmt = $pdo->prepare('INSERT INTO users (role, name, email, phone, password_hash, status) VALUES (\'agent\', ?, ?, ?, ?, \'pending\')');
    $stmt->execute([$name, $email, $phone ?: null, password_hash($password, PASSWORD_DEFAULT)]);
    $userId = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare('INSERT INTO agencies (owner_user_id, name, email, phone, status) VALUES (?, ?, ?, ?, \'pending\')');
    $stmt->execute([$userId, $agencyName, $email, $phone ?: null]);
    $agencyId = (int)$pdo->lastInsertId();

    $pdo->commit();
    respond(['ok' => true, 'status' => 'pending', 'user_id' => $userId, 'agency_id' => $agencyId], 201);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    respond(['error' => 'Registration could not be completed'], 500);
}
