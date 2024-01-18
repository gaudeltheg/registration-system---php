<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the file was accessed directly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Password validation criteria
    $uppercase = preg_match('@[A-Z]@', $password); // At least one uppercase letter
    $lowercase = preg_match('@[a-z]@', $password); // At least one lowercase letter
    $number = preg_match('@[0-9]@', $password); // At least one digit
    $specialChars = preg_match('@[^\w]@', $password); // At least one special character
    $minLength = 8; // Minimum password length

    // Check if password meets the criteria
    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < $minLength) {
        echo "<script>alert('Password should contain at least one uppercase letter, one lowercase letter, one digit, one special character, and be at least 8 characters long.'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Connect to the database
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbname = "login";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the username already exists
    $usernameCheckQuery = "SELECT * FROM users WHERE username = '$username'";
    $usernameCheckResult = $conn->query($usernameCheckQuery);

    if ($usernameCheckResult->num_rows > 0) {
        // Username already exists
        echo "<script>alert('Username is already in use. Please choose a different username.'); window.history.back();</script>";
        exit;
    }

    // Check if the email is already in use
    $emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
    $emailCheckResult = $conn->query($emailCheckQuery);

    if ($emailCheckResult->num_rows > 0) {
        // Email is already in use
        echo "<script>alert('Email is already in use. Please choose a different email.'); window.history.back();</script>";
        exit;
    }

    // Insert data into the database with hashed password
    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashedPassword', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration completed successfully'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
        }

        .registration-form {
            text-align: center;
        }

        .registration-form input[type="text"],
        .registration-form input[type="password"],
        .registration-form input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .registration-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .registration-form button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration Page</h2>
        <div class="registration-form">
            <form action="" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <br>
                <button type="submit">Register</button>
            </form>
            <?php
            if (isset($error)) {
                echo '<p class="error">' . $error . '</p>';
            }
            ?>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
