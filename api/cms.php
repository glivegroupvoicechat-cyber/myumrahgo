<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user=require_auth($pdo);
if(!in_array($user['role'],['admin','super_admin'],true)) respond(['error'=>'Admin access required'],403);
$method=$_SERVER['REQUEST_METHOD']??'GET';
if($method==='GET'){
  $type=(string)($_GET['type']??'content');
  if($type==='banners'){
    $stmt=$pdo->query("SELECT id,title,subtitle,image_path,link_url,sort_order,starts_at,ends_at,status,created_at,updated_at FROM promotional_banners ORDER BY sort_order ASC, created_at DESC LIMIT 250");
    respond(['ok'=>true,'banners'=>$stmt->fetchAll()]);
  }
  if($type==='notifications'){
    $stmt=$pdo->query("SELECT id,audience_type,audience_id,title,message,action_url,status,published_at,created_at FROM notifications ORDER BY created_at DESC LIMIT 250");
    respond(['ok'=>true,'notifications'=>$stmt->fetchAll()]);
  }
  $stmt=$pdo->query("SELECT id,content_key,title,body,image_path,status,updated_by,updated_at FROM cms_content ORDER BY content_key ASC");
  respond(['ok'=>true,'content'=>$stmt->fetchAll()]);
}
require_method('POST');
$data=json_input();
$type=(string)($data['type']??'content');
if($type==='content'){
  $key=trim((string)($data['content_key']??''));$title=trim((string)($data['title']??''));$body=(string)($data['body']??'');
  if($key==='')respond(['error'=>'content_key is required'],422);
  $status=in_array(($data['status']??'draft'),['draft','published','archived'],true)?$data['status']:'draft';
  $stmt=$pdo->prepare('INSERT INTO cms_content(content_key,title,body,image_path,status,updated_by) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE title=VALUES(title),body=VALUES(body),image_path=VALUES(image_path),status=VALUES(status),updated_by=VALUES(updated_by)');
  $stmt->execute([$key,$title?:null,$body,trim((string)($data['image_path']??''))?:null,$status,(int)$user['id']]);
  respond(['ok'=>true,'content_key'=>$key]);
}
if($type==='banner'){
  $title=trim((string)($data['title']??''));if($title==='')respond(['error'=>'title is required'],422);
  $status=in_array(($data['status']??'draft'),['draft','published','archived'],true)?$data['status']:'draft';
  $stmt=$pdo->prepare('INSERT INTO promotional_banners(title,subtitle,image_path,link_url,sort_order,starts_at,ends_at,status,created_by) VALUES(?,?,?,?,?,?,?,?,?)');
  $stmt->execute([$title,trim((string)($data['subtitle']??''))?:null,trim((string)($data['image_path']??''))?:null,trim((string)($data['link_url']??''))?:null,(int)($data['sort_order']??0),$data['starts_at']??null,$data['ends_at']??null,$status,(int)$user['id']]);
  respond(['ok'=>true,'banner_id'=>(int)$pdo->lastInsertId()],201);
}
if($type==='notification'){
  $title=trim((string)($data['title']??''));$message=trim((string)($data['message']??''));
  if($title===''||$message==='')respond(['error'=>'title and message are required'],422);
  $audience=in_array(($data['audience_type']??'all'),['all','customer','agency','staff','admin'],true)?$data['audience_type']:'all';
  $status=in_array(($data['status']??'draft'),['draft','published','archived'],true)?$data['status']:'draft';
  $published=$status==='published'?date('Y-m-d H:i:s'):null;
  $stmt=$pdo->prepare('INSERT INTO notifications(audience_type,audience_id,title,message,action_url,status,published_at,created_by) VALUES(?,?,?,?,?,?,?,?)');
  $stmt->execute([$audience,$data['audience_id']??null,$title,$message,trim((string)($data['action_url']??''))?:null,$status,$published,(int)$user['id']]);
  respond(['ok'=>true,'notification_id'=>(int)$pdo->lastInsertId()],201);
}
respond(['error'=>'Unknown CMS type'],422);
