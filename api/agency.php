<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

require_method('GET');
$user = require_auth($pdo);

if (in_array($user['role'], ['admin','super_admin'], true)) {
    $stmt = $pdo->query('SELECT id, owner_user_id, name, email, phone, status, created_at FROM agencies ORDER BY created_at DESC LIMIT 100');
    respond(['ok' => true, 'agencies' => $stmt->fetchAll()]);
}

$stmt = $pdo->prepare('SELECT id, owner_user_id, name, email, phone, status, created_at FROM agencies WHERE owner_user_id = ? LIMIT 1');
$stmt->execute([(int)$user['id']]);
$agency = $stmt->fetch();
respond(['ok' => true, 'agency' => $agency ?: null]);
