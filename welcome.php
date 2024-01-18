<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $targetDir = "profile_pictures/";
    $targetFile = $targetDir . basename($_FILES['profile_picture']['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image or a fake image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        $error = "File already exists.";
        $uploadOk = 0;
    }

    // Check file size (optional)
    // You can set a maximum file size if required.
    // For example, to set a maximum file size of 1MB (1,048,576 bytes), use:
    // if ($_FILES['profile_picture']['size'] > 1048576) {
    //     $error = "File is too large. Maximum size allowed is 1MB.";
    //     $uploadOk = 0;
    // }

    // Allow only certain image file formats (e.g., jpeg and png)
    if ($imageFileType !== "jpg" && $imageFileType !== "jpeg" && $imageFileType !== "png") {
        $error = "Only JPG, JPEG, and PNG files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        // File upload failed
        // You can display the error message or handle it differently as per your requirement.
        // For example, redirect back to the form with an error message:
        header("Location: welcome.php?error=" . urlencode($error));
        exit;
    } else {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            // File moved successfully, save the file path to the database for the user
            // You can replace the placeholder URL with the actual URL in the database.
            // For example:
            // $profilePictureUrl = "profile_pictures/" . basename($_FILES['profile_picture']['name']);
            $profilePictureUrl = $targetFile;
        } else {
            $error = "Error uploading the file.";
            // File upload failed
            // You can display the error message or handle it differently as per your requirement.
            // For example, redirect back to the form with an error message:
            header("Location: welcome.php?error=" . urlencode($error));
            exit;
        }
    }
}

echo "Session username: " . $username;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
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

        .profile {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .profile p {
            font-weight: bold;
            font-size: 18px;
        }

        .upload-form {
            text-align: center;
        }

        .upload-form input[type="file"] {
            margin-top: 10px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $username; ?></h2>
        <div class="profile">
            <?php
            // Display the profile picture and username
            // Fetch the profile picture URL from the database for the logged-in user
            // Replace the placeholder URL with the actual URL from the database
            $profilePictureUrl = 'profile_pictures/default.jpg';
            ?>
            <img src="<?php echo $profilePictureUrl; ?>" alt="Profile Picture">
            <p><?php echo $username; ?></p>
        </div>
        <div class="upload-form">
            <form action="welcome.php" method="POST" enctype="multipart/form-data">
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png">
                <br>
                <input type="submit" value="Upload">
            </form>
            <?php
            // Display error message if any
            if (isset($error)) {
                echo '<p class="error">' . $error . '</p>';
            }
            ?>
        </div>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
