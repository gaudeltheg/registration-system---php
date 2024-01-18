<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: welcome.php");
    exit;
}

// Display error message
function displayError($error) {
    echo '<p class="error">' . $error . '</p>';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define your database connection credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "login";

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");

        // Bind the parameter
        $stmt->bindParam(':email', $email);

        // Execute the query
        $stmt->execute();

        // Fetch the user from the result set
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a user with the given email exists
        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_start();

                // Store the user data in the session
                $_SESSION['username'] = $user['username'];

                // Redirect to the welcome page
                header("Location: welcome.php");
                exit;
            } else {
                // Password is incorrect
                $error = "Invalid email or password.";
                displayError($error);
            }
        } else {
            // No user found with the given email
            $error = "Invalid email or password.";
            displayError($error);
        }
    } catch (PDOException $e) {
        // Display any errors
        $error = "Error: " . $e->getMessage();
        displayError($error);
    }

    // Close the database connection
    $pdo = null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .form-group input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
