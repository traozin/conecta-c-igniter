<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

/**
 * @property User_model $User_model
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 */
class Users extends RestController {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index_get() {
        $users = $this->User_model->get_all();
        $this->response($users, 200);
    }

    public function index_post() {
        try {
            $this->form_validation->set_data($this->input->post());

            $this->form_validation->set_rules('name', 'Nome', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('password', 'Senha', 'required|min_length[6]');

            if (!$this->form_validation->run()) {
                throw new Exception(json_encode($this->form_validation->error_array()), 400);
            }

            $data = $this->input->post();
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            $id = $this->User_model->insert($data);

            if (!$id) {
                throw new Exception("Erro ao criar usuário.", 500);
            }

            $this->response(['id' => $id, 'message' => 'Usuário criado.'], 201);

        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;

            $decoded = json_decode($e->getMessage(), true);

            $this->response([
                'status' => false,
                'errors' => $decoded ?: ['error' => $e->getMessage()]
            ], $code);
        }
    }

    public function index_put($id = null) {
        try {
            if (!$id || !is_numeric($id)) {
                throw new Exception("ID inválido.", 400);
            }

            $data = json_decode(file_get_contents("php://input"), true);

            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('name', 'Nome', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', "required|valid_email|callback__unique_email_except[$id]");

            if (!$this->form_validation->run()) {
                throw new Exception(json_encode($this->form_validation->error_array()), 400);
            }

            if (isset($data['password']) && strlen($data['password']) > 0) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            } else {
                unset($data['password']);
            }

            if (!$this->User_model->update($id, $data)) {
                throw new Exception("Erro ao atualizar usuário.", 500);
            }

            $this->response(['message' => 'Usuário atualizado.'], 200);

        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            $decoded = json_decode($e->getMessage(), true);

            $this->response([
                'status' => false,
                'errors' => $decoded ?: ['error' => $e->getMessage()]
            ], $code);
        }
    }

    public function index_delete($id = null) {
        if ($this->User_model->delete($id)) {
            $this->response(['message' => 'Usuário deletado.'], 200);
        } else {
            $this->response(['error' => 'Erro ao deletar usuário.'], 500);
        }
    }

    public function _unique_email_except($email, $id) {
        $exists = $this->db->where('email', $email)
            ->where('id !=', $id)
            ->get('users')
            ->row();

        if ($exists) {
            $this->form_validation->set_message('_unique_email_except', 'O campo {field} já está em uso.');
            return FALSE;
        }

        return true;
    }

}
