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

    public function index_get($id = null) {
        if ($id === null) {
            $users = $this->User_model->findAll();
            return $this->response($users, 200);
        }

        $user = $this->User_model->findById($id);
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        return $this->response($user, 200);
    }

    public function index_post() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('name', 'Nome', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Senha', 'required|min_length[6]');

            if (!$this->form_validation->run()) {
                throw new Exception(json_encode($this->form_validation->error_array()), 400);
            }

            // Verifica se o e-mail já existe
            $email_exists = $this->db->where('email', $data['email'])->get('users')->row();
            if ($email_exists) {
                throw new Exception(json_encode(['email' => 'O email já está em uso.']), 400);
            }

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
            if (!$id || !$this->User_model->findById($id)) {
                throw new Exception("Usuário não encontrado.", 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('name', 'Nome', 'required|min_length[3]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback__unique_email_except[' . $id . ']');

            if (!$this->form_validation->run()) {
                throw new Exception(json_encode($this->form_validation->error_array()), 400);
            }

            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            $success = $this->User_model->update($id, $data);

            if (!$success) {
                throw new Exception("Erro ao atualizar usuário.", 500);
            }

            $this->response(['message' => 'Usuário atualizado com sucesso.'], 200);
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
        try {
            if (!$id || !$this->User_model->findById($id)) {
                throw new Exception("Usuário não encontrado.", 404);
            }

            $deleted = $this->User_model->delete($id);

            if (!$deleted) {
                throw new Exception("Erro ao excluir usuário.", 500);
            }

            $this->response(['message' => 'Usuário deletado com sucesso.'], 200);
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->response([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function _unique_email_except($email, $id) {
        $user = $this->User_model->findByEmail($email);
        if ($user && $user->id != $id) {
            $this->form_validation->set_message('_unique_email_except', 'O email já está em uso.');
            return false;
        }
        return true;
    }
}
