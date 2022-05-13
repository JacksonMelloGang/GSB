<?php
    require($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    require($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    $info = "";
    session_start();


    if(isset($_SESSION["authorization"])){
        header("Location: /dashboard.php");
        return;
    }

    // When atempting to login
    /* It's checking if the user is trying to login, if so, it will check if the user is allowed to
    login, if so, it will set the user level to USER and set the userid, if not, it will display an
    error message. */
    if(isset($_POST['login'])){
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $result = check($username, $password, $connexion); // supposed to return array(true|false, message)
        
        // Check if he is allowed to access if true, set auth & userid else, display invalid user/password 
        if($result[0] == true){
            $info = $result[1]; // = "success"
            $_SESSION["authorization"] = "USER"; // set user level

            //set userid if we later, want to get information from the user like it's name from the database
            setUserId($username, $connexion);

            if(isset($_GET["page"])){
                header("Location: {$_GET["page"]}");
            } else {
                header("Location: dashboard.php", true, 0);
            }
            exit();
        } else {
            $info = $result[1];
        }
        
        // close sql connection
        $connexion = null;
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
    <style>
        #submit:hover {
            cursor:pointer;
        }
    </style>
</head>
<body>
    <div id='background'></div>
    <div id="back-login" style="color: white;">
            <!-- It's the form that the user will fill to login. -->
            <form id="loginform" action="" method="post">
                <h1>Authentication</h1>
                <input type="text" placeholder="Nom d'utilisateur" name="username">
                <br>
                <input type="password" placeholder="Mot de passe" name="password">
                <hr>
                <input type="hidden" value="login" name="login">
                <input id='submit' type=submit value="Se connecter">

                <?php 
                echo("<br><span style='color: red; margin-top: 10px'>$info</span>");
                
                if(isset($_GET["error"])){
                    echo("<span>Une erreur est survenue: <b>ERR-". strtoupper(htmlspecialchars($_GET["error"])) ."</b></span>");
                }
                ?>
                
                <a style="color: white;" href="password_setup.php">Nouveau ?</a>
            </form>
    </div>
</body>
</html>

