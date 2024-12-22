<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body>
    <?php
         if(isset($_GET["err"])){
            if($_GET["err"]==6){
                echo "<p style='color:red; margin-top:10px;'>Task not found or you do not have permission to edit it</p>";
                echo "<a href='index.php'>Go to Tasks Display</a>";
            }
    }
    ?>
</body>
</html>