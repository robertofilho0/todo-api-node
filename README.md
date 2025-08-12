# 📝 To-Do API - Node.js + Express + MongoDB

> API simples para gerenciamento de tarefas com operações CRUD, filtros por status, paginação e ordenação.  
> Desenvolvida com **Node.js**, **Express** e **MongoDB**.

---

## 📌 Ajustes e melhorias

O projeto ainda está em desenvolvimento e as próximas atualizações serão voltadas para as seguintes tarefas:

- [x] CRUD completo de tarefas
- [x] Filtro por status
- [x] Paginação e ordenação
- [ ] Autenticação de usuários
- [ ] Testes automatizados

---

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:

- **Node.js** v16+ e **npm** instalados
- Uma conta no [MongoDB Atlas](https://www.mongodb.com/cloud/atlas) ou instância local do MongoDB
- Compatível com **Windows**, **Linux** e **macOS**

---

## 🚀 Instalando ToDo API

**1. Clone o repositório:**
```bash
git clone https://github.com/seuusuario/todo-api-node.git
cd todo-api-node
```
**2. Instale as dependências:**
```bash
npm install
```
**3. Configure o arquivo .env:**
```.env
PORT=3000
MONGO_URI=sua_connection_string_mongodb_atlas

```
**4. Execute a aplicação:**
```bash
npm run dev
```

## ☕ Usando ToDo API
Endpoints:

``POST    /api/tarefas       -> Criar nova tarefa
``

``
GET     /api/tarefas       -> Listar tarefas
``

``
GET     /api/tarefas/:id   -> Buscar tarefa por ID
``

``
PUT     /api/tarefas/:id   -> Atualizar tarefa
``

``
DELETE  /api/tarefas/:id   -> Deletar tarefa
``

**Exemplos curl:**

- Criar tarefa:
````bash 
curl -X POST http://localhost:3000/api/tarefas \
-H "Content-Type: application/json" \
-d '{"titulo":"Estudar Node.js"}'

````
- Listar tarefas:
````bash 
curl http://localhost:3000/api/tarefas
````
- Buscar por ID:
````bash 
curl http://localhost:3000/api/tarefas/<ID_AQUI>
````
- Atualizar tarefa:
````bash 
curl -X PUT http://localhost:3000/api/tarefas/<ID_AQUI> \
-H "Content-Type: application/json" \
-d '{"status":"concluida"}'
````
- Deletar tarefa:
````bash 
curl -X DELETE http://localhost:3000/api/tarefas/<ID_AQUI>
````

