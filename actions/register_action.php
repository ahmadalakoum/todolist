<?php
require "../connection.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username= trim($_POST["username"]);
    $email= trim($_POST["email"]);
    $password= trim($_POST["password"]);
    $confirmPassword=trim($_POST["confirm_password"]);
    
    //check if inputs are valid
    if(empty($username) || empty($email) || empty($password) || empty($confirmPassword)){
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
     echo $existingUserCount;

     if ($existingUserCount > 0) {
         // Redirect with an error code if username/email is taken
         header("Location: ../register.php?err=3");
         exit();
     }
    // register a new user 
    $hashedPassword= password_hash($password,PASSWORD_BCRYPT);
    $sql="INSERT INTO users (username,email,password) VALUES (:username,:email,:password)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();
    header("Location:../index.php");

}