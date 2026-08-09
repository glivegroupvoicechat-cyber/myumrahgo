<?php
require __DIR__.'/bootstrap.php';
require_method('POST');
$input=json_input();
$name=trim((string)($input['name']??'')); $phone=trim((string)($input['phone']??'')); $email=trim((string)($input['email']??''));
$packageId=(int)($input['package_id']??0); $travellers=max(1,(int)($input['travellers']??1)); $room=trim((string)($input['room_type']??'')); $notes=trim((string)($input['notes']??''));
if($name==='' || $phone==='' || $packageId<1) respond(['error'=>'Name, phone and package are required.'],422);
$check=$pdo->prepare("SELECT id FROM packages WHERE id=? AND status='published'"); $check->execute([$packageId]); if(!$check->fetch()) respond(['error'=>'Package not found.'],404);
$pdo->beginTransaction();
try { $u=$pdo->prepare("SELECT id FROM users WHERE phone=? LIMIT 1"); $u->execute([$phone]); $user=$u->fetch(); if(!$user){$ins=$pdo->prepare("INSERT INTO users(role,name,email,phone) VALUES('customer',?,?,?)");$ins->execute([$name,$email?:null,$phone]);$userId=(int)$pdo->lastInsertId();} else {$userId=(int)$user['id'];}
$booking=booking_no(); $stmt=$pdo->prepare("INSERT INTO bookings(booking_no,user_id,package_id,travellers,room_type,status,notes) VALUES(?,?,?,?,?,'inquiry',?)"); $stmt->execute([$booking,$userId,$packageId,$travellers,$room,$notes]); $pdo->commit(); respond(['success'=>true,'booking_no'=>$booking],201); }
catch(Throwable $e){$pdo->rollBack(); respond(['error'=>'Unable to create booking request.'],500);}
