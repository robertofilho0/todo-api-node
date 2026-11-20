const mongoose = require('mongoose');

const STATUS = ['pendente', 'em_andamento', 'concluida'];
const PRIORIDADES = ['baixa', 'media', 'alta'];

const tarefaSchema = new mongoose.Schema(
  {
    titulo: { type: String, required: true, trim: true, maxlength: 120 },
    descricao: { type: String, trim: true, maxlength: 1000 },
    status: { type: String, enum: STATUS, default: 'pendente', index: true },
    prioridade: { type: String, enum: PRIORIDADES, default: 'media', index: true },
    dataLimite: { type: Date },
    concluidaEm: { type: Date },
  },
  {
    timestamps: true,
    toJSON: {
      versionKey: false,
      transform: (_doc, ret) => {
        ret.id = ret._id;
        delete ret._id;
        return ret;
      },
    },
  },
);

// Registra automaticamente quando a tarefa foi concluída
tarefaSchema.pre('save', function () {
  if (this.isModified('status')) {
    this.concluidaEm = this.status === 'concluida' ? new Date() : undefined;
  }
});

tarefaSchema.index({ titulo: 'text', descricao: 'text' });

module.exports = mongoose.model('Tarefa', tarefaSchema);
module.exports.STATUS = STATUS;
module.exports.PRIORIDADES = PRIORIDADES;
