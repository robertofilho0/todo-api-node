# 📝 To-Do API · Laravel

[![CI](https://github.com/robertofilho0/todo-api-node/actions/workflows/ci.yml/badge.svg)](https://github.com/robertofilho0/todo-api-node/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/php-8.3%20%7C%208.4-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-12-FF2D20?logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

API REST de tarefas em **Laravel 12** com autenticação por token (**Sanctum**). Cada usuário tem as próprias tarefas, com status, prioridade, prazo, **etiquetas**, filtros, busca e estatísticas.

## ✨ Funcionalidades

- **Autenticação com Sanctum**: cadastro, login, `me` e logout, com rate limit de 5 tentativas por minuto
- **Tarefas por usuário**, protegidas por **Policy**: as tarefas de outra pessoa retornam `404`, sem revelar que existem
- **Status e prioridade com Enums do PHP 8.1+** (`pendente`, `em_andamento`, `concluida` / `baixa`, `media`, `alta`)
- Registro automático de `concluida_em` e indicador de **tarefa atrasada**
- **Etiquetas (N:N)**: criadas sob demanda por usuário, normalizadas (minúsculas, sem duplicatas) e usadas como filtro
- **Filtros**: status, prioridade, etiqueta, busca no título e na descrição, só atrasadas, ordenação por prioridade, prazo, título ou criação, e paginação
- **Estatísticas**: total por status, atrasadas, que vencem hoje e percentual concluído
- Boas práticas do Laravel: Form Requests, API Resources, Route Model Binding, Factories e Seeders
- **Testes PHPUnit**, **Pint** e **CI** em PHP 8.3 e 8.4, com um job que roda as migrations num **MySQL 8**

## 🧱 Stack

PHP 8.3+ · Laravel 12 · Sanctum 4 · MySQL 8 ou SQLite · PHPUnit · Pint · Docker

## 📁 Estrutura

```
app/
├── Enums/               # StatusTarefa e Prioridade
├── Http/
│   ├── Controllers/Api/ # Auth, Tarefa e Etiqueta
│   ├── Requests/        # validação
│   └── Resources/       # formatação das respostas
├── Models/              # User, Tarefa e Etiqueta
└── Policies/            # TarefaPolicy (só o dono acessa)
database/                # migrations, factories e seeder
routes/api.php
tests/
```

## 🚀 Como rodar

```bash
git clone https://github.com/robertofilho0/todo-api-node.git
cd todo-api-node
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed      # usuário demo@todo.dev / Senha123
php artisan serve
```

Ou com Docker (MySQL): `export APP_KEY=$(php artisan key:generate --show) && docker compose up --build`.

## 📚 Endpoints

Todas as rotas, exceto cadastro e login, exigem `Authorization: Bearer <token>`.

| Método   | Rota                        | Descrição                         |
| -------- | --------------------------- | --------------------------------- |
| `POST`   | `/api/register`             | Cadastro                          |
| `POST`   | `/api/login`                | Login                             |
| `GET`    | `/api/me`                   | Usuário autenticado               |
| `POST`   | `/api/logout`               | Revoga o token                    |
| `GET`    | `/api/tarefas`              | Lista (filtros e paginação)       |
| `POST`   | `/api/tarefas`              | Cria tarefa                       |
| `GET`    | `/api/tarefas/{id}`         | Detalhes                          |
| `PATCH`  | `/api/tarefas/{id}`         | Atualiza (incluindo etiquetas)    |
| `DELETE` | `/api/tarefas/{id}`         | Remove                            |
| `GET`    | `/api/tarefas/estatisticas` | Resumo                            |
| `GET`    | `/api/etiquetas`            | Etiquetas com total de tarefas    |
| `DELETE` | `/api/etiquetas/{id}`       | Remove etiqueta                   |

**Filtros de `/api/tarefas`:** `status`, `prioridade`, `etiqueta`, `busca`, `atrasadas=1`, `ordenar` (`created_at`, `data_limite`, `prioridade`, `titulo`), `direcao` (`asc` ou `desc`) e `per_page`.

```bash
curl -X POST http://localhost:8000/api/tarefas \
  -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" \
  -d '{"titulo":"Estudar Laravel","prioridade":"alta","data_limite":"2026-12-31","etiquetas":["estudos","php"]}'
```

## 🧪 Testes

```bash
composer test      # PHPUnit com SQLite em memória
composer lint      # Pint
```

## 📄 Licença

MIT.

---

Feito por [Roberto Filho](https://github.com/robertofilho0) · [LinkedIn](https://www.linkedin.com/in/roberto-filho-393b372b2/)
