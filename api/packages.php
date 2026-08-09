<?php
require __DIR__.'/bootstrap.php';
require_method('GET');
$stmt=$pdo->query("SELECT id,name,slug,duration_nights,makkah_nights,madinah_nights,base_price_pkr,description FROM packages WHERE status='published' ORDER BY created_at DESC");
respond(['data'=>$stmt->fetchAll()]);
