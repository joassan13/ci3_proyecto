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
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_unique');
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

        // If insert failed, check DB error to detect unique constraint violation.
        // This covers race conditions where two requests pass validation
        // but the DB rejects a duplicate (MySQL error code 1062).
        if (!$id) {
            $dberr = $this->db->error();
            if (isset($dberr['code']) && $dberr['code'] == 1062) {
                echo json_encode(['success' => false, 'errors' => ['email' => 'Correo electrónico en uso (DB constraint)']]);
                return;
            }
            echo json_encode(['success' => false, 'errors' => ['db' => 'Falla en inserción']]);
            return;
        }

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
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_unique['.$id.']');
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

        // If update failed, inspect DB error for duplicate-key (1062).
        if ($ok === FALSE) {
            $dberr = $this->db->error();
            if (isset($dberr['code']) && $dberr['code'] == 1062) {
                echo json_encode(['success' => false, 'errors' => ['email' => 'Correo electrónico en uso (DB constraint)']]);
                return;
            }
            echo json_encode(['success' => false, 'errors' => ['db' => 'Falla en actualización']]);
            return;
        }

        $sess = [
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

    // Callback for form validation: ensure email is unique.
    // When updating, pass the current user id as parameter: callback_email_unique[123]
    public function email_unique($email, $id = null)
    {
        $existing = $this->user_model->get_by_email($email);
        if ($existing) {
            // If updating the same record, it's allowed
            if ($id !== null && isset($existing['id']) && (string)$existing['id'] === (string)$id) {
                return TRUE;
            }
            $this->load->library('form_validation');
            $this->form_validation->set_message('email_unique', 'El %s ya está en uso');
            return FALSE;
        }
        return TRUE;
    }

    // Import users from uploaded CSV (expects header row). Returns JSON report.
    public function import()
    {
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
            return;
        }

        $tmp = $_FILES['file']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if (!$handle) {
            echo json_encode(['success' => false, 'error' => 'Unable to open uploaded file']);
            return;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            echo json_encode(['success' => false, 'error' => 'Empty CSV file']);
            fclose($handle);
            return;
        }

        // Normalize header
        $header = array_map(function($h){ return strtolower(trim($h)); }, $header);
        $expected = ['first_name','last_name','email','phone','rfc','curp','gender'];
        // Map columns to expected names
        $map = [];
        foreach ($expected as $col) {
            $idx = array_search($col, $header);
            // if ($idx === false) {
            //     echo json_encode(['success' => false, 'error' => 'Cabeceras inválidas. Se requiere: ' . implode(',', $expected)]);
            //     fclose($handle);
            //     return;
            // }
            $map[$col] = $idx;
        }

        $inserted = 0; $errors = [];
        $seen_emails = [];

        // Patterns (same as validation rules)
        $rfc_pattern = '/^([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})$/i';
        $curp_pattern = '/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i';
        $phone_pattern = '/^[0-9]{10}$/';

        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = [];
            foreach ($expected as $col) {
                $data[$col] = isset($row[$map[$col]]) ? trim($row[$map[$col]]) : '';
            }

            // Basic validation
            $rowErrors = [];
            if ($data['first_name'] === '') $rowErrors[] = 'first_name required';
            if ($data['last_name'] === '') $rowErrors[] = 'last_name required';
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $rowErrors[] = 'invalid email';
            // Check uniqueness: already in DB or duplicated in this import
            $email_lower = strtolower($data['email']);
            if (in_array($email_lower, $seen_emails) || $this->user_model->get_by_email($data['email'])) {
                $rowErrors[] = 'email already exists';
            }
            if (!preg_match($phone_pattern, $data['phone'])) $rowErrors[] = 'invalid phone';
            if (!preg_match($rfc_pattern, $data['rfc'])) $rowErrors[] = 'invalid rfc';
            if (!preg_match($curp_pattern, $data['curp'])) $rowErrors[] = 'invalid curp';
            if (!in_array($data['gender'], ['M','F','O'])) $rowErrors[] = 'invalid gender';

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $rowNum, 'errors' => $rowErrors];
                continue;
            }

            // Prepare insert array compatible with model
            $ins = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'rfc' => $data['rfc'],
                'curp' => $data['curp'],
                'gender' => $data['gender'],
                'password' => ''
            ];
            // Try inserting the row. If the DB rejects it due to the unique
            // constraint (e.g. concurrent insert), record the error for this row
            // and continue with the next one instead of failing the whole import.
            $res = $this->user_model->insert($ins);
            if (!$res) {
                $dberr = $this->db->error();
                if (isset($dberr['code']) && $dberr['code'] == 1062) {
                    $errors[] = ['row' => $rowNum, 'errors' => ['correo electrónico en uso (restricción DB)']];
                    continue;
                }
                $errors[] = ['row' => $rowNum, 'errors' => ['Error al insertar en la base de datos']];
                continue;
            }

            $inserted++;
            $seen_emails[] = $email_lower;
        }

        fclose($handle);
        echo json_encode(['success' => true, 'inserted' => $inserted, 'errors' => $errors]);
    }


}
