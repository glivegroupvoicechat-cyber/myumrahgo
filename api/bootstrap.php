<?php
declare(strict_types=1);
$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) { http_response_code(503); header('Content-Type: application/json'); echo json_encode(['error'=>'Backend is not configured yet.']); exit; }
$config = require $configPath;
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Karachi');
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['db']['host'], $config['db']['name'], $config['db']['charset'] ?? 'utf8mb4');
try { $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]); }
catch (Throwable $e) { http_response_code(503); header('Content-Type: application/json'); echo json_encode(['error'=>'Database unavailable.']); exit; }
header('Content-Type: application/json; charset=utf-8');
function json_input(): array { $data=json_decode(file_get_contents('php://input'), true); return is_array($data)?$data:[]; }
function respond(array $data,int $status=200): never { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function require_method(string $method): void { if($_SERVER['REQUEST_METHOD']!==$method) respond(['error'=>'Method not allowed'],405); }
function booking_no(): string { return 'MYG-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3))); }
