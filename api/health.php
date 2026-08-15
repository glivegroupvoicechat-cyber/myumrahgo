<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
header('Cache-Control: no-store');
try {
    $pdo->query('SELECT 1');
    $checks = [];
    foreach (['users','agencies','hotels','flights','packages','bookings','payments'] as $table) {
        try { $pdo->query("SELECT 1 FROM {$table} LIMIT 1"); $checks[$table] = true; }
        catch (Throwable $e) { $checks[$table] = false; }
    }
    $ok = !in_array(false, $checks, true);
    respond(['ok'=>$ok,'service'=>'MyUmrahGo API','database'=>true,'tables'=>$checks,'timestamp'=>gmdate('c')], $ok ? 200 : 503);
} catch (Throwable $e) {
    respond(['ok'=>false,'service'=>'MyUmrahGo API','database'=>false,'error'=>'Database unavailable','timestamp'=>gmdate('c')], 503);
}
