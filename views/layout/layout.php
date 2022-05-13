<?php  

    header("Cache-Control: max-age=1"); 
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");
    $sql = "SELECT visNom, visPrenom FROM visiteur WHERE visMatricule = '{$_SESSION["userId"]}'";
    $result = $connexion->query($sql);
    $ligne = $result->fetch();
    if($result->rowCount() == 0){
        $user = "ERROR";
        echo("<script>console.error(\"Couldn't get username from database.\")</script>");
    } else {
        $user = "{$ligne['visNom']} {$ligne['visPrenom']}";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Style -->
    <link href="/public/css/style.css" rel="stylesheet" type="text/css">
    <!--Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Dependencies -->
    <script src="/public/js/jquery-3.6.0.min.js"></script>
    <script src="/public/js/chart.js"></script>
</head>

<body>
    <nav class="sidebar">
            <img src="../public/img/gsblogo.png">
            <li class="first-sidebar"><a href="/dashboard.php">Acceuil</a></li>
            <hr>
            <div class="dropdown">
                    <span style="color: red"><a href="/views/Rapports.php">Rapports</a></span>
                    <div class="dropdown-content">
                            <a href="/views/Rapports.php?action=new">Nouveau</a>
                            <a href="/views/Rapports.php?action=consult">Consulter</a>
                    </div>
            </div>
            <hr>
            <?php
                if($_SESSION['authorization'] == "RESP"){
                    echo("<li><a href='/views/Visiteurs.php?action=consult'>Visiteurs</a></li>");
                }
            ?>
            <li><a href="/views/Medicaments.php?action=consult">Medicaments</a></li>
            <li><a href="/views/Praticiens.php?action=consult">Praticiens</a></li>

            <a class="logout" onclick="logout()" href="#">Se Déconnecter</a>
    </nav>

    <div class="page-content">
        <nav class="topbar">
            <div id="search">
                <input type="text" class="search-input" placeholder="Search...">
                <div id="search-result" class='loading-effect' style="display:none; position: absolute; color: white; background-color: red;  "></div>
            </div>
            <div id="username">
                <span><?= $user ?></span>
            </div>
        </nav>
        
        <div class="content">
            <?= $content ?>
        </div>
    </div>
    
    <script src="/public/js/search-bar.js"></script>
    <script src="/public/js/app.js"></script>

</body>
</html>