<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_method('GET');

session_start();
$userId = (int)($_SESSION['myumrahgo_user_id'] ?? 0);
if ($userId < 1) respond(['error'=>'Authentication required'],401);

$stmt = $pdo->prepare('SELECT id, role, name, email, status FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user || !in_array($user['role'], ['admin','super_admin'], true)) respond(['error'=>'Admin access required'],403);

$action = $_GET['action'] ?? 'summary';
if ($action === 'summary') {
    $counts = [];
    foreach ([
        'agencies'=>'SELECT COUNT(*) FROM agencies',
        'pending_agencies'=>"SELECT COUNT(*) FROM agencies WHERE status='pending'",
        'hotels'=>"SELECT COUNT(*) FROM hotels WHERE active=1",
        'flights'=>"SELECT COUNT(*) FROM flights WHERE active=1",
        'packages'=>"SELECT COUNT(*) FROM packages WHERE status='published'",
        'bookings'=>"SELECT COUNT(*) FROM bookings",
        'pending_bookings'=>"SELECT COUNT(*) FROM bookings WHERE status IN ('inquiry','pending')"
    ] as $key=>$sql) $counts[$key]=(int)$pdo->query($sql)->fetchColumn();
    respond(['user'=>$user,'counts'=>$counts]);
}

if ($action === 'agencies') {
    $rows=$pdo->query("SELECT a.id,a.name,a.email,a.phone,a.status,a.created_at,u.name AS owner_name,u.email AS owner_email FROM agencies a JOIN users u ON u.id=a.owner_user_id ORDER BY a.created_at DESC LIMIT 100")->fetchAll();
    respond(['agencies'=>$rows]);
}

respond(['error'=>'Unknown action'],400);
