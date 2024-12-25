<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

// Database connection
require "connection.php";

// Fetch users
$sql = "SELECT id, username, email, role FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main>
        <section>
            <h2>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!</h2>
            <p>Manage users and perform administrative tasks.</p>
        </section>

        <section>
            <h2>Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user["id"]) ?></td>
                            <td><?= htmlspecialchars($user["username"]) ?></td>
                            <td><?= htmlspecialchars($user["email"]) ?></td>
                            <td><?= htmlspecialchars($user["role"]) ?></td>
                            <td>
                                <?php if ($user["role"] != "admin"): ?>
                                    <form action="promote.php" method="post" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                                        <button type="submit">Promote to Admin</button>
                                    </form>
                                <?php endif; ?>
                                <form action="delete_user.php" method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                                    <button type="submit" onclick="return confirm('Are you sure?')">Delete User</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
