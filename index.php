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
        header("Location: ./index.php?err=5");
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
// Fetch tasks based on the selected status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$tasks = [];
try {
    if ($status_filter === 'all') {
        $sql = "SELECT * FROM tasks WHERE user_id = :user_id ORDER BY due_date ASC";
    } else {
        $sql = "SELECT * FROM tasks WHERE user_id = :user_id AND status = :status ORDER BY due_date ASC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    if ($status_filter !== 'all') {
        $stmt->bindParam(':status', $status_filter, PDO::PARAM_STR);
    }
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

        <?php
            if(isset($_GET["err"])){
                if($_GET["err"]==5){
                    echo "<p style='color:red; text-align:center; margin-top:10px;'>Please enter the task title and description</p>";
                }
        }
        ?>

        <!-- Task Filter -->
        <form method="GET" action="index.php" class="filter-form">
            <label for="status">Filter by status:</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
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
                            <td>
                                
                                <a href="delete_task.php?id=<?php echo $task['id'];?>" class="delete">❌</a>
                                <?php if ($task['status'] === 'pending') {?>
                                    <a href="edit_task.php?id=<?php echo $task['id'];?>" class="edit">✏️</a>
                                    <a href="complete_task.php?id=<?php echo $task['id'];?>" class="complete">✅</a>
                                <?php }?>
                            </td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div class="changeContainer">
            <a href="changeInformation.php">Change personal information</a>
        </div>
        
    </div>
</body>
</html>
