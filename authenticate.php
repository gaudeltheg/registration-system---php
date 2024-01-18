<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "login";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Authentication successful
        $_SESSION['email'] = $row['email'];
        header("Location: welcome.php");
        exit;
    } else {
        // Incorrect password
        header("Location: login.php?error=Invalid+password");
        exit;
    }
} else {
    // User not found
    header("Location: login.php?error=User+not+found");
    exit;
}

$conn->close();
?>
