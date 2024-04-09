<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use src\Controllers\Client\CommentController;

class Comment implements MessageComponentInterface
{
    protected $clients;
    private $subscribe = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Thêm một đối tượng vào Kho lưu trữ đối tượng $clients
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = (array)json_decode($msg);
        $msg = (array)json_decode($msg[0]);
        $action = $msg['action'];
        switch ($action) {
            case 'subscribe':
            {
                $this->subscribe[$from->resourceId] = $msg['article_id'];
                echo 'ConnectId: ' . $from->resourceId . ' subscribed singlePost with id = ' . $msg['article_id'] . "\n";
                $from->send(json_encode(['subscribed' => $msg['article_id']]));
                break;
            }
            case 'postComment':
            {
                print_r($msg);
                print_r($this->subscribe);
                $comment = (new CommentController())->postComment($msg);
                if (!empty($comment)) {
                    foreach ($this->clients as $client) {
                        if ($this->subscribe[$client->resourceId] == $msg['article_id']) {
                            $client->send($comment);
                        }
                    }
                }
                break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

}