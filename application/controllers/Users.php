<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->helper('url');
    }

    public function index()
    {
        $this->load->view('users/index');
    }

    public function list_users()
    {
        $users = $this->user_model->get_all();
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function create()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First name', 'required');
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

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
        ];

        $id = $this->user_model->insert($data);
        echo json_encode(['success' => (bool)$id, 'id' => $id]);
    }

    public function get($id = null)
    {
        if (!$id) { show_404(); }
        $user = $this->user_model->get($id);
        echo json_encode($user);
    }

    public function update($id = null)
    {
        if (!$id) { show_404(); }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First name', 'required');
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

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
        ];

        $ok = $this->user_model->update($id, $data);
        echo json_encode(['success' => (bool)$ok]);
    }

    public function delete($id = null)
    {
        if (!$id) { show_404(); }
        $ok = $this->user_model->delete($id);
        echo json_encode(['success' => (bool)$ok]);
    }

}
