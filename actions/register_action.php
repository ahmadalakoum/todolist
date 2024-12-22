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