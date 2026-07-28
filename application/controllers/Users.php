<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->helper('url');
    }

    // Compatibility wrapper: allow requests to users/list
    // public function list()
    // {
    //     $this->list_users();
    // }

    // Load create form view (non-AJAX)
    public function create_view()
    {
        $this->load->view('users/create');
    }

    // Load edit form view (non-AJAX), prefill user data
    public function edit_view($id = null)
    {
        if (!$id) { show_404(); }
        $user = $this->user_model->get($id);
        if (!$user) { show_404(); }
        $data = ['user' => $user];
        $this->load->view('users/edit', $data);
    }

    public function index()
    {
        $this->load->view('users/index');
    }

    public function list_users()
    {
        $users = $this->user_model->get_all();
        // Remove password before returning
        foreach ($users as &$u) {
            if (isset($u['password'])) unset($u['password']);
        }
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    // alias for compatibility with view's AJAX URL: users/list
    // public function list()
    // {
    //     $this->list_users();
    // }

    public function create()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First name', 'required');
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        // Additional rules: RFC, CURP, phone, gender
        $this->form_validation->set_rules('rfc', 'RFC', "trim|required|regex_match[/^([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})$/i]");
        $this->form_validation->set_rules('curp', 'CURP', "trim|required|regex_match[/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i]");
        $this->form_validation->set_rules('phone', 'Teléfono', "trim|required|regex_match[/^[0-9]{10}$/]");
        $this->form_validation->set_rules('gender', 'Sexo', "trim|required|in_list[M,F,O]");
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'rfc' => $this->input->post('rfc'),
            'curp' => $this->input->post('curp'),
            'gender' => $this->input->post('gender'),
        ];

        // Hash password according to selected algorithm
        $password = $this->input->post('password');
        $algo = $this->input->post('hash_algo') ?: 'bcrypt';
        if ($password !== null) {
            if ($algo === 'bcrypt') {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            } elseif (in_array($algo, ['md5','sha1','sha256'])) {
                $data['password'] = hash($algo, $password);
            } else {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
        }

        $id = $this->user_model->insert($data);
        echo json_encode(['success' => (bool)$id, 'id' => $id]);
    }

    public function get($id = null)
    {
        if (!$id) { show_404(); }
        $user = $this->user_model->get($id);
        if (isset($user['password'])) unset($user['password']);
        echo json_encode($user);
    }

    public function update($id = null)
    {
        if (!$id) { show_404(); }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First name', 'required');
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        // Additional rules for update
        $this->form_validation->set_rules('rfc', 'RFC', "trim|required|regex_match[/^([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})$/i]");
        $this->form_validation->set_rules('curp', 'CURP', "trim|required|regex_match[/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i]");
        $this->form_validation->set_rules('phone', 'Teléfono', "trim|required|regex_match[/^[0-9]{10}$/]");
        $this->form_validation->set_rules('gender', 'Sexo', "trim|required|in_list[M,F,O]");
        // password is optional on update; only validate if provided
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'rfc' => $this->input->post('rfc'),
            'curp' => $this->input->post('curp'),
            'gender' => $this->input->post('gender'),
        ];

        // If a new password was provided, hash it before updating
        $password = $this->input->post('password');
        $algo = $this->input->post('hash_algo') ?: 'bcrypt';
        if ($password) {
            if ($algo === 'bcrypt') {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            } elseif (in_array($algo, ['md5','sha1','sha256'])) {
                $data['password'] = hash($algo, $password);
            } else {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
        }

        $ok = $this->user_model->update($id, $data);

        $sess = [
            // 'id' => $user['id'],
            // 'email' => $user['email'],
            'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
            'last_name' => isset($data['last_name']) ? $data['last_name'] : ''
        ];
        $this->session->set_userdata('user', $sess);

        echo json_encode(['success' => (bool)$ok]);
    }

    public function delete($id = null)
    {
        if (!$id) { show_404(); }
        $ok = $this->user_model->delete($id);
        echo json_encode(['success' => (bool)$ok]);
    }

    public function chart_data()
    {
        $this->load->model('User_model');
        $total = $this->user_model->count_all();
        $by_gender = $this->user_model->count_group_by('gender');

        header('Content-Type: application/json');
        echo json_encode(['total' => $total, 'by_gender' => $by_gender]);
    }

}
