<?php

if (!function_exists('set_socket_user')) {
    function set_socket_user($u_id, $conn_id)
    {
        $CI = &get_instance();
        $CI->db->where('id', $u_id);
        $CI->db->set(['user_login_id' => $conn_id, 'user_login_status' => 1]);
        $CI->db->update('user');
    }
}