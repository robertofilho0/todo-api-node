const { z } = require('zod');
const { STATUS, PRIORIDADES } = require('../models/Tarefa');

const objectId = z.string().regex(/^[a-f\d]{24}$/i, 'ID inválido');

const base = {
  titulo: z.string().trim().min(1, 'titulo é obrigatório').max(120),
  descricao: z.string().trim().max(1000).optional(),
  status: z.enum(STATUS).optional(),
  prioridade: z.enum(PRIORIDADES).optional(),
  dataLimite: z.coerce.date().optional(),
};

const idParams = z.object({ id: objectId });

const createBody = z.object(base).strict();

const updateBody = z
  .object(base)
  .partial()
  .strict()
  .refine((data) => Object.keys(data).length > 0, {
    message: 'Informe ao menos um campo para atualizar',
  });

const listQuery = z.object({
  status: z.enum(STATUS).optional(),
  prioridade: z.enum(PRIORIDADES).optional(),
  busca: z.string().trim().min(1).max(100).optional(),
  page: z.coerce.number().int().min(1).default(1),
  limit: z.coerce.number().int().min(1).max(100).default(10),
  sortBy: z
    .enum(['createdAt', 'updatedAt', 'titulo', 'prioridade', 'dataLimite'])
    .default('createdAt'),
  order: z.enum(['asc', 'desc']).default('desc'),
});

module.exports = { idParams, createBody, updateBody, listQuery };
