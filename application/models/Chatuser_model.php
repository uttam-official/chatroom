<?php
class Chatuser_Model extends CI_Model
{
    public function setUserLoginId($user_id, $login_id)
    {
        $this->db->where('id', $user_id)
            ->update('user', ['user_login_id' => $login_id, 'user_login_status' => 1]);
        return true;
    }
    public function setUserConnClose($login_id)
    {
        $this->db->where('user_login_id', $login_id)
            ->update('user', ['user_login_id' => 0, 'user_login_status' => 0]);
        return true;
    }
    public function getUser($user_id = 0, $login_id = 0)
    {
        $user_id ? $this->db->where('id', $user_id) : '';
        $login_id ? $this->db->where('user_login_id', $login_id) : '';
        return $this->db->get('user')->row();
    }
    public function saveChat($user_id, $msg)
    {
        $data = [
            'sender_id' => $user_id,
            'receiver_id' => $msg->receiver,
            'message' => $msg->message
        ];
        $this->db->insert('chat', $data);
        return true;
    }
}