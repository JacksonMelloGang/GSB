<?php

    header("Access-Control-Allow-Origin: *");

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/DbConnexion.php");

    $bilan = "";
    $rapId = "";
    $saisiedef = 0;

    if(isset($_POST["bilan"])){
        $bilan = htmlspecialchars($_POST["bilan"]);
    } else {
        $bilan = "Pas d'update.";
    }
    
    if(!isset($_POST['rapid'])){
        die("Rapport ID Not Valid.");
    }

    if(filter_var($_POST['rapid'], FILTER_VALIDATE_INT) == true){
        $rapId = filter_var($_POST['rapid'], FILTER_SANITIZE_NUMBER_INT);
    } else {
        die("Numéro de rapport Non Valide");
    }

    if(isset($_POST['saisiedef'])){
        $saisiedef = 1;
    }

    $sqlcheckauthor = "SELECT visMatricule FROM rapportvisite WHERE id = ?";
    $stmtauthor = $connexion->prepare($sqlcheckauthor);
    $stmtauthor->execute([$rapId]);
    $resultauthor = $stmtauthor->fetch();

    if($resultauthor === false){
        die("Le Rapport n'existe pas !");
    } 

    if($resultauthor['visMatricule'] != $_SESSION['userId']){
        die("Vous n'êtes pas l'auteur !");
    }


    $sqlupdate = "UPDATE rapportvisite SET rapBilan = ? WHERE id = ?";
    $stmtupdate= $connexion->prepare($sqlupdate);
    $stmtupdate->execute([$bilan, $rapId]);

    $sqllockrap = "UPDATE rapportvisite SET saisiedef = ? WHERE id = ?";
    $stmtlockrap = $connexion->prepare($sqllockrap);
    $stmtlockrap->execute([$saisiedef, $rapId]);

    if($stmtupdate === false){
        die("Couldn't update rapport");
    } else {
        echo("Success");
    }

    

    