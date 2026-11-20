const AppError = require('../utils/AppError');

const formatIssues = (issues) =>
  issues.map((issue) => ({
    campo: issue.path.join('.') || undefined,
    mensagem: issue.message,
  }));

/**
 * Valida body, query e params com schemas do Zod.
 * Os dados já convertidos ficam em `req.validated.{body,query,params}`
 * e o body validado também substitui `req.body`.
 */
const validate = (schemas) => (req, _res, next) => {
  req.validated = req.validated || {};

  for (const key of ['params', 'query', 'body']) {
    if (!schemas[key]) continue;

    const result = schemas[key].safeParse(req[key] ?? {});
    if (!result.success) {
      return next(AppError.badRequest('Dados inválidos', formatIssues(result.error.issues)));
    }

    req.validated[key] = result.data;
    if (key === 'body') req.body = result.data;
  }

  return next();
};

module.exports = validate;
