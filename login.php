<?php
    require($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    $info = "";
    session_start();

    // When atempting to login
    if(isset($_POST['login'])){
        $result = check($_POST['username'], $_POST['password'], $connexion); // supposed to return array(true|false, message)
        
        // Check if he is allowed to access if true, set auth & userid else, display invalid user/password 
        if($result[0] == true){
            $info = $result[1]; // success
            $_SESSION["authorization"] = "USER"; // set user level

            //set user id if we later, want to get information from the user like it's name or whatever
            setUserId($_POST['username'], $_POST['password'], $connexion);

            if(isset($_GET["page"])){
                header("Location: {$_GET["page"]}");
            } else {
                header("Location: index.php", true, 0);
            }
            exit();
        } else {
            $info = $result[1];
        }
    }

    // close sql connexion
    $connexion = null;
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
            <form id="loginform" action="" method="post">
                <h1>Authentication</h1>
                <input type="text" placeholder="Username" name="username">
                <br>
                <input type="password" placeholder="Password" name="password">
                <hr>
                <input type="hidden" value="login" name="login">
                <input type=submit value="Se connecter">

                <?php echo("<br><span style='color: red; margin-top: 10px'>$info</span>") ?>
            </form>
    </div>
</body>
</html>

