const mongoose = require('mongoose');
const { MongoMemoryServer } = require('mongodb-memory-server');
(async () => {
  const version = process.argv[2];
  const mongod = await MongoMemoryServer.create(version ? { binary: { version } } : {});
  console.log('server', version || 'default', mongod.getUri());
  try {
    await mongoose.connect(mongod.getUri(), { serverSelectionTimeoutMS: 5000 });
    console.log('OK connected', (await mongoose.connection.db.admin().serverInfo()).version);
  } catch (e) { console.log('FAIL', e.message); }
  await mongoose.disconnect(); await mongod.stop();
})();
