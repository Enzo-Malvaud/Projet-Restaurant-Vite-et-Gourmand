// mongodb-config.js
// Configuration MongoDB automatique selon l'environnement

require('dotenv').config({ 
  path: process.env.NODE_ENV === 'production' ? '.env.production' : '.env.local' 
});

const mongoose = require('mongoose');

const mongoConfig = {
  local: {
    uri: process.env.MONGODB_URI || 'mongodb://admin:password123@localhost:27017/myapp?authSource=admin',
    options: {
      maxPoolSize: 5,
      serverSelectionTimeoutMS: 5000,
    }
  },
  production: {
    uri: process.env.MONGODB_URI || 'mongodb://mongoadmin:SecurePassword123!@#@mongodb:27017/myapp_prod?authSource=admin',
    options: {
      maxPoolSize: 20,
      serverSelectionTimeoutMS: 5000,
      retryWrites: true,
      w: 'majority'
    }
  }
};

const env = process.env.NODE_ENV || 'local';
const config = mongoConfig[env];

/**
 * Connecter à MongoDB
 */
async function connectMongoDB() {
  try {
    console.log(`🔄 Connexion à MongoDB (${env})...`);
    console.log(`📍 URI: ${config.uri.replace(/:[^@]+@/, ':****@')}`); // Cache le password
    
    await mongoose.connect(config.uri, config.options);
    
    console.log(`✅ MongoDB connecté avec succès!`);
    console.log(`📊 Base de données: ${mongoose.connection.db.getName()}`);
    
    return mongoose.connection;
  } catch (error) {
    console.error('❌ Erreur de connexion MongoDB:', error.message);
    process.exit(1);
  }
}

/**
 * Déconnecter de MongoDB
 */
async function disconnectMongoDB() {
  try {
    await mongoose.disconnect();
    console.log('✅ Déconnecté de MongoDB');
  } catch (error) {
    console.error('❌ Erreur de déconnexion:', error.message);
  }
}

/**
 * Vérifier la santé de la connexion
 */
async function checkConnectionHealth() {
  try {
    const admin = mongoose.connection.getClient().db('admin');
    const result = await admin.command({ ping: 1 });
    return result.ok === 1;
  } catch (error) {
    return false;
  }
}

module.exports = {
  connectMongoDB,
  disconnectMongoDB,
  checkConnectionHealth,
  config,
  mongoose
};