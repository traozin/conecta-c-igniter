<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model {
    public function findById($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function findAll() {
        return $this->db->get('users')->result();
    }

    public function findByEmail($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function insert($data) {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->update('users', $data, ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete('users', ['id' => $id]);
    }
}
