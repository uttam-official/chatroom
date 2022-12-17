<?php

if (!function_exists('logged_user')) {
    function logged_user()
    {
        $CI = &get_instance();
        if ($CI->session->userdata('user')) {
            return json_decode($CI->encryption->decrypt($CI->session->userdata('user')));
        }
        return false;
    }
}