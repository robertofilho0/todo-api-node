const AppError = require('../utils/AppError');

/**
 * Converte qualquer erro em uma resposta JSON padronizada:
 * { "error": "mensagem", "details": [...] }
 */
module.exports = (err, req, res, _next) => {
  let error = err;

  // JSON malformado no body
  if (err.type === 'entity.parse.failed') {
    error = AppError.badRequest('JSON inválido no corpo da requisição');
  }
  // Mongoose: ID em formato inválido
  else if (err.name === 'CastError') {
    error = AppError.badRequest(`Valor inválido para o campo "${err.path}"`);
  }
  // Mongoose: falha de validação do schema
  else if (err.name === 'ValidationError' && err.errors) {
    error = AppError.badRequest(
      'Dados inválidos',
      Object.values(err.errors).map((e) => ({ campo: e.path, mensagem: e.message })),
    );
  }
  // Mongo: chave única duplicada
  else if (err.code === 11000) {
    const campo = Object.keys(err.keyValue || {})[0];
    error = AppError.conflict(
      campo ? `Já existe um registro com este ${campo}` : 'Registro duplicado',
    );
  }
  // Sequelize: chave única duplicada / validação
  else if (err.name === 'SequelizeUniqueConstraintError') {
    error = AppError.conflict('Já existe um registro com estes dados');
  } else if (err.name === 'SequelizeValidationError') {
    error = AppError.badRequest(
      'Dados inválidos',
      err.errors.map((e) => ({ campo: e.path, mensagem: e.message })),
    );
  }
  // JWT inválido/expirado
  else if (err.name === 'JsonWebTokenError' || err.name === 'TokenExpiredError') {
    error = AppError.unauthorized(
      err.name === 'TokenExpiredError' ? 'Token expirado' : 'Token inválido',
    );
  }

  const statusCode = error instanceof AppError ? error.statusCode : error.statusCode || 500;
  const isServerError = statusCode >= 500;

  if (isServerError && process.env.NODE_ENV !== 'test') {
    console.error(err);
  }

  const body = {
    error:
      isServerError && process.env.NODE_ENV === 'production'
        ? 'Erro interno do servidor'
        : error.message || 'Erro interno do servidor',
  };
  if (error.details) body.details = error.details;

  res.status(statusCode).json(body);
};
