<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';

require_method('GET');
$user = require_auth($pdo);
respond(['ok' => true, 'user' => $user]);
