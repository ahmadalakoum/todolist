<?php
require "connection.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}


if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username=trim($_POST["username"]);
    $email=trim($_POST["email"]);
if(empty($username) || empty($email)){
    header("Location: changeInformation.php?err=1");
    exit();
} 
if($username === $_SESSION["username"] && $email=== $_SESSION["email"]){
    header("Location: changeInformation.php?err=7");
    exit();
}
$sql="UPDATE users SET username=:username, email=:email WHERE id=:id";

$stmt=$pdo->prepare($sql);

$stmt->bindParam(":username", $username);

$stmt->bindParam(":email", $email);

$stmt->bindParam(":id", $_SESSION["user_id"]);

$stmt->execute();

$_SESSION["username"]=$username;

$_SESSION["email"]=$email;

header("Location: index.php");

}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change personal Information</title>
    <link rel="stylesheet" href="styles/change.css">
</head>
<body>
    <h1>Change your personal information</h1>
    <form method="POST" action="changeInformation.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $_SESSION["username"];?>" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $_SESSION["email"];?>" required>
        <button type="submit">Update</button>
    </form>
    <?php
        if(isset($_GET["err"])){
            if($_GET["err"]==1){
                echo "<p style='color:red; text-align:center; margin-top:10px;'>All fields are required</p>";
            }
            if($_GET["err"]==7){
                echo "<p style='color:red; text-align:center; margin-top:10px;'>Username and Email should be different</p>";
            }
        }
    ?>
</body>
</html>