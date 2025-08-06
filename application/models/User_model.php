<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model {
    public function get_all() {
        return $this->db->get('users')->result_array();
    }

    public function insert($data) {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('users');
    }
}
