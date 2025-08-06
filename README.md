# Conecta Lá - API RESTful com CodeIgniter 3

API RESTful desenvolvida em PHP utilizando o framework **CodeIgniter 3** e a biblioteca [chriskacerguis/codeigniter-restserver](https://github.com/chriskacerguis/codeigniter-restserver). O ambiente está completamente dockerizado para facilitar o setup e a execução.

---

## ✅ Requisitos

* Docker
* Docker Compose

---

## 🚀 Inicialização do Projeto

### 1. Clonando o Repositório

```bash
git clone <url-do-repositorio>
cd conecta-la
```

### 2. Inicialização Rápida com `build.sh`

O script `build.sh` automatiza os seguintes passos:

* Remove containers antigos
* Executa `composer install`
* Reconstrói e sobe os containers

#### Executar:

```bash
./build.sh
```

### 3. Acessando a API

Após iniciar, acesse via navegador ou Postman:

```
http://localhost:8000
```

> `index.php` removido das URLs via `.htaccess` com `mod_rewrite` habilitado.

---

## 🐳 Estrutura dos Containers

* `app`: Apache com PHP 7.4 + CodeIgniter 3 + `mod_rewrite`
* `db`: MySQL 5.7 com volume de inicialização do banco de dados

---

## 🛢 Banco de Dados

Um script SQL é executado automaticamente ao iniciar o container MySQL:

```yml
./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
```

### Credenciais padrão:

* **Host**: `db`
* **Database**: `conectala_api`
* **User**: `ci3_user`
* **Password**: `ci3_pass`

---

## 📬 Testes com Postman

Coleção disponível em:

```
public/Conecta Lá.postman_collection.json
```

Importe no Postman para testar os endpoints.

---

## 📡 Rotas Disponíveis

| Método | Endpoint    | Ação              |
| ------ | ----------- | ----------------- |
| GET    | /users      | Listar usuários   |
| GET    | /users/{id} | Ver um usuário    |
| POST   | /users      | Criar usuário     |
| PUT    | /users/{id} | Atualizar usuário |
| DELETE | /users/{id} | Remover usuário   |

---

## 🧩 Considerações

* Respostas JSON estruturadas em todas as requisições
* Validações com `form_validation` envoltas em `try/catch`
* URLs amigáveis sem `index.php` via `.htaccess`
* `mod_rewrite` já habilitado no container Apache via `Dockerfile`

