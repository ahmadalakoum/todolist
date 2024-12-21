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
    //mark task as completed
    $sql = "UPDATE tasks SET status='completed' WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $task_id);

    // Execute the query
    $stmt->execute();
    header("Location: index.php");
    exit();

}