const Tarefa = require('../models/tarefa');

exports.createTarefa = async (req, res) => {
  const { titulo, descricao, status } = req.body;
  if (!titulo) return res.status(400).json({ error: 'titulo é obrigatório' });

  const tarefa = await Tarefa.create({ titulo, descricao, status });
  return res.status(201).json(tarefa);
};

exports.getTarefas = async (req, res) => {
  const { status, page = 1, limit = 10, sortBy = 'createdAt', order = 'desc' } = req.query;

  const filter = {};
  if (status) filter.status = status;

  const skip = (Number(page) - 1) * Number(limit);
  const sort = { [sortBy]: order === 'desc' ? -1 : 1 };

  const total = await Tarefa.countDocuments(filter);
  const tarefas = await Tarefa.find(filter).sort(sort).skip(skip).limit(Number(limit));

  res.json({ total, page: Number(page), limit: Number(limit), tarefas });
};

exports.getTarefaById = async (req, res) => {
  const tarefa = await Tarefa.findById(req.params.id);
  if (!tarefa) return res.status(404).json({ error: 'Tarefa não encontrada' });
  res.json(tarefa);
};

exports.updateTarefa = async (req, res) => {
  const { titulo, descricao, status } = req.body;
  const updated = await Tarefa.findByIdAndUpdate(
    req.params.id,
    { titulo, descricao, status },
    { new: true, runValidators: true }
  );
  if (!updated) return res.status(404).json({ error: 'Tarefa não encontrada' });
  res.json(updated);
};

exports.deleteTarefa = async (req, res) => {
  const deleted = await Tarefa.findByIdAndDelete(req.params.id);
  if (!deleted) return res.status(404).json({ error: 'Tarefa não encontrada' });
  res.json({ message: 'Tarefa deletada com sucesso' });
};
