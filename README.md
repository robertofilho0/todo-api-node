# 📝 To-Do API

[![CI](https://github.com/robertofilho0/todo-api-node/actions/workflows/ci.yml/badge.svg)](https://github.com/robertofilho0/todo-api-node/actions/workflows/ci.yml)
![Node](https://img.shields.io/badge/node-%3E%3D22-339933?logo=node.js&logoColor=white)
![Express](https://img.shields.io/badge/express-5-000000?logo=express)
![MongoDB](https://img.shields.io/badge/mongodb-7-47A248?logo=mongodb&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

API REST para gerenciamento de tarefas, com filtros, busca, paginação, estatísticas, validação de entrada, documentação OpenAPI e testes automatizados.

## ✨ Funcionalidades

- CRUD completo de tarefas (`PUT` e `PATCH` para atualização parcial)
- Status (`pendente`, `em_andamento`, `concluida`), prioridade e data limite
- Registro automático da data de conclusão
- Listagem com **filtros**, **busca por título**, **paginação** e **ordenação** (com whitelist de campos)
- Endpoint de **estatísticas** (total por status e tarefas atrasadas)
- Validação de entrada com **Zod** e respostas de erro padronizadas
- Segurança: **Helmet**, **CORS** configurável, **rate limiting** e limite de tamanho do body
- Documentação interativa com **Swagger UI** em `/docs`
- Health check em `/health` e encerramento gracioso (SIGINT/SIGTERM)
- **Testes de integração** com Jest + Supertest + MongoDB em memória
- **Docker** e **docker-compose** prontos, CI com **GitHub Actions**

## 🧱 Stack

Node.js 22+ · Express 5 · MongoDB / Mongoose · Zod · Jest · Supertest · ESLint · Prettier · Docker

## 📁 Estrutura

```
src/
├── app.js              # configuração do Express (middlewares, rotas)
├── server.js           # conexão com o banco e inicialização do servidor
├── config/             # variáveis de ambiente validadas e conexão com o MongoDB
├── controllers/        # regras de cada endpoint
├── docs/openapi.json   # especificação OpenAPI 3
├── middlewares/        # validação, 404 e tratamento central de erros
├── models/             # schemas do Mongoose
├── routes/             # definição das rotas
├── utils/AppError.js   # erro HTTP de domínio
└── validators/         # schemas Zod
tests/                  # testes de integração e validação
```

## 🚀 Como rodar

### Com Docker (recomendado)

```bash
git clone https://github.com/robertofilho0/todo-api-node.git
cd todo-api-node
docker compose up --build
```

A API sobe em `http://localhost:3000` e a documentação em `http://localhost:3000/docs`.

### Localmente

Pré-requisitos: Node.js 22+ e um MongoDB (local ou Atlas).

```bash
npm install
cp .env.example .env   # ajuste o MONGO_URI
npm run dev
```

### Variáveis de ambiente

| Variável      | Padrão                               | Descrição                                 |
| ------------- | ------------------------------------ | ----------------------------------------- |
| `PORT`        | `3000`                               | Porta HTTP                                |
| `MONGO_URI`   | `mongodb://localhost:27017/todo-api` | String de conexão do MongoDB              |
| `CORS_ORIGIN` | `*`                                  | Origens permitidas, separadas por vírgula |
| `NODE_ENV`    | `development`                        | `development`, `test` ou `production`     |

## 📚 Endpoints

| Método   | Rota                        | Descrição                            |
| -------- | --------------------------- | ------------------------------------ |
| `GET`    | `/health`                   | Status da API e do banco             |
| `GET`    | `/api/tarefas`              | Lista tarefas (filtros e paginação)  |
| `GET`    | `/api/tarefas/estatisticas` | Total por status e tarefas atrasadas |
| `GET`    | `/api/tarefas/:id`          | Busca uma tarefa                     |
| `POST`   | `/api/tarefas`              | Cria uma tarefa                      |
| `PUT`    | `/api/tarefas/:id`          | Atualiza uma tarefa                  |
| `PATCH`  | `/api/tarefas/:id`          | Atualiza uma tarefa                  |
| `DELETE` | `/api/tarefas/:id`          | Remove uma tarefa                    |

**Parâmetros de listagem:** `status`, `prioridade`, `busca`, `page` (padrão 1), `limit` (1–100, padrão 10), `sortBy` (`createdAt`, `updatedAt`, `titulo`, `prioridade`, `dataLimite`) e `order` (`asc` ou `desc`).

### Exemplos

```bash
# Criar
curl -X POST http://localhost:3000/api/tarefas \
  -H "Content-Type: application/json" \
  -d '{"titulo":"Estudar Node.js","prioridade":"alta","dataLimite":"2026-12-31"}'

# Listar tarefas de alta prioridade, 5 por página
curl "http://localhost:3000/api/tarefas?prioridade=alta&limit=5"

# Concluir
curl -X PATCH http://localhost:3000/api/tarefas/<ID> \
  -H "Content-Type: application/json" \
  -d '{"status":"concluida"}'
```

Resposta da listagem:

```json
{
  "data": [
    { "id": "665f...", "titulo": "Estudar Node.js", "status": "pendente", "prioridade": "alta" }
  ],
  "meta": { "total": 1, "page": 1, "limit": 10, "totalPages": 1 }
}
```

Formato padrão de erro:

```json
{
  "error": "Dados inválidos",
  "details": [{ "campo": "titulo", "mensagem": "titulo é obrigatório" }]
}
```

## 🧪 Testes e qualidade

```bash
npm test               # testes (sobe um MongoDB em memória automaticamente)
npm run test:coverage  # com relatório de cobertura
npm run lint           # ESLint
npm run format         # Prettier
```

Para usar um MongoDB já existente nos testes, defina `MONGO_URI_TEST`.

## 📄 Licença

Distribuído sob a licença MIT. Veja [LICENSE](LICENSE).

---

Feito por [Roberto Filho](https://github.com/robertofilho0) · [LinkedIn](https://www.linkedin.com/in/roberto-filho-393b372b2/)
