/**
 * Erro de domínio com status HTTP associado.
 * Lançado pelos controllers/services e convertido em resposta pelo errorHandler.
 */
class AppError extends Error {
  constructor(statusCode, message, details) {
    super(message);
    this.name = 'AppError';
    this.statusCode = statusCode;
    this.details = details;
  }

  static badRequest(message = 'Requisição inválida', details) {
    return new AppError(400, message, details);
  }

  static unauthorized(message = 'Não autenticado') {
    return new AppError(401, message);
  }

  static forbidden(message = 'Acesso negado') {
    return new AppError(403, message);
  }

  static notFound(message = 'Recurso não encontrado') {
    return new AppError(404, message);
  }

  static conflict(message = 'Conflito') {
    return new AppError(409, message);
  }
}

module.exports = AppError;
