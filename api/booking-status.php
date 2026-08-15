<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);
if(!in_array($user['role'],['admin','super_admin','booking','agent'],true)) respond(['error'=>'Booking permission required'],403);
$data=json_input();
$bookingId=(int)($data['booking_id']??0);
$status=trim((string)($data['status']??''));
$allowed=['pending','under_review','confirmed','processing','visa_processing','flight_processing','hotel_processing','transport_processing','issued','completed','cancelled','rejected'];
if($bookingId<1||!in_array($status,$allowed,true)) respond(['error'=>'Valid booking_id and status are required'],422);

if(in_array($user['role'],['admin','super_admin'],true)){
  $s=$pdo->prepare('SELECT * FROM bookings WHERE id=? LIMIT 1');$s->execute([$bookingId]);
}else{
  $s=$pdo->prepare('SELECT b.* FROM bookings b INNER JOIN agencies a ON a.id=b.agency_id WHERE b.id=? AND a.owner_user_id=? LIMIT 1');$s->execute([$bookingId,(int)$user['id']]);
}
$booking=$s->fetch();
if(!$booking) respond(['error'=>'Booking not found or access denied'],404);

$pdo->beginTransaction();
try{
  $u=$pdo->prepare('UPDATE bookings SET status=? WHERE id=?');$u->execute([$status,$bookingId]);
  if($pdo->query("SHOW TABLES LIKE 'booking_status_history'")->fetchColumn()){
    $h=$pdo->prepare('INSERT INTO booking_status_history(booking_id,status,note,changed_by,created_at) VALUES(?,?,?,?,NOW())');
    $h->execute([$bookingId,$status,trim((string)($data['note']??''))?:null,(int)$user['id']]);
  }
  $pdo->commit();
  respond(['ok'=>true,'booking_id'=>$bookingId,'status'=>$status]);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();respond(['error'=>'Unable to update booking status'],500);}
