const request = require('supertest');
const app = require('../src/app');
const db = require('./helpers/db');

beforeAll(db.connect);
afterEach(db.clear);
afterAll(db.close);

const criar = (dados) => request(app).post('/api/tarefas').send(dados);

describe('CRUD de tarefas', () => {
  it('cria uma tarefa com valores padrão', async () => {
    const res = await criar({ titulo: '  Estudar Node.js  ' });

    expect(res.status).toBe(201);
    expect(res.headers.location).toBe(`/api/tarefas/${res.body.id}`);
    expect(res.body).toMatchObject({
      titulo: 'Estudar Node.js',
      status: 'pendente',
      prioridade: 'media',
    });
    expect(res.body._id).toBeUndefined();
  });

  it('busca, atualiza e remove uma tarefa', async () => {
    const { body: criada } = await criar({ titulo: 'Ler livro' });

    const get = await request(app).get(`/api/tarefas/${criada.id}`);
    expect(get.status).toBe(200);
    expect(get.body.titulo).toBe('Ler livro');

    const upd = await request(app).put(`/api/tarefas/${criada.id}`).send({ status: 'concluida' });
    expect(upd.status).toBe(200);
    expect(upd.body.status).toBe('concluida');
    expect(upd.body.concluidaEm).toBeDefined();

    const reaberta = await request(app)
      .patch(`/api/tarefas/${criada.id}`)
      .send({ status: 'pendente' });
    expect(reaberta.body.concluidaEm).toBeUndefined();

    const del = await request(app).delete(`/api/tarefas/${criada.id}`);
    expect(del.status).toBe(204);

    const depois = await request(app).get(`/api/tarefas/${criada.id}`);
    expect(depois.status).toBe(404);
  });

  it('retorna 404 para tarefa inexistente', async () => {
    const id = '665f1c2b8a1e4b6d2c3f4a5b';
    expect((await request(app).get(`/api/tarefas/${id}`)).status).toBe(404);
    expect((await request(app).patch(`/api/tarefas/${id}`).send({ titulo: 'x' })).status).toBe(404);
    expect((await request(app).delete(`/api/tarefas/${id}`)).status).toBe(404);
  });
});

describe('Listagem', () => {
  beforeEach(async () => {
    await criar({ titulo: 'Comprar pão', prioridade: 'baixa' });
    await criar({ titulo: 'Pagar contas', prioridade: 'alta', status: 'em_andamento' });
    await criar({ titulo: 'Comprar café', prioridade: 'alta', status: 'concluida' });
  });

  it('pagina os resultados', async () => {
    const res = await request(app).get('/api/tarefas?limit=2&page=2');
    expect(res.status).toBe(200);
    expect(res.body.data).toHaveLength(1);
    expect(res.body.meta).toEqual({ total: 3, page: 2, limit: 2, totalPages: 2 });
  });

  it('filtra por status e prioridade', async () => {
    const res = await request(app).get('/api/tarefas?prioridade=alta&status=concluida');
    expect(res.body.data.map((t) => t.titulo)).toEqual(['Comprar café']);
  });

  it('busca pelo título sem diferenciar maiúsculas', async () => {
    const res = await request(app).get('/api/tarefas?busca=COMPRAR&sortBy=titulo&order=asc');
    expect(res.body.data.map((t) => t.titulo)).toEqual(['Comprar café', 'Comprar pão']);
  });

  it('trata caracteres especiais na busca com segurança', async () => {
    const res = await request(app).get('/api/tarefas?busca=.*');
    expect(res.status).toBe(200);
    expect(res.body.data).toHaveLength(0);
  });

  it('retorna estatísticas', async () => {
    await criar({ titulo: 'Atrasada', dataLimite: '2020-01-01' });
    const res = await request(app).get('/api/tarefas/estatisticas');
    expect(res.body).toEqual({
      total: 4,
      porStatus: { pendente: 2, em_andamento: 1, concluida: 1 },
      atrasadas: 1,
    });
  });
});
