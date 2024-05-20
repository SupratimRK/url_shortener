<?php
// MySQL connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "url_shortener";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}