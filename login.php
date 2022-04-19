<?php
    $info = "";

    // only start session if user doesn't have any session already open
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    
    // When atempting to login
    if(isset($_POST['login'])){
        require_once("./controller/middleware/auth_middleware.php");
        $result = check($_POST['username'], $_POST['password']);
                
        // Check if he is allowed to access otherwise 
        if($result[0] == true){
            $info = $result[1];
            $_SESSION["authorization"] = "USER";

            header("Location: index.php");
        } else {
            $info = $result[1];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="public/css/login.css" type="text/css" rel="stylesheet">
    <title>Se Connecter</title>
</head>
<body>
    <div id='background'></div>
    <div id="back-login">
            <form id="loginform" action="#" method="post">
                <h1>Authentication</h1>
                <input type="text" placeholder="Username" name="username">
                <br>
                <input type="password" placeholder="Password" name="password">
                <hr>
                <input type="hidden" value="login" name="login">
                <input type=submit value="Se connecter">

                <?php echo("<br><span style='color: red;'>$info</span>") ?>
            </form>
    </div>
</body>
</html>

