const request = require('supertest');
const app = require('../src/app');

// Estes testes não acessam o banco: a validação rejeita antes.
describe('Validação e rotas básicas', () => {
  it('GET /health responde ok', async () => {
    const res = await request(app).get('/health');
    expect(res.status).toBe(200);
    expect(res.body.status).toBe('ok');
  });

  it('retorna 404 padronizado para rota inexistente', async () => {
    const res = await request(app).get('/api/nao-existe');
    expect(res.status).toBe(404);
    expect(res.body.error).toMatch(/não encontrada/);
  });

  it('rejeita criação sem título', async () => {
    const res = await request(app).post('/api/tarefas').send({ descricao: 'sem título' });
    expect(res.status).toBe(400);
    expect(res.body.details[0].campo).toBe('titulo');
  });

  it('rejeita status fora do enum', async () => {
    const res = await request(app).post('/api/tarefas').send({ titulo: 'x', status: 'feito' });
    expect(res.status).toBe(400);
  });

  it('rejeita campos desconhecidos', async () => {
    const res = await request(app).post('/api/tarefas').send({ titulo: 'x', admin: true });
    expect(res.status).toBe(400);
  });

  it('rejeita ID inválido', async () => {
    const res = await request(app).get('/api/tarefas/123');
    expect(res.status).toBe(400);
    expect(res.body.details[0].mensagem).toBe('ID inválido');
  });

  it('rejeita JSON malformado', async () => {
    const res = await request(app)
      .post('/api/tarefas')
      .set('Content-Type', 'application/json')
      .send('{"titulo":');
    expect(res.status).toBe(400);
    expect(res.body.error).toMatch(/JSON inválido/);
  });

  it('rejeita limit acima de 100 e sortBy arbitrário', async () => {
    expect((await request(app).get('/api/tarefas?limit=1000')).status).toBe(400);
    expect((await request(app).get('/api/tarefas?sortBy=senha')).status).toBe(400);
  });

  it('rejeita atualização vazia', async () => {
    const res = await request(app).patch('/api/tarefas/665f1c2b8a1e4b6d2c3f4a5b').send({});
    expect(res.status).toBe(400);
  });

  it('aplica headers de segurança', async () => {
    const res = await request(app).get('/health');
    expect(res.headers['x-content-type-options']).toBe('nosniff');
    expect(res.headers['x-powered-by']).toBeUndefined();
  });
});
