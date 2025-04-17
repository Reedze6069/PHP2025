<?php
// db_connect.php - Database connection script using MySQLi

// Database credentials
$servername = "localhost";
$username   = "root";   // TODO: replace with your MySQL username
$password   = "";   // TODO: replace with your MySQL password
$dbname     = "food-order";         // Your MySQL database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the charset to utf8mb4 for Unicode support and security
$conn->set_charset("utf8mb4");

// The $conn object will be used for database queries in other scripts.
?>
