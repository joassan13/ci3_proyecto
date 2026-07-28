<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'security']);
    }

    public function index()
    {
        // If already logged in, redirect
        if ($this->session->userdata('user')) {
            redirect('users');
            return;
        }
        $this->load->view('auth/login');
    }

    public function login()
    {
        // If already logged in, redirect
        if ($this->session->userdata('user')) {
            redirect('users');
            return;
        }
        $this->load->view('auth/login');
    }

    public function do_login()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $is_ajax = $this->input->is_ajax_request();

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            if ($is_ajax) {
                echo json_encode(['success' => false, 'error' => trim($errors)]);
            } else {
                $this->session->set_flashdata('login_error', $errors);
                redirect('auth/login');
            }
            return;
        }

        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->user_model->get_by_email($email);
        if (!$user) {
            $msg = 'Usuario o contraseña incorrectos.';
            if ($is_ajax) { echo json_encode(['success' => false, 'error' => $msg]); }
            else { $this->session->set_flashdata('login_error', $msg); redirect('auth/login'); }
            return;
        }

        $valid = false;
        // Prefer password_verify for bcrypt
        if (!empty($user['password']) && password_needs_rehash($user['password'], PASSWORD_BCRYPT) === false) {
            // Try password_verify; suppress warnings if not a valid hash
            if (@password_verify($password, $user['password'])) $valid = true;
        }

        // Fallbacks for other algorithms: plain md5/sha1/sha256 hashes stored
        if (!$valid) {
            if (!empty($user['password']) && hash('sha256', $password) === $user['password']) $valid = true;
            elseif (!empty($user['password']) && hash('sha1', $password) === $user['password']) $valid = true;
            elseif (!empty($user['password']) && md5($password) === $user['password']) $valid = true;
        }

        if (!$valid) {
            $msg = 'Usuario o contraseña incorrectos.';
            if ($is_ajax) { echo json_encode(['success' => false, 'error' => $msg]); }
            else { $this->session->set_flashdata('login_error', $msg); redirect('auth/login'); }
            return;
        }

        // Build session payload (avoid storing password)
        $sess = [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => isset($user['first_name']) ? $user['first_name'] : '',
            'last_name' => isset($user['last_name']) ? $user['last_name'] : ''
        ];
        $this->session->set_userdata('user', $sess);

        if ($is_ajax) {
            echo json_encode(['success' => true, 'user' => $sess]);
        } else {
            redirect('users');
        }
    }

    public function logout()
    {
        // Unset only the user key to preserve other session data if needed
        $this->session->unset_userdata('user');
        // Optionally destroy the session completely
        // $this->session->sess_destroy();
        redirect('auth/login');
    }
}
