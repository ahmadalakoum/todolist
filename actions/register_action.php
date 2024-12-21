<?php
require "../connection.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username= $_POST["username"];
    $email= $_POST["email"];
    $password= $_POST["password"];
    $confirmPassword=$_POST["confirm_password"];
    
    //check if inputs are valid
    if(empty(trim($username)) || empty(trim($email)) || empty(trim($password)) || empty(trim($confirmPassword))){
        echo "All fields are required";
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