<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserModel extends CI_Model
{
    public function register($data)
    {
        $status = $this->db->insert('user', $data);
        if ($status) {
            return $this->db->select(['id', 'name', 'email'])->where('id', $this->db->insert_id())->get('user')->row();
        } else {
            return false;
        }
    }
    public function getRegisteredUser()
    {
        $users = $this->db->select(['id', 'name', 'email', 'user_login_status', 'user_login_id'])->where('id!=""')->get('user')->result();
        return $users;
    }
    public function getOnlineUser($uid)
    {
        $users = $this->db->select(['id', 'name', 'email', 'user_login_status', 'user_login_id'])->where('id!=' . $uid)->get('user')->result();
        return $users;
    }
    public function getchats($uid, $fid)
    {
        $user = logged_user();
        $this->db->join('user u', 'c.sender_id=u.id');
        $this->db->select(['c.*', 'u.name']);
        $this->db->where("(c.sender_id=" . $uid . " and c.receiver_id=" . $fid . ") or (c.sender_id=" . $fid . " and c.receiver_id=" . $uid . ")")
            ->order_by('created_at', 'ASC');
        $data = $this->db->get('chat c')->result();
        foreach ($data as  $k => $l) {
            if ($l->sender_id == $user->id) {
                $l->name = 'me';
            }
        }
        $frndName = $this->db->where('id', $fid)->get('user')->row()->name;
        return [$data, $frndName];
    }
}