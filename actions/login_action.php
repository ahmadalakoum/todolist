<?php
require "../connection.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email= trim($_POST["email"]);
    $password= trim($_POST["password"]);
    
    //check if inputs are valid
    if( empty($email) || empty($password) ){
        header("Location: ../login.php?err=1");
    }
    //login the user
    $sql= "SELECT id,username,email, password FROM users WHERE email =:email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$user){
        header("Location: ../login.php?err=4");
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