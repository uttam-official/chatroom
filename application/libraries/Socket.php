<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
    public $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('chatuser_model', 'model');
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $query_string = $conn->httpRequest->getUri()->getQuery();
        $user = json_decode($this->CI->encryption->decrypt($query_string));
        $this->CI->model->setUserLoginId($user->id, $conn->resourceId);
        // Store the new connection in $this->clients
        $this->clients->attach($conn);
        foreach ($this->clients as $client) {
            if ($client->resourceId != $conn->resourceId) {
                $client->send(json_encode(['type' => 'online', 'fid' => $user->id]));
            }
        }

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg);
        $user = $this->CI->model->getUser(0, $from->resourceId);
        $receiver = $this->CI->model->getUser($msg->receiver, 0);
        if ($msg->type == 'msg_notify' && $receiver->user_login_id) {
            foreach ($this->clients as $client) {
                if ($client->resourceId == $receiver->user_login_id) {
                    $client->send(json_encode(['type' => 'msg_notify', 'fid' => $user->id]));
                }
            }
        } elseif ($msg->type == 'msg') {
            $this->CI->model->saveChat($user->id, $msg);
            foreach ($this->clients as $client) {
                if ($from->resourceId == $client->resourceId) {
                    $client->send(json_encode(['type' => 'msg', 'user' => 'me', 'msg' => $msg->message, 'time' => date('h:i:s A jS-F-Y')]));
                } elseif ($receiver->user_login_id && $receiver->user_login_id == $client->resourceId) {
                    $client->send(json_encode(['type' => 'msg', 'user' => $user->name, 'fid' => $user->id, 'msg' => $msg->message, 'time' => date('h:i:s A jS-F-Y')]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $user = $this->CI->model->getUser(0, $conn->resourceId);
        foreach ($this->clients as $client) {
            if ($client->resourceId != $conn->resourceId) {
                $client->send(json_encode(['type' => 'offline', 'fid' => $user->id]));
            }
        }
        $this->CI->model->setUserConnClose($conn->resourceId);
        echo "Connection Closed! ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }
}