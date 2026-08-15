<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

$user = require_auth($pdo);
require_method('POST');
$data = json_input();

$customerName = trim((string)($data['customer_name'] ?? ''));
$title = trim((string)($data['title'] ?? 'Umrah Quotation'));
$validUntil = trim((string)($data['valid_until'] ?? ''));
$total = max(0, (float)($data['total_pkr'] ?? 0));
$items = is_array($data['items'] ?? null) ? $data['items'] : [];

if ($customerName === '') respond(['error'=>'customer_name is required'],422);
if ($total <= 0) respond(['error'=>'total_pkr must be greater than zero'],422);

$agencyId = null;
if (in_array($user['role'], ['agent','sales','booking','accounts','marketing','readonly'], true)) {
    $stmt = $pdo->prepare("SELECT id FROM agencies WHERE owner_user_id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([(int)$user['id']]);
    $agencyId = (int)($stmt->fetchColumn() ?: 0);
    if (!$agencyId) respond(['error'=>'Active agency access is required'],403);
}

$quotationNo = 'QT-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('INSERT INTO quotations (quotation_no, agency_id, title, customer_name, valid_until, total_pkr, status) VALUES (?, ?, ?, ?, ?, ?, ?)' );
    $stmt->execute([$quotationNo, $agencyId ?: null, $title, $customerName, $validUntil ?: null, $total, 'issued']);
    $quotationId = (int)$pdo->lastInsertId();

    $itemStmt = $pdo->prepare('INSERT INTO quotation_items (quotation_id, item_name, currency, unit_price, quantity, amount_pkr) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($items as $item) {
        $itemStmt->execute([
            $quotationId,
            trim((string)($item['name'] ?? 'Item')),
            strtoupper((string)($item['currency'] ?? 'PKR')),
            (float)($item['unit_price'] ?? 0),
            max(1, (int)($item['qty'] ?? 1)),
            (float)($item['amount_pkr'] ?? 0)
        ]);
    }
    audit_log($pdo, (int)$user['id'], 'quotation.create', 'quotation', $quotationId, ['quotation_no'=>$quotationNo,'total_pkr'=>$total]);
    $pdo->commit();
    respond(['ok'=>true,'id'=>$quotationId,'quotation_no'=>$quotationNo,'status'=>'issued'],201);
} catch (Throwable $e) {
    $pdo->rollBack();
    respond(['error'=>'Quotation could not be created'],500);
}
