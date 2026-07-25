<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbcheck extends CI_Controller {

    public function index()
    {
        header('Content-Type: application/json');
        try {
            $query = $this->db->query('SELECT 1 AS ok');
            $row = $query->row();
            echo json_encode(['connected' => true, 'result' => $row]);
        } catch (Exception $e) {
            echo json_encode(['connected' => false, 'error' => $e->getMessage()]);
        }
    }

}
