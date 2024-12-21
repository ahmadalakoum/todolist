<?php
require "connection.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch the task details
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    //delete the task
    $sql = "DELETE FROM tasks WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $task_id);
    $stmt->execute();
    header("Location: index.php");
}