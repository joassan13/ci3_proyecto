<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->helper('url');
    }

    // Export users as CSV (Excel-compatible). Optional query param: ids=1,2,3
    public function export_csv()
    {
        $ids = $this->input->get('ids');
        $this->load->dbforge();

        $this->db->select('id, first_name, last_name, email, phone, rfc, curp, gender');
        $this->db->from('users');
        if ($ids) {
            $arr = array_filter(array_map('trim', explode(',', $ids)));
            if (!empty($arr)) $this->db->where_in('id', $arr);
        }
        $query = $this->db->get();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="users_export_' . date('Ymd_His') . '.csv"');
        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        echo "\xEF\xBB\xBF";
        fputcsv($out, ['id','first_name','last_name','email','phone','rfc','curp','gender']);
        foreach ($query->result_array() as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    // Download a CSV template for imports
    public function download_template()
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="users_import_template.csv"');
        echo "\xEF\xBB\xBF"; // BOM
        $out = fopen('php://output', 'w');
        fputcsv($out, ['first_name','last_name','email','phone','rfc','curp','gender']);
        // Example row
        fputcsv($out, ['Alejandra','Villa','alejandra.villa@example.com','5512345678','VILL800101XXX','VIVA800101HDFRRS09','F']);
        fclose($out);
        exit;
    }

    // Export printable HTML (user can Save as PDF from browser)
    public function export_pdf()
    {
        $ids = $this->input->get('ids');
        $this->db->select('id, first_name, last_name, email, phone, rfc, curp, gender');
        $this->db->from('users');
        if ($ids) {
            $arr = array_filter(array_map('trim', explode(',', $ids)));
            if (!empty($arr)) $this->db->where_in('id', $arr);
        }
        $query = $this->db->get();
        $rows = $query->result_array();

        echo '<!doctype html><html><head><meta charset="utf-8"><title>Usuarios</title>';
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">';
        echo '</head><body><div class="container"><h3>Usuarios</h3><table class="table table-bordered">';
        echo '<thead><tr><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Teléfono</th><th>RFC</th><th>CURP</th><th>Sexo</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['first_name']) . '</td>';
            echo '<td>' . htmlspecialchars($r['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($r['email']) . '</td>';
            echo '<td>' . htmlspecialchars($r['phone']) . '</td>';
            echo '<td>' . htmlspecialchars($r['rfc']) . '</td>';
            echo '<td>' . htmlspecialchars($r['curp']) . '</td>';
            echo '<td>' . htmlspecialchars($r['gender']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table><p>Imprima o guarde como PDF desde el navegador.</p></div></body></html>';
    }

}
