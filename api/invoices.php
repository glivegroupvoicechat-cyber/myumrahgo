<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);

function resolve_agency(PDO $pdo, array $user): int {
    if(in_array($user['role'],['admin','super_admin'],true)) return (int)($_GET['agency_id'] ?? 0);
    $s=$pdo->prepare("SELECT id FROM agencies WHERE owner_user_id=? AND status='active' LIMIT 1");
    $s->execute([(int)$user['id']]);
    return (int)($s->fetchColumn()?:0);
}
$agencyId=resolve_agency($pdo,$user);
if($agencyId<1) respond(['error'=>'Agency access is required'],403);

if($_SERVER['REQUEST_METHOD']==='GET'){
    $stmt=$pdo->prepare('SELECT id,invoice_no,quotation_id,booking_id,agency_id,customer_id,subtotal_pkr,tax_pkr,total_pkr,status,due_date,created_at,updated_at FROM invoices WHERE agency_id=? ORDER BY created_at DESC LIMIT 250');
    $stmt->execute([$agencyId]);
    respond(['ok'=>true,'invoices'=>$stmt->fetchAll()]);
}
require_method('POST');
$data=json_input();
$customerId=(int)($data['customer_id']??0);
$quotationId=(int)($data['quotation_id']??0);
$bookingId=(int)($data['booking_id']??0);
$subtotal=(float)($data['subtotal_pkr']??0);
$tax=(float)($data['tax_pkr']??0);
$total=$subtotal+$tax;
if($total<0) respond(['error'=>'Invalid invoice amount'],422);
$invoiceNo='INV-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
$stmt=$pdo->prepare("INSERT INTO invoices(invoice_no,quotation_id,booking_id,agency_id,customer_id,subtotal_pkr,tax_pkr,total_pkr,status,due_date,created_at,updated_at) VALUES(?,?,?,?,?,?,?,'issued',?,?,NOW(),NOW())");
$stmt->execute([$invoiceNo,$quotationId?:null,$bookingId?:null,$agencyId,$customerId?:null,$subtotal,$tax,$total,$data['due_date']??null]);
respond(['ok'=>true,'invoice_no'=>$invoiceNo,'invoice_id'=>(int)$pdo->lastInsertId(),'total_pkr'=>$total],201);
