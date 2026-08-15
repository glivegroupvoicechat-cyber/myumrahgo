<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (in_array($user['role'], ['admin','super_admin'], true)) {
    $agencyId = isset($_GET['agency_id']) ? (int)$_GET['agency_id'] : null;
} else {
    $stmt = $pdo->prepare("SELECT id FROM agencies WHERE owner_user_id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int)$user['id']]);
    $agencyId = (int)($stmt->fetchColumn() ?: 0);
    if ($agencyId < 1) respond(['error'=>'Active agency access is required'],403);
}
if ($agencyId < 1) respond(['error'=>'agency_id is required'],422);

if ($method === 'GET') {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    if ($customerId < 1) respond(['error'=>'customer_id is required'],422);
    $check = $pdo->prepare('SELECT id FROM customers WHERE id = ? AND agency_id = ? LIMIT 1');
    $check->execute([$customerId,$agencyId]);
    if (!$check->fetch()) respond(['error'=>'Customer not found'],404);
    $stmt = $pdo->prepare('SELECT id,full_name,passport_no,passport_expiry,dob,nationality,passport_country,created_at FROM passengers WHERE customer_id = ? ORDER BY created_at DESC');
    $stmt->execute([$customerId]);
    respond(['ok'=>true,'passengers'=>$stmt->fetchAll()]);
}

require_method('POST');
$data=json_input();
$customerId=(int)($data['customer_id']??0);
$name=trim((string)($data['full_name']??''));
if($customerId<1||$name==='') respond(['error'=>'customer_id and full_name are required'],422);
$check=$pdo->prepare('SELECT id FROM customers WHERE id = ? AND agency_id = ? LIMIT 1');
$check->execute([$customerId,$agencyId]);
if(!$check->fetch()) respond(['error'=>'Customer not found'],404);
$stmt=$pdo->prepare('INSERT INTO passengers (customer_id,full_name,passport_no,passport_expiry,dob,nationality,passport_country,restricted_notes) VALUES (?,?,?,?,?,?,?,?)');
$stmt->execute([$customerId,$name,trim((string)($data['passport_no']??''))?:null,$data['passport_expiry']??null,$data['dob']??null,trim((string)($data['nationality']??''))?:null,trim((string)($data['passport_country']??''))?:null,null]);
respond(['ok'=>true,'passenger_id'=>(int)$pdo->lastInsertId()],201);
