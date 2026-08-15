<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);
$method=$_SERVER['REQUEST_METHOD']??'GET';
$agencyId=null;
if(!in_array($user['role'],['admin','super_admin'],true)){
  $stmt=$pdo->prepare('SELECT id FROM agencies WHERE owner_user_id=? AND status=\'active\' LIMIT 1');
  $stmt->execute([(int)$user['id']]);
  $agencyId=(int)($stmt->fetchColumn()?:0);
  if($agencyId<1) respond(['error'=>'Active agency access is required'],403);
}else{$agencyId=(int)($_GET['agency_id']??0)?:null;}

if($method==='GET'){
  if(in_array($user['role'],['admin','super_admin'],true)){
    $stmt=$pdo->query('SELECT t.id,t.subject,t.priority,t.status,t.created_at,t.updated_at,t.agency_id,u.name AS requester_name FROM support_tickets t INNER JOIN users u ON u.id=t.user_id ORDER BY t.created_at DESC LIMIT 250');
  }else{
    $stmt=$pdo->prepare('SELECT id,subject,priority,status,created_at,updated_at FROM support_tickets WHERE agency_id=? AND user_id=? ORDER BY created_at DESC LIMIT 100');
    $stmt->execute([$agencyId,(int)$user['id']]);
  }
  respond(['ok'=>true,'tickets'=>$stmt->fetchAll()]);
}
require_method('POST');
$data=json_input();
$subject=trim((string)($data['subject']??''));$message=trim((string)($data['message']??''));
if($subject===''||$message==='')respond(['error'=>'Subject and message are required'],422);
$priority=in_array(($data['priority']??'normal'),['low','normal','high','urgent'],true)?$data['priority']:'normal';
$stmt=$pdo->prepare('INSERT INTO support_tickets(agency_id,user_id,subject,message,priority,status) VALUES(?,?,?,?,?,\'open\')');
$stmt->execute([$agencyId,(int)$user['id'],$subject,$message,$priority]);
respond(['ok'=>true,'ticket_id'=>(int)$pdo->lastInsertId(),'status'=>'open'],201);
