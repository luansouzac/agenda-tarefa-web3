# Agenda de Tarefas

Sistema de agendamento de tarefas com cadastro de usuários e login.

- **`api-agenda-tarefa/`**: API em Laravel com autenticação via Sanctum (token)
- **`lista-tarefa/`**: frontend em Vue 3 + Vuetify + Pinia

## Requisitos

- PHP 8.2+ e Composer
- Node.js 22.18+ (ou 24.12+) e npm

O banco usado é SQLite, então não é preciso instalar MySQL.

## Como rodar

### 1. API (Laravel)

```bash
cd api-agenda-tarefa
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

A API fica em `http://127.0.0.1:8000/api`.

> No Windows, troque `cp` por `copy` e crie o arquivo `database/database.sqlite` vazio manualmente no lugar do `touch`.

### 2. Frontend (Vue)

Em outro terminal:

```bash
cd lista-tarefa
npm install
npm run dev
```

Acesse `http://localhost:5173`.

## Como logar

O comando `php artisan migrate --seed` cria um usuário de teste e as categorias **Trabalho**, **Pessoal** e **Família**:

| E-mail             | Senha    |
|--------------------|----------|
| `test@example.com` | `123456` |

Também dá para criar uma conta nova pelo link **Criar conta** na tela de login.

Para recriar o banco do zero (apaga todos os dados):

```bash
php artisan migrate:fresh --seed
```

## Testes da API

```bash
cd api-agenda-tarefa
php artisan test
```

## Rotas da API

| Método | Rota                | Autenticação | Descrição                          |
|--------|---------------------|--------------|------------------------------------|
| POST   | `/api/register`     | não          | Cria a conta e devolve o token     |
| POST   | `/api/login`        | não          | Faz login e devolve o token        |
| GET    | `/api/user`         | sim          | Dados do usuário logado            |
| POST   | `/api/logout`       | sim          | Revoga o token atual               |
| CRUD   | `/api/tarefas`      | sim          | Tarefas do usuário logado          |
| CRUD   | `/api/categorias`   | sim          | Categorias                         |

As rotas autenticadas esperam o header `Authorization: Bearer <token>`.
