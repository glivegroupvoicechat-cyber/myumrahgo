<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';
$user = require_auth($pdo);
if (!in_array($user['role'], ['admin','super_admin'], true)) respond(['error'=>'Admin access required'],403);
require_method('GET');
$status = trim((string)($_GET['status'] ?? ''));
$params=[];
$sql='SELECT a.id,a.name,a.email,a.phone,a.status,a.created_at,u.name AS owner_name,u.email AS owner_email FROM agencies a LEFT JOIN users u ON u.id=a.owner_user_id';
if ($status !== '') { $sql.=' WHERE a.status=?'; $params[]=$status; }
$sql.=' ORDER BY a.created_at DESC LIMIT 200';
$stmt=$pdo->prepare($sql);$stmt->execute($params);
respond(['ok'=>true,'agencies'=>$stmt->fetchAll()]);
