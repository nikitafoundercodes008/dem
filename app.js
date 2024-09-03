const express = require('express');
const path = require('path');
var cors = require('cors');
const ejs = require('ejs');
var bodyParser = require('body-parser');
var session=require('express-session');
const crypto = require('crypto');

const pool = require('./db');


const app = express();
const port = process.env.PORT;

// Import route files
const apiRoutes = require('./routes/api');
const adminRoutes = require('./routes/admin');

// Middleware to handle JSON requests
const secret = crypto.randomBytes(64).toString('hex');

app.use(session({
  secret: `${secret}`, // Secret used to sign the session ID cookie
  resave: false,
  saveUninitialized: true,
  cookie: { secure: false }, // Change this to true if using HTTPS
}));

app.use(cors());
app.use("/", express.static(path.join(__dirname, 'public')));
app.set("view engine", "ejs");
app.use(express.json({limit: '50mb'}));
app.use(express.urlencoded({limit: '50mb'}));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
    extended: true
}));

// Middleware to attach the connection pool to the request object
app.use((req, res, next) => {
    req.db = pool; // Attach pool to req
    next();
});

// Define routes
app.use('/prodsite', adminRoutes);
app.use('/api', apiRoutes);

// Default route
app.get('/', (req, res) => {
    res.send('Main Function');
});

// Start the server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}/`);
});
