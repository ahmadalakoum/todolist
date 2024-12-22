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

    try {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $task_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            header("Location: error.php?err=6");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error fetching task: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_title = trim($_POST["task_title"]);
    $task_description = trim($_POST["task_description"]);
    $due_date = $_POST["due_date"];

    if (empty($task_title) || empty($task_description)) {
        header("Location: edit_task.php?err=5");
        exit();
    }

    try {
        $sql = "UPDATE tasks SET title = :title, description = :description, due_date = :due_date WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $task_title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $task_description, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $task_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error updating task: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="styles/edit.css">
</head>
<body>
    <div class="container">
        <h1>Edit Task</h1>
        <form method="POST" action="edit_task.php?id=<?php echo $task['id']; ?>">
            <input type="text" name="task_title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            <input type="text" name="task_description" value="<?php echo htmlspecialchars($task['description']); ?>" required>
            <input type="date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
            <button type="submit">Update Task</button>
        </form>
        
        <?php
            if(isset($_GET["err"])){
                if($_GET["err"]==5){
                    echo "<p style='color:red; text-align:center; margin-top:10px;'>Please enter the task title and description</p>";
                }
        }
        ?>
        <a href="index.php">Cancel</a>
    </div>
</body>
</html>
