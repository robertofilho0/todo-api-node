const mongoose = require('mongoose');

const TarefaSchema = new mongoose.Schema({
  titulo: { type: String, required: true, trim: true },
  descricao: { type: String, trim: true },
  status: { type: String, enum: ['pendente','concluida'], default: 'pendente' },
}, { timestamps: true });

module.exports = mongoose.model('Tarefa', TarefaSchema);
