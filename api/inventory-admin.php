<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);
if (!in_array($user['role'], ['admin','super_admin'], true)) {
    respond(['error' => 'Admin access required'], 403);
}

require_method('POST');
$data = json_input();
$type = trim((string)($data['type'] ?? ''));
$action = trim((string)($data['action'] ?? ''));
$id = (int)($data['id'] ?? 0);

$allowed = ['hotels','flights','visa_products','transport_services','ziyarat_services'];
if (!in_array($type, $allowed, true)) respond(['error' => 'Invalid inventory type'], 422);
if (!in_array($action, ['publish','unpublish','delete'], true)) respond(['error' => 'Invalid action'], 422);
if ($id < 1) respond(['error' => 'id is required'], 422);

$tables = [
  'hotels' => 'hotels',
  'flights' => 'flights',
  'visa_products' => 'visa_products',
  'transport_services' => 'transport_services',
  'ziyarat_services' => 'ziyarat_services'
];
$table = $tables[$type];

if ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    audit_log($pdo, (int)$user['id'], 'inventory.delete', $type, $id, ['deleted' => $stmt->rowCount() > 0]);
    respond(['ok' => true, 'deleted' => $stmt->rowCount() > 0]);
}

$status = $action === 'publish' ? 'published' : 'draft';
$stmt = $pdo->prepare("UPDATE {$table} SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);
audit_log($pdo, (int)$user['id'], 'inventory.status', $type, $id, ['status' => $status]);
respond(['ok' => true, 'id' => $id, 'status' => $status]);
