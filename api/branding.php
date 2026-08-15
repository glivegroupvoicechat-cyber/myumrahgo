<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);
if(in_array($user['role'],['admin','super_admin'],true)){
  $agencyId=(int)($_GET['agency_id']??0);
}else{
  $stmt=$pdo->prepare("SELECT id FROM agencies WHERE owner_user_id=? AND status='active' LIMIT 1");
  $stmt->execute([(int)$user['id']]);
  $agencyId=(int)($stmt->fetchColumn()?:0);
}
if($agencyId<1) respond(['error'=>'Agency access is required'],403);

if($_SERVER['REQUEST_METHOD']==='GET'){
  $stmt=$pdo->prepare('SELECT agency_id,logo_path,primary_color,secondary_color,tagline,whatsapp,email,website,social_links,bank_details,terms_text,co_branding_mode,updated_at FROM agency_branding WHERE agency_id=? LIMIT 1');
  $stmt->execute([$agencyId]);
  respond(['ok'=>true,'branding'=>$stmt->fetch()?:null]);
}
require_method('POST');
$data=json_input();
$stmt=$pdo->prepare('INSERT INTO agency_branding(agency_id,logo_path,primary_color,secondary_color,tagline,whatsapp,email,website,social_links,bank_details,terms_text,co_branding_mode) VALUES(?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE logo_path=VALUES(logo_path),primary_color=VALUES(primary_color),secondary_color=VALUES(secondary_color),tagline=VALUES(tagline),whatsapp=VALUES(whatsapp),email=VALUES(email),website=VALUES(website),social_links=VALUES(social_links),bank_details=VALUES(bank_details),terms_text=VALUES(terms_text),co_branding_mode=VALUES(co_branding_mode)');
$stmt->execute([$agencyId,trim((string)($data['logo_path']??''))?:null,trim((string)($data['primary_color']??''))?:null,trim((string)($data['secondary_color']??''))?:null,trim((string)($data['tagline']??''))?:null,trim((string)($data['whatsapp']??''))?:null,trim((string)($data['email']??''))?:null,trim((string)($data['website']??''))?:null,json_encode($data['social_links']??new stdClass(),JSON_UNESCAPED_UNICODE),json_encode($data['bank_details']??new stdClass(),JSON_UNESCAPED_UNICODE),trim((string)($data['terms_text']??''))?:null,in_array(($data['co_branding_mode']??'hybrid'),['platform','agency','hybrid'],true)?$data['co_branding_mode']:'hybrid']);
respond(['ok'=>true,'agency_id'=>$agencyId]);
