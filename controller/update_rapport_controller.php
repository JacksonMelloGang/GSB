<?php

header("Access-Control-Allow-Origin: *");

// Restricted Access
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/auth_middleware.php");
check_if_allowed('USER'); // Rank Needed

// require sql connection
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/DbConnexion.php");

$rapId = "";
$bilan = "";
$produit1 = "";
$produit2 = "";
$saisiedef = 0;


if(!isset($_POST['rapid'])){
    die("Numéro de Rapport non-fourni.");
}

if(filter_var($_POST['rapid'], FILTER_VALIDATE_INT) == true){
    $rapId = filter_var($_POST['rapid'], FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Numéro de rapport Non Valide");
}

if(!isset($_POST["bilan"])){
    die("Pas de bilan");
} 

if(!isset($_POST["produit1"])){
    die("Pas Produit N°1");
}

if(!isset($_POST["produit2"])){
    die("Pas Produit N°2");
}

if(!isset($_POST['saisiedef'])){
    die("Saisie Définitive Non définie.");
}


////////////////////////////////////////////////
$bilan = htmlspecialchars($_POST["bilan"]);
$produit1 = htmlspecialchars($_POST["produit1"]);
$produit2 = htmlspecialchars($_POST["produit2"]);
$saisiedef = $_POST['saisiedef'];

if($saisiedef == 0){
    $saisiedef = 0;
} else {
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

$sqlupdate = "UPDATE rapportvisite SET rapBilan = ?, prod1 = ?, prod2 = ?  WHERE id = ?";
$stmtupdate= $connexion->prepare($sqlupdate);
$stmtupdate->execute([$bilan, $produit1, $produit2, $rapId]);

$sqllockrap = "UPDATE rapportvisite SET saisiedef = ? WHERE id = ?";
$stmtlockrap = $connexion->prepare($sqllockrap);
$stmtlockrap->execute([$saisiedef, $rapId]);

if($stmtupdate === false || $stmtlockrap === false){
    die("Couldn't update rapport");
} else {
    echo("Success");
}
