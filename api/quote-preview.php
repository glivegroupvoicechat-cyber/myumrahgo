<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';
$user = require_auth($pdo);
require_method('POST');
$data = json_input();

$fx = (float)($data['sar_to_pkr'] ?? 77);
if ($fx <= 0) respond(['error'=>'sar_to_pkr must be greater than zero'],422);
$travellers = max(1,(int)($data['travellers'] ?? 1));
$margin = max(0,(float)($data['margin_pkr'] ?? 0));
$items = is_array($data['items'] ?? null) ? $data['items'] : [];
$allowed = ['flight','visa','makkah_hotel','madinah_hotel','transport','ziyarat','other'];
$breakdown=[];$basePkr=0.0;
foreach($items as $item){
    if(!is_array($item)) continue;
    $type=(string)($item['type'] ?? 'other');
    if(!in_array($type,$allowed,true)) continue;
    $currency=strtoupper((string)($item['currency'] ?? 'PKR'));
    $amount=max(0,(float)($item['amount'] ?? 0));
    $qty=max(1,(float)($item['qty'] ?? 1));
    $pkr=$currency==='SAR' ? $amount*$fx : $amount;
    $line=$pkr*$qty;
    $basePkr += $line;
    $breakdown[]=['type'=>$type,'label'=>trim((string)($item['label'] ?? ucfirst(str_replace('_',' ',$type)))),'qty'=>$qty,'unit_pkr'=>round($pkr,2),'total_pkr'=>round($line,2)];
}
$totalBeforeMargin=$basePkr;
$grandTotal=$totalBeforeMargin+$margin;
$perPerson=$grandTotal/$travellers;
respond(['ok'=>true,'currency'=>'PKR','sar_to_pkr'=>$fx,'travellers'=>$travellers,'margin_pkr'=>round($margin,2),'base_pkr'=>round($basePkr,2),'grand_total_pkr'=>round($grandTotal,2),'per_person_pkr'=>round($perPerson,2),'breakdown'=>$breakdown]);
