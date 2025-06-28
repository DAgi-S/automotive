<?php
if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'automotive2');
} else {
    define('DB_HOST', 'localhost'); // or your host's DB host if different
    define('DB_USER', 'nati');
    define('DB_PASS', 'Nati-0911');
    define('DB_NAME', 'automotive');
}

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 