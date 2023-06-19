<?php

namespace App\WebSocket;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use function Symfony\Component\Translation\t;


class MessageHandler implements MessageComponentInterface
{
    protected $connections;
    protected EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        foreach ($this->connections as $connection) {
            $numRecv = count($this->connections) - 1;
            echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
            $message = json_decode($msg, true);
            if ($connection === $from) {
                if ($message!=null) {
                    var_dump($message['method']);
                    $chat = new Message();
                    $chat
                        ->setClient($message['name'])
                        ->setText($message['message']);
                    $this->manager->persist($chat);
                    $this->manager->flush();
                }
                continue;
            }
            $connection->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}