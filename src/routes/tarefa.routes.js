const { Router } = require('express');
const controller = require('../controllers/tarefa.controller');
const validate = require('../middlewares/validate');
const schemas = require('../validators/tarefa.validator');

const router = Router();

router.get('/', validate({ query: schemas.listQuery }), controller.list);
router.get('/estatisticas', controller.stats);
router.post('/', validate({ body: schemas.createBody }), controller.create);
router.get('/:id', validate({ params: schemas.idParams }), controller.getById);
router.put(
  '/:id',
  validate({ params: schemas.idParams, body: schemas.updateBody }),
  controller.update,
);
router.patch(
  '/:id',
  validate({ params: schemas.idParams, body: schemas.updateBody }),
  controller.update,
);
router.delete('/:id', validate({ params: schemas.idParams }), controller.remove);

module.exports = router;
