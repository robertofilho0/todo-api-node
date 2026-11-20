const Tarefa = require('../models/Tarefa');
const AppError = require('../utils/AppError');

const escapeRegex = (text) => text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

async function findOrFail(id) {
  const tarefa = await Tarefa.findById(id);
  if (!tarefa) throw AppError.notFound('Tarefa não encontrada');
  return tarefa;
}

exports.create = async (req, res) => {
  const tarefa = await Tarefa.create(req.body);
  res.status(201).location(`/api/tarefas/${tarefa.id}`).json(tarefa);
};

exports.list = async (req, res) => {
  const { status, prioridade, busca, page, limit, sortBy, order } = req.validated.query;

  const filter = {};
  if (status) filter.status = status;
  if (prioridade) filter.prioridade = prioridade;
  if (busca) filter.titulo = { $regex: escapeRegex(busca), $options: 'i' };

  const [total, tarefas] = await Promise.all([
    Tarefa.countDocuments(filter),
    Tarefa.find(filter)
      .sort({ [sortBy]: order === 'desc' ? -1 : 1, _id: 1 })
      .skip((page - 1) * limit)
      .limit(limit),
  ]);

  res.json({
    data: tarefas,
    meta: { total, page, limit, totalPages: Math.ceil(total / limit) || 1 },
  });
};

exports.getById = async (req, res) => {
  res.json(await findOrFail(req.params.id));
};

exports.update = async (req, res) => {
  const tarefa = await findOrFail(req.params.id);
  tarefa.set(req.body);
  await tarefa.save();
  res.json(tarefa);
};

exports.remove = async (req, res) => {
  const tarefa = await findOrFail(req.params.id);
  await tarefa.deleteOne();
  res.status(204).end();
};

exports.stats = async (_req, res) => {
  const porStatus = await Tarefa.aggregate([{ $group: { _id: '$status', total: { $sum: 1 } } }]);
  const resumo = Object.fromEntries(Tarefa.STATUS.map((s) => [s, 0]));
  porStatus.forEach(({ _id, total }) => {
    resumo[_id] = total;
  });
  const atrasadas = await Tarefa.countDocuments({
    status: { $ne: 'concluida' },
    dataLimite: { $lt: new Date() },
  });
  res.json({
    total: Object.values(resumo).reduce((a, b) => a + b, 0),
    porStatus: resumo,
    atrasadas,
  });
};
