<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);
if(!in_array($user['role'],['admin','super_admin','booking','agent'],true)) respond(['error'=>'Voucher permission required'],403);
$bookingId=(int)($_GET['booking_id']??0);
if($bookingId<1) respond(['error'=>'booking_id is required'],422);

if(in_array($user['role'],['admin','super_admin'],true)){
  $s=$pdo->prepare('SELECT b.id,b.booking_no,b.agency_id FROM bookings b WHERE b.id=? LIMIT 1');$s->execute([$bookingId]);
}else{
  $s=$pdo->prepare('SELECT b.id,b.booking_no,b.agency_id FROM bookings b INNER JOIN agencies a ON a.id=b.agency_id WHERE b.id=? AND a.owner_user_id=? LIMIT 1');$s->execute([$bookingId,(int)$user['id']]);
}
$booking=$s->fetch();
if(!$booking) respond(['error'=>'Booking not found or access denied'],404);

if($_SERVER['REQUEST_METHOD']==='GET'){
  $s=$pdo->prepare('SELECT id,booking_id,voucher_no,voucher_type,storage_path,status,issued_by,issued_at,created_at FROM vouchers WHERE booking_id=? ORDER BY created_at DESC');$s->execute([$bookingId]);respond(['ok'=>true,'vouchers'=>$s->fetchAll()]);
}
require_method('POST');
$voucherNo='VCH-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
$s=$pdo->prepare("INSERT INTO vouchers(booking_id,voucher_no,voucher_type,status,issued_by,issued_at,created_at) VALUES(?,?,?,'issued',?,NOW(),NOW())");
$s->execute([$bookingId,$voucherNo,trim((string)(json_input()['voucher_type']??'umrah'))?:'umrah',(int)$user['id']]);
respond(['ok'=>true,'voucher_id'=>(int)$pdo->lastInsertId(),'voucher_no'=>$voucherNo,'status'=>'issued'],201);
