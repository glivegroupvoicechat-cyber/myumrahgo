<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_method('POST');

session_start();
$userId=(int)($_SESSION['myumrahgo_user_id'] ?? 0);
if($userId<1) respond(['error'=>'Authentication required'],401);
$stmt=$pdo->prepare("SELECT id,role FROM users WHERE id=? LIMIT 1");
$stmt->execute([$userId]);
$user=$stmt->fetch();
if(!$user || !in_array($user['role'],['admin','super_admin'],true)) respond(['error'=>'Admin access required'],403);

$data=json_input();
$action=(string)($data['action'] ?? '');
if($action==='approve_agency'){
    $agencyId=(int)($data['agency_id'] ?? 0);
    if($agencyId<1) respond(['error'=>'Invalid agency'],422);
    $pdo->beginTransaction();
    try{
        $stmt=$pdo->prepare("UPDATE agencies SET status='active' WHERE id=? AND status='pending'");
        $stmt->execute([$agencyId]);
        if($stmt->rowCount()!==1){$pdo->rollBack();respond(['error'=>'Agency not found or already processed'],404);}
        $stmt=$pdo->prepare("SELECT owner_user_id FROM agencies WHERE id=?");
        $stmt->execute([$agencyId]);
        $ownerId=(int)$stmt->fetchColumn();
        $pdo->prepare("UPDATE users SET status='active', role='agent' WHERE id=?")->execute([$ownerId]);
        $pdo->prepare("INSERT INTO audit_logs(user_id,action,entity_type,entity_id,metadata) VALUES(?,?,?,?,?)")
            ->execute([$userId,'agency.approved','agency',$agencyId,json_encode(['owner_user_id'=>$ownerId])]);
        $pdo->commit();
        respond(['success'=>true,'agency_id'=>$agencyId,'status'=>'active']);
    }catch(Throwable $e){$pdo->rollBack();respond(['error'=>'Unable to approve agency'],500);}
}
respond(['error'=>'Unknown action'],400);
