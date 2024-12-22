<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles/register.css">
</head>
<body>
    <div class="container">
        <form class="registration-form" method="POST" action="actions/register_action.php">
            <h1>Register</h1>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
            </div>
            <button type="submit">Register</button>
            <p class="redirect">Already have an account? <a href="login.php">Login here</a></p>
        </form>
        <?php
            if(isset($_GET["err"])){
                if($_GET["err"]==1){
                    echo "<p style='color:red; text-align:center; margin-top:10px;'>All fields are required</p>";
                }
                if($_GET["err"]==2){
                    echo "<p style='color:red; text-align:center; margin-top:10px;'>Password do not match</p>";
                }
                if($_GET["err"]==3){
                    echo "<p style='color:red; text-align:center; margin-top:10px;'>Email or Username already exists</p>";
                }
            }
            
        ?>
    </div>
</body>
</html>
