<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="container">
        <form class="login-form" method="POST" action="actions/login_action.php">
            <h1>Login</h1>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
            <p class="redirect">Don't have an account? <a href="register.php">Register here</a></p>
        </form>
        <?php
            if(isset($_GET["err"]) && $_GET["err"] == 1){
                echo "<p style='color:red; text-align:center; margin-top:10px;'>All fields are required</p>";
            }
        ?>
    </div>
</body>
</html>
