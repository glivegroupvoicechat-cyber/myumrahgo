<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (in_array($user['role'], ['admin','super_admin'], true)) {
        $stmt = $pdo->query('SELECT b.id, b.booking_no, b.user_id, b.agency_id, b.package_id, b.travellers, b.room_type, b.status, b.total_pkr, b.notes, b.created_at, a.name AS agency_name FROM bookings b LEFT JOIN agencies a ON a.id = b.agency_id ORDER BY b.created_at DESC LIMIT 200');
    } else {
        $stmt = $pdo->prepare('SELECT b.id, b.booking_no, b.user_id, b.agency_id, b.package_id, b.travellers, b.room_type, b.status, b.total_pkr, b.notes, b.created_at FROM bookings b INNER JOIN agencies a ON a.id = b.agency_id WHERE a.owner_user_id = ? ORDER BY b.created_at DESC LIMIT 200');
        $stmt->execute([(int)$user['id']]);
    }
    respond(['ok' => true, 'bookings' => $stmt->fetchAll()]);
}

require_method('POST');
$data = json_input();
$packageId = isset($data['package_id']) ? (int)$data['package_id'] : 0;
$travellers = max(1, (int)($data['travellers'] ?? 1));
$roomType = trim((string)($data['room_type'] ?? ''));
$notes = trim((string)($data['notes'] ?? ''));
$total = isset($data['total_pkr']) ? (float)$data['total_pkr'] : null;

if ($packageId < 1) respond(['error' => 'package_id is required'], 422);

$agencyId = null;
if (!in_array($user['role'], ['admin','super_admin'], true)) {
    $stmt = $pdo->prepare("SELECT id FROM agencies WHERE owner_user_id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int)$user['id']]);
    $agency = $stmt->fetch();
    if (!$agency) respond(['error' => 'Active agency access is required'], 403);
    $agencyId = (int)$agency['id'];
}

$stmt = $pdo->prepare('SELECT id FROM packages WHERE id = ? AND status = \'published\' LIMIT 1');
$stmt->execute([$packageId]);
if (!$stmt->fetch()) respond(['error' => 'Package not available'], 404);

$bookingNo = booking_no();
$stmt = $pdo->prepare('INSERT INTO bookings (booking_no, user_id, agency_id, package_id, travellers, room_type, status, total_pkr, notes) VALUES (?, ?, ?, ?, ?, ?, \'pending\', ?, ?)');
$stmt->execute([$bookingNo, (int)$user['id'], $agencyId, $packageId, $travellers, $roomType ?: null, $total, $notes ?: null]);
respond(['ok' => true, 'booking_no' => $bookingNo, 'id' => (int)$pdo->lastInsertId(), 'status' => 'pending'], 201);
