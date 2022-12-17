<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Auth extends CI_Controller
{
    private $data;
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation', null, 'validation');
        $this->load->model('usermodel', 'model');
    }
    public function login_view()
    {
        if (logged_user()) {
            redirect(base_url());
        }
        $this->data['title'] = "User login";
        $this->data['content'] = $this->parser->parse('login', $this->data, true);
        $this->parser->parse('templates/blank', $this->data);
    }
    public function validate()
    {
        if (logged_user()) {
            redirect(base_url());
        }
        if ($this->input->method() != 'post') redirect(base_url('login'));
        $this->validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->validation->set_rules('pass', 'Password', 'trim|required|min_length[8]');

        if ($this->validation->run() == false) {
            $this->session->set_flashdata('login_err', validation_errors());
            redirect(base_url('login'));
        } else {
            $email = $this->input->post('email');
            $pass = $this->input->post('pass');

            $user = $this->db->select(['id', 'name', 'email', 'pass'])->where('email', $email)->get('user')->row();
            if (password_verify($pass, $user->pass)) {
                unset($user->pass);
                $this->session->set_userdata('user', $this->encryption->encrypt(json_encode($user)));
                redirect(base_url('/'));
            } else {
                $this->session->set_flashdata('login_err', "Cradential error!");
                redirect(base_url('login'));
            }
        }
    }
    public function register_view()
    {
        if (logged_user()) {
            redirect(base_url());
        }
        $this->data['title'] = "User registration";
        $this->data['content'] = $this->parser->parse('register', $this->data, true);
        $this->parser->parse('templates/blank', $this->data);
    }
    public function register()
    {
        if (logged_user()) {
            redirect(base_url());
        }
        if ($this->input->method() != 'post') redirect(base_url('register'));
        $this->validation->set_rules('name', 'Name', 'trim|required');
        $this->validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.email]');
        $this->validation->set_rules('pass', 'Password', 'trim|required|min_length[8]');
        $this->validation->set_rules('con_pass', 'Confirm password', 'trim|required|matches[pass]');

        if ($this->validation->run() == false) {
            $this->session->set_flashdata('form_err', validation_errors());
            redirect(base_url('register'));
        } else {
            $this->data['name'] = $this->input->post('name');
            $this->data['email'] = $this->input->post('email');
            $this->data['pass'] = password_hash($this->input->post('pass'), PASSWORD_BCRYPT);
            $user = $this->model->register($this->data);
            if ($user) {
                $this->session->set_userdata('user', $this->encryption->encrypt(json_encode($user)));
                redirect(base_url('/'));
            } else {
                $this->session->set_flashdata('form_err', 'Something went wrong!');
                redirect(base_url('register'));
            }
        }
    }
    public function logout()
    {
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}