<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);
require_method('POST');
$data = json_input();

$items = is_array($data['items'] ?? null) ? $data['items'] : [];
$travellers = max(1, (int)($data['travellers'] ?? 1));
$margin = max(0, (float)($data['margin_pkr'] ?? 0));
$exchange = (float)($data['sar_to_pkr'] ?? 0);
if ($exchange <= 0) respond(['error' => 'sar_to_pkr must be greater than zero'], 422);

$subtotalSar = 0.0;
$subtotalPkr = 0.0;
$lines = [];
foreach ($items as $item) {
    $name = trim((string)($item['name'] ?? 'Item'));
    $currency = strtoupper((string)($item['currency'] ?? 'PKR'));
    $unit = max(0, (float)($item['unit_price'] ?? 0));
    $qty = max(1, (int)($item['qty'] ?? $travellers));
    $amount = $unit * $qty;
    if ($currency === 'SAR') $subtotalSar += $amount;
    else $subtotalPkr += $amount;
    $lines[] = ['name'=>$name,'currency'=>$currency,'unit_price'=>$unit,'qty'=>$qty,'amount'=>$amount];
}

$converted = $subtotalSar * $exchange;
$base = $subtotalPkr + $converted;
$total = $base + $margin;
respond(['ok'=>true,'travellers'=>$travellers,'sar_to_pkr'=>$exchange,'subtotal_sar'=>round($subtotalSar,2),'subtotal_pkr'=>round($subtotalPkr,2),'converted_sar_pkr'=>round($converted,2),'margin_pkr'=>round($margin,2),'total_pkr'=>round($total,2),'lines'=>$lines]);
