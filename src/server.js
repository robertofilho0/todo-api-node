const app = require('./app');
const env = require('./config/env');
const { connectDB, disconnectDB } = require('./config/db');

async function start() {
  await connectDB(env.MONGO_URI);

  const server = app.listen(env.PORT, () => {
    console.log(`🚀 API rodando em http://localhost:${env.PORT}`);
    console.log(`📚 Documentação em http://localhost:${env.PORT}/docs`);
  });

  const shutdown = (signal) => {
    console.log(`\n${signal} recebido. Encerrando...`);
    server.close(async () => {
      await disconnectDB();
      process.exit(0);
    });
  };

  process.on('SIGINT', shutdown);
  process.on('SIGTERM', shutdown);
}

start().catch((err) => {
  console.error('❌ Falha ao iniciar a aplicação:', err);
  process.exit(1);
});
