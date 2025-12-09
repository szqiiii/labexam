<?php
$host = 'localhost';
$user = 'root';
$pass = '';  
$dbname = 'lab_exam_db';


$conn = mysqli_connect($host, $user, $pass);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    echo "Error creating database: " . mysqli_error($conn);
}


mysqli_select_db($conn, $dbname);


$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $sql)) {
    echo "Error creating users table: " . mysqli_error($conn);
}


$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if (!mysqli_query($conn, $sql)) {
    echo "Error creating messages table: " . mysqli_error($conn);
}

$check = mysqli_query($conn, "SELECT * FROM users WHERE username='testuser'");
if (mysqli_num_rows($check) == 0) {
    $sql = "INSERT INTO users (username, password) VALUES ('testuser', 'testpass123')";
    mysqli_query($conn, $sql);
}

session_start();
?>