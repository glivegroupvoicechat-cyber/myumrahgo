<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_method('POST');
$data=json_input();
$name=trim((string)($data['agency']??''));
$contact=trim((string)($data['contact']??''));
$email=strtolower(trim((string)($data['email']??'')));
$phone=trim((string)($data['phone']??''));
$city=trim((string)($data['city']??''));
$password=(string)($data['password']??'');
if($name===''||$contact===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$phone===''||$city==='') respond(['error'=>'Please complete all required fields.'],422);
if($password!==''&&strlen($password)<8) respond(['error'=>'Password must be at least 8 characters.'],422);
try{
  $pdo->beginTransaction();
  $check=$pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
  $check->execute([$email]);
  if($check->fetch()){ $pdo->rollBack(); respond(['error'=>'An account already exists with this email.'],409); }
  $hash=$password!==''?password_hash($password,PASSWORD_DEFAULT):null;
  $u=$pdo->prepare("INSERT INTO users(role_key,name,email,phone,password_hash,status) VALUES('agency_owner',?,?,?,?, 'pending')");
  $u->execute([$contact,$email,$phone,$hash]);
  $uid=(int)$pdo->lastInsertId();
  $a=$pdo->prepare("INSERT INTO agencies(owner_user_id,name,email,phone,address,status) VALUES(?,?,?,?,?,'pending')");
  $a->execute([$uid,$name,$email,$phone,$city]);
  $agencyId=(int)$pdo->lastInsertId();
  $b=$pdo->prepare('INSERT INTO agency_branding(agency_id,email,whatsapp) VALUES(?,?,?)');
  $b->execute([$agencyId,$email,$phone]);
  $pdo->commit();
  respond(['ok'=>true,'status'=>'pending','agency_id'=>$agencyId,'message'=>'Your agency application has been submitted for admin review.']);
}catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); respond(['error'=>'Unable to submit application right now.'],500); }
