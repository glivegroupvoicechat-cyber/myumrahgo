<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';
$user = require_auth($pdo);
if (!in_array($user['role'], ['admin','super_admin'], true)) respond(['error'=>'Admin access required'],403);

$count=function(string $sql) use ($pdo): int { return (int)$pdo->query($sql)->fetchColumn(); };
respond([
 'ok'=>true,
 'agencies'=>['active'=>$count("SELECT COUNT(*) FROM agencies WHERE status='active'"),'pending'=>$count("SELECT COUNT(*) FROM agencies WHERE status='pending'")],
 'bookings'=>['pending'=>$count("SELECT COUNT(*) FROM bookings WHERE status='pending'")],
 'inventory'=>['published'=>$count("SELECT COUNT(*) FROM hotels WHERE active=1") + $count("SELECT COUNT(*) FROM flights WHERE active=1")],
 'payments'=>['pending'=>0]
]);
