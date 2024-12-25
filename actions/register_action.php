<?php
require "../connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    $profile_picture = $_FILES["profile_picture"];

    // Check if inputs are valid
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($profile_picture['name'])) {
        header("Location: ../register.php?err=1");
        exit();
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        header("Location: ../register.php?err=2");
        exit();
    }

    // Check if username or email already exists
    $sql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $existingUserCount = $stmt->fetchColumn();

    if ($existingUserCount > 0) {
        // Redirect with an error code if username/email is taken
        header("Location: ../register.php?err=3");
        exit();
    }

    // Handle profile picture upload
    $upload_dir = "../uploads/";
    $image_file_type = strtolower(pathinfo($profile_picture['name'], PATHINFO_EXTENSION));

    $profile_picture_name = "IMG_" . bin2hex(random_bytes(10)) . '.' . $image_file_type;
    $target_file = $upload_dir . $profile_picture_name;

    // Check if the uploaded file is an image
    if (!getimagesize($profile_picture['tmp_name'])) {
        header("Location: ../register.php?err=8"); // Invalid image file
        exit();
    }

    // Check file size (1MB max)
    if ($profile_picture["size"] > 1000000) {
        header("Location: ../register.php?err=9"); // File size too large
        exit();
    }

    // Check if the file type is valid
    $allowed_types = array("jpg", "jpeg", "png", "gif");
    if (!in_array($image_file_type, $allowed_types)) {
        header("Location: ../register.php?err=7"); // Invalid file type
        exit();
    }

    // Check for any upload error
    if ($profile_picture['error'] != UPLOAD_ERR_OK) {
        header("Location: ../register.php?err=10"); // Error uploading the file
        exit();
    }

    // Move the uploaded file to the uploads directory
    if (!move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
        header("Location: ../register.php?err=10"); // Error uploading the file
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Register the new user
    $sql = "INSERT INTO users (username, email, password, profile_picture) VALUES (:username, :email, :password, :profile_picture)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':profile_picture', $profile_picture_name);
    $stmt->execute();

    // Redirect to login page after successful registration
    header("Location: ../index.php");
}
?>