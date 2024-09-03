// db.js
const mysql = require('mysql2/promise');
require('dotenv').config();

const pool = mysql.createPool({
  connectionLimit: 10,
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  dateStrings: true
});


module.exports = pool;




// const pool = mysql.createPool({
//     host: 'localhost',
//     user: 'your_username',
//     password: 'your_password',
//     database: 'your_database',
//     waitForConnections: true,
//     connectionLimit: 10,  // Number of connections to maintain in the pool
//     queueLimit: 0         // Number of queued connection requests
// });
