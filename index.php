<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
(new \Symfony\Component\Dotenv\Dotenv())->load(__DIR__ . '/.env');

require_once 'helper.php';
header('Content-Type: application/json; charset=utf-8');
cors();
require_once 'router.php';




