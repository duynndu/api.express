<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// Đảm bảo các phần phụ thuộc của nhà soạn nhạc đã được cài đặt
require 'vendor/autoload.php';

/**
 * chat.php
 * Gửi bất kỳ tin nhắn đến nào tới tất cả các máy khách được kết nối (except sender)
 */
class MyChat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

// Chạy ứng dụng máy chủ thông qua giao thức WebSocket trên cổng 8080
$app = new Ratchet\App('localhost', 8080);
$app->route('/chat', new MyChat, array('*'));
$app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
$app->run();