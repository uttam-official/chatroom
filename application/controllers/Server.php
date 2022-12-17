<?php
class Server extends CI_Controller
{
    public function run()
    {
        $this->socket_server->run();
    }
}