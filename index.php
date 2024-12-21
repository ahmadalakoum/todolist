<?php
require "connection.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_title = trim($_POST["task_title"]);
    $task_description = trim($_POST["task_description"]);
    $due_date = $_POST["due_date"];

    if (empty($task_title) || empty($task_description)) {
        echo "All fields are required.";
        exit();
    }

    try {
        $sql = "INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $task_title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $task_description, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->execute();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch tasks from the database
$tasks = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY due_date ASC");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching tasks: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo "Welcome to  " .  $_SESSION["username"] . " todo list"; ?></h1>
        <a href="logout.php" class="logout-button">Logout</a>
        

        <!-- Add Task Form -->
        <form class="add-task-form" method="POST" action="index.php">
            <input type="text" name="task_title" placeholder="Task Title" required>
            <input type="text" name="task_description" placeholder="Task Description" required>
            <input type="date" name="due_date" required>
            <button type="submit">Add Task</button>
        </form>

        <!-- Task List -->
        <div class="task-list">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamically display tasks from the database -->
                     <?php foreach ($tasks as $i => $task) {?>
                        <tr>
                            <td><?php echo ++$i;?></td>
                            <td><?php echo $task['title'];?></td>
                            <td><?php echo $task['description'];?></td>
                            <td><?php echo $task['due_date'];?></td>
                            <td><?php echo $task['status'] ?></td>
                            <td>
                                <a href="edit_task.php?id=<?php echo $task['id'];?>" class="edit">Edit</a>
                                <a href="delete_task.php?id=<?php echo $task['id'];?>" class="delete">Delete</a>
                                <?php if ($task['status'] === 'pending') {?>
                                    <a href="complete_task.php?id=<?php echo $task['id'];?>" class="complete">Complete</a>
                                <?php }?>
                            </td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
       
    </div>
</body>
</html>
