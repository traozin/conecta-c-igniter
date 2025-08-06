<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Users extends RestController {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model');
    }

    public function index_get() {
        $users = $this->User_model->get_all();
        $this->response($users, 200);
    }

    public function index_post() {
        $data = $this->input->post();

        $id = $this->User_model->insert($data);
        if ($id) {
            $this->response(['id' => $id, 'message' => 'Usuário criado.'], 201);
        } else {
            $this->response(['error' => 'Erro ao criar usuário.'], 500);
        }
    }

    public function index_put($id = null) {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->User_model->update($id, $data)) {
            $this->response(['message' => 'Usuário atualizado.'], 200);
        } else {
            $this->response(['error' => 'Erro ao atualizar usuário.'], 500);
        }
    }

    public function index_delete($id = null) {
        if ($this->User_model->delete($id)) {
            $this->response(['message' => 'Usuário deletado.'], 200);
        } else {
            $this->response(['error' => 'Erro ao deletar usuário.'], 500);
        }
    }
}
