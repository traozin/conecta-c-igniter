<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Testdb extends CI_Controller {

    public function index() {
        $this->load->database();

        if ($this->db->conn_id) {
            echo "Conexão com banco MySQL via Docker: OK";
        } else {
            echo "Erro na conexão com banco.";
        }
    }
}
