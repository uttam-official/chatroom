<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    private $user, $data;
    public function __construct()
    {
        parent::__construct();
        $this->user = logged_user();
        if (!$this->user) {
            $this->session->set_flashdata('login_err', "Please login to proced!");
            redirect(base_url('login'));
        }
        $this->load->model('usermodel', 'model');
    }
    public function index()
    {
        $this->data['csrf_name'] = $this->security->get_csrf_token_name();
        $this->data['csrf_hash'] = $this->security->get_csrf_hash();
        $this->data['title'] = "Chat with friends";
        $this->data['user'] = $this->session->userdata('user');
        $uid = $this->data['uid'] = $this->user->id;
        $this->data['name'] = $this->user->name;
        $this->data['email'] = $this->user->email;

        $this->data['friends'] = $this->model->getOnlineUser($uid);

        $this->data['content'] = $this->parser->parse('dashboard', $this->data, true);
        $this->parser->parse('templates/base', $this->data);
    }

    public function getchats()
    {
        $fid = $this->input->post('fid');
        if ($fid) {
            $data = $this->model->getchats($this->user->id, $fid);
            echo json_encode(['chat' => $data[0], 'frnd' => $data[1], 'csrf_hash' => $this->security->get_csrf_hash()]);
        }
    }
}