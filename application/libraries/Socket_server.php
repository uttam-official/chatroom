<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'Socket.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Socket_server
{
    public function run()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Socket()
                )
            ),
            9500
            // ,'192.168.1.5'
        );

        $server->run();
    }
}