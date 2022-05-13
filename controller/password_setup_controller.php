<?php

require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

$code = htmlspecialchars($_POST["code"]);
$password = htmlspecialchars($_POST["password"]);

$connexion->beginTransaction();

$sqlcheckcode = "SELECT nom FROM utilisateur WHERE temp_setupcode=?";
$stmt = $connexion->prepare($sqlcheckcode);
$stmt->execute([$code]);

$result = $stmt->fetch();

if($result === false){
    die("Code Invalide.");
}

// Hash Password
$hashedpassword = password_hash($password, PASSWORD_DEFAULT);

// Allow to proceed to the next step 
$allownexstep = true;

$sqlsetpassword = "UPDATE utilisateur SET motdepasse = '{$hashedpassword}' WHERE nom = '{$result["nom"]}'";
$stmtupdatepassword = $connexion->exec($sqlsetpassword);

if($stmtupdatepassword === false | $stmtupdatepassword == 0){
    ob_start();
    echo("ERREUR: Couldn't update password.");
    echo($connexion->errorInfo());
    $content = ob_get_clean();
    $allownexstep = false;
}

if($allownexstep == true){
    $deletesetupcodesql = "UPDATE utilisateur SET temp_setupcode= NULL WHERE nom='{$result["nom"]}'";
    $stmtdeletepassword = $connexion->exec($deletesetupcodesql);

    if($stmtdeletepassword === false | $stmtdeletepassword == 0){
        ob_start();
        echo("Couldn't Delete tempcode.");
        $content = ob_get_clean();
    } else {
        $content = "Success !";
    }    

}

$connexion->commit();
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
    <div class="page-content">
        <nav class="topbar">
            <h1 style="color: red;">GSB</h1>
        </nav>
        
        <div class="content">
            <div style="height:100%"><?= $content ?></div>
        </div>
    </div>
</body>
</html>