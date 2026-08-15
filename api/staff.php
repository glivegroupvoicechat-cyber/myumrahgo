<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);
if (in_array($user['role'], ['admin','super_admin'], true)) {
    $agencyId = (int)($_GET['agency_id'] ?? 0);
} else {
    $stmt = $pdo->prepare("SELECT id FROM agencies WHERE owner_user_id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int)$user['id']]);
    $agencyId = (int)($stmt->fetchColumn() ?: 0);
}
if ($agencyId < 1) respond(['error'=>'Agency access is required'],403);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt=$pdo->prepare('SELECT s.id,s.staff_role,s.status,s.created_at,u.id AS user_id,u.name,u.email,u.phone,u.status AS user_status FROM agency_staff s INNER JOIN users u ON u.id=s.user_id WHERE s.agency_id=? ORDER BY s.created_at DESC');
    $stmt->execute([$agencyId]);
    respond(['ok'=>true,'staff'=>$stmt->fetchAll()]);
}

require_method('POST');
$data=json_input();
$name=trim((string)($data['name']??''));
$email=strtolower(trim((string)($data['email']??'')));
$phone=trim((string)($data['phone']??''));
$role=trim((string)($data['staff_role']??'sales'));
if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)) respond(['error'=>'Valid name and email are required'],422);
$allowed=['sales','booking','accounts','marketing','readonly'];
if(!in_array($role,$allowed,true)) respond(['error'=>'Invalid staff role'],422);
$check=$pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');$check->execute([$email]);
if($check->fetch()) respond(['error'=>'A user with this email already exists'],409);
$pdo->beginTransaction();
try{
  $u=$pdo->prepare("INSERT INTO users(role_key,name,email,phone,password_hash,status) VALUES('agency_staff',?,?,?,NULL,'pending')");
  $u->execute([$name,$email,$phone?:null]);
  $uid=(int)$pdo->lastInsertId();
  $s=$pdo->prepare('INSERT INTO agency_staff(agency_id,user_id,staff_role,status) VALUES(?,?,?,\'invited\')');
  $s->execute([$agencyId,$uid,$role]);
  $pdo->commit();
  respond(['ok'=>true,'staff_id'=>(int)$pdo->lastInsertId(),'user_id'=>$uid,'status'=>'invited'],201);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();respond(['error'=>'Unable to create staff invitation'],500);}
