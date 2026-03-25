db = db.getSiblingDB('myapp');

// Créer collections
db.createCollection('users');
db.createCollection('products');
db.createCollection('orders');
db.createCollection('logs');

// Créer des indexes
db.users.createIndex({ email: 1 }, { unique: true });
db.users.createIndex({ createdAt: -1 });

db.products.createIndex({ name: 1 });
db.products.createIndex({ category: 1 });
db.products.createIndex({ price: 1 });

db.orders.createIndex({ userId: 1 });
db.orders.createIndex({ createdAt: -1 });
db.orders.createIndex({ status: 1 });

db.logs.createIndex({ timestamp: -1 });
db.logs.createIndex({ level: 1 });

// Créer un utilisateur applicatif (pas le root)
db.createUser({
  user: 'appuser',
  pwd: 'apppassword123',
  roles: [
    {
      role: 'readWrite',
      db: 'myapp'
    }
  ]
});

// Log de confirmation
print('✓ MongoDB initialized with collections and indexes');
print('✓ User "appuser" created');