<?php

    if(isset($_POST['login'])){
        echo("aas");
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
                <input type="text" placeholder="Username" id="username">
                <br>
                <input type="password" placeholder="Password" id="password">
                <hr>
                <input type="hidden" value="login" name="login">
                <input type=submit value="Se connecter">
                <?php echo($info) ?>
            </form>
    </div>
</body>
</html>

