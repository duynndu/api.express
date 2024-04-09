<?php

use MyApp\Comment;
use Ratchet\Http\HttpServer;
use \Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

(new \Symfony\Component\Dotenv\Dotenv())->load(__DIR__ . '/../.env');

require_once 'vendor/autoload.php';
require_once 'helper.php';


$server = IoServer::factory(new HttpServer(new WsServer(new Comment())), 8080);

$server->run();