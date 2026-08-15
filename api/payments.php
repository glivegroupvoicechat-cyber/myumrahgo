<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);
if(in_array($user['role'],['admin','super_admin'],true)) $agencyId=(int)($_GET['agency_id']??0);
else { $s=$pdo->prepare("SELECT id FROM agencies WHERE owner_user_id=? AND status='active' LIMIT 1");$s->execute([(int)$user['id']]);$agencyId=(int)($s->fetchColumn()?:0); }
if($agencyId<1) respond(['error'=>'Agency access is required'],403);

if($_SERVER['REQUEST_METHOD']==='GET'){
 $s=$pdo->prepare('SELECT id,agency_id,invoice_id,booking_id,amount_pkr,payment_method,reference_no,proof_path,status,verified_by,verified_at,created_at FROM payments WHERE agency_id=? ORDER BY created_at DESC LIMIT 250');$s->execute([$agencyId]);respond(['ok'=>true,'payments'=>$s->fetchAll()]);
}
require_method('POST');
$d=json_input();$invoiceId=(int)($d['invoice_id']??0);$bookingId=(int)($d['booking_id']??0);$amount=(float)($d['amount_pkr']??0);$method=trim((string)($d['payment_method']??''));$ref=trim((string)($d['reference_no']??''));
if($amount<=0||$method==='')respond(['error'=>'amount_pkr and payment_method are required'],422);
$s=$pdo->prepare("INSERT INTO payments(agency_id,invoice_id,booking_id,amount_pkr,payment_method,reference_no,proof_path,status,created_at) VALUES(?,?,?,?,?,?,?, 'pending', NOW())");
$s->execute([$agencyId,$invoiceId?:null,$bookingId?:null,$amount,$method,$ref?:null,trim((string)($d['proof_path']??''))?:null]);
respond(['ok'=>true,'payment_id'=>(int)$pdo->lastInsertId(),'status'=>'pending'],201);
