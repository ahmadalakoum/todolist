<?php
require "../connection.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email= $_POST["email"];
    $password= $_POST["password"];
    
    //check if inputs are valid
    if( empty(trim($email)) || empty(trim($password)) ){
        echo "All fields are required";
        exit();
    }
    //login the user
    $sql= "SELECT id,username,email, password FROM users WHERE email =:email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$user){
        echo "Invalid email or password";
        exit();
    }

    if($user){
        if(password_verify($password,$user["password"])){
            //start a session
            session_start();
            $_SESSION["user_id"]=$user['id'];
            $_SESSION["username"]=$user["username"];
            $_SESSION["isLoggedIn"]=true;
            //redirect to home page
            header("Location:../index.php");

        }
    }
}