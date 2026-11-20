const AppError = require('../utils/AppError');

module.exports = (req, _res, next) => {
  next(AppError.notFound(`Rota ${req.method} ${req.originalUrl} não encontrada`));
};
