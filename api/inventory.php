<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

require_method('GET');
$user = require_auth($pdo);
$type = strtolower(trim((string)($_GET['type'] ?? 'hotels')));

if ($type === 'hotels') {
    $city = $_GET['city'] ?? null;
    $sql = 'SELECT h.id, h.city, h.name, h.distance_m, h.stars, h.description, h.active FROM hotels h WHERE h.active = 1';
    $params = [];
    if ($city && in_array($city, ['makkah','madinah'], true)) { $sql .= ' AND h.city = ?'; $params[] = $city; }
    $sql .= ' ORDER BY h.city, h.distance_m IS NULL, h.distance_m, h.name';
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    respond(['ok' => true, 'type' => 'hotels', 'items' => $stmt->fetchAll()]);
}

if ($type === 'flights') {
    $stmt = $pdo->query('SELECT id, airline, flight_no, direction, origin, destination, departure_at, arrival_at, baggage_kg, active FROM flights WHERE active = 1 ORDER BY departure_at LIMIT 500');
    respond(['ok' => true, 'type' => 'flights', 'items' => $stmt->fetchAll()]);
}

if ($type === 'packages') {
    $stmt = $pdo->query("SELECT id, name, slug, duration_nights, makkah_nights, madinah_nights, base_price_pkr, status, description FROM packages WHERE status = 'published' ORDER BY updated_at DESC LIMIT 200");
    respond(['ok' => true, 'type' => 'packages', 'items' => $stmt->fetchAll()]);
}

respond(['error' => 'Unknown inventory type'], 400);
