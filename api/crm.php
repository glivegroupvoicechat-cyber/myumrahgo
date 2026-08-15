<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$agencyId = null;
if (in_array($user['role'], ['admin','super_admin'], true)) {
    $agencyId = isset($_GET['agency_id']) ? (int)$_GET['agency_id'] : null;
} else {
    $stmt = $pdo->prepare("SELECT id FROM agencies WHERE owner_user_id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int)$user['id']]);
    $agencyId = (int)($stmt->fetchColumn() ?: 0);
    if ($agencyId < 1) respond(['error'=>'Active agency access is required'],403);
}

if ($method === 'GET') {
    $q = trim((string)($_GET['q'] ?? ''));
    $sql = 'SELECT id,name,mobile,email,nationality,country,notes,created_at,updated_at FROM customers WHERE agency_id = ?';
    $params = [$agencyId];
    if ($q !== '') { $sql .= ' AND (name LIKE ? OR mobile LIKE ? OR email LIKE ?)'; $like = "%{$q}%"; array_push($params,$like,$like,$like); }
    $sql .= ' ORDER BY created_at DESC LIMIT 250';
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    respond(['ok'=>true,'customers'=>$stmt->fetchAll()]);
}

require_method('POST');
$data = json_input();
$name = trim((string)($data['name'] ?? ''));
$mobile = trim((string)($data['mobile'] ?? ''));
$email = strtolower(trim((string)($data['email'] ?? '')));
if ($name === '') respond(['error'=>'Customer name is required'],422);
if ($email !== '' && !filter_var($email,FILTER_VALIDATE_EMAIL)) respond(['error'=>'Invalid email'],422);
$stmt = $pdo->prepare('INSERT INTO customers (agency_id,name,mobile,email,nationality,country,notes) VALUES (?,?,?,?,?,?,?)');
$stmt->execute([$agencyId,$name,$mobile ?: null,$email ?: null,trim((string)($data['nationality'] ?? '')) ?: null,trim((string)($data['country'] ?? '')) ?: null,trim((string)($data['notes'] ?? '')) ?: null]);
respond(['ok'=>true,'customer_id'=>(int)$pdo->lastInsertId()],201);
