const mongoose = require('mongoose');

const connectDB = async (mongoUri) => {
  if (!mongoUri) throw new Error('MONGO_URI não definido no .env');
  await mongoose.connect(mongoUri);
  console.log('Conectado ao MongoDB');
};

module.exports = connectDB;
