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

// Fetch user details including profile picture
$sql = "SELECT username, profile_picture FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
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

        <!-- Navbar -->
        <nav style=" display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 10px;
                        margin-bottom:20px;
                        background-color: #4CAF50;
                        color: white;">
    <div>
        <h1 style="font-size: 24px; color: white; margin: 0;">My Todo List</h1>
    </div>
    <div style="display: flex; align-items: center;">
        <div style="display: flex; align-items: center; margin-right: 20px;">
            <?php if ($user['profile_picture']): ?>
                <img src="uploads/<?php echo $user['profile_picture']; ?>" alt="Profile Picture" style="width: 70px; height: 70px; border-radius: 50%; margin-right: 10px; object-fit: cover; border: 2px solid #ddd; transition: transform 0.3s ease-in-out;">
            <?php else: ?>
                <img src="uploads/default-profile.png" alt="Default Profile Picture" style="width: 70px; height: 70px; border-radius: 50%; margin-right: 10px; object-fit: cover; border: 2px solid #ddd; transition: transform 0.3s ease-in-out;">
            <?php endif; ?>
            <span style="font-weight: bold; font-size: 16px;"><?php echo $_SESSION["username"]; ?></span>
        </div>
        <div style="display: flex; flex-direction: row; align-items:center; justify-content:center; gap:5px;">
            <a href="changeInformation.php" style="margin-top: 10px; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;">Change personal information</a>
            <a href="logout.php" style="margin-top: 10px; padding: 10px 15px; background-color: #ff4d4d; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;">Logout</a>
        </div>
    </div>
</nav>
        

        <!-- Add Task Form -->
        <form class="add-task-form" method="POST" action="index.php">
            <input type="text" name="task_title" placeholder="Task Title" required>
            <input type="text" name="task_description" placeholder="Task Description" required>
            <input type="date" name="due_date" required>
            <button type="submit">Add Task</button>
        </form>

        <?php
            if (isset($_GET["err"])) {
                if ($_GET["err"] == 5) {
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
                    <?php foreach ($tasks as $i => $task) { ?>
                        <tr>
                            <td><?php echo ++$i; ?></td>
                            <td><?php echo $task['title']; ?></td>
                            <td><?php echo $task['description']; ?></td>
                            <td><?php echo $task['due_date']; ?></td>
                            <td>
                                <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="delete">❌</a>
                                <?php if ($task['status'] === 'pending') { ?>
                                    <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="edit">✏️</a>
                                    <a href="complete_task.php?id=<?php echo $task['id']; ?>" class="complete">✅</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>


    </div>
</body>
</html>
