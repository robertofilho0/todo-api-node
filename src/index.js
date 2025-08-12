require('dotenv').config();
require('express-async-errors'); // para tratar erros async sem try/catch em cada rota

const express = require('express');
const cors = require('cors');
const morgan = require('morgan');

const connectDB = require('./db');
const tarefasRouter = require('./routes/tarefas');
const errorHandler = require('./middleware/errorHandler');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(morgan('dev'));
app.use(express.json());

// rotas
app.use('/api/tarefas', tarefasRouter);

// 404
app.use((req, res) => res.status(404).json({ error: 'Rota não encontrada' }));

// middleware de erro (deve ser o último)
app.use(errorHandler);

// conectar DB e iniciar servidor
(async () => {
  try {
    await connectDB(process.env.MONGO_URI);
    app.listen(PORT, () => console.log(`Servidor rodando na porta ${PORT}`));
  } catch (err) {
    console.error('Erro ao conectar DB:', err);
    process.exit(1);
  }
})();
