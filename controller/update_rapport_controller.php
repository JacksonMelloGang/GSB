<?php

header("Access-Control-Allow-Origin: *");

// Restricted Access
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/auth_middleware.php");
check_if_allowed('USER'); // Rank Needed

// require sql connection
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/DbConnexion.php");

// require rapport model
require_once($_SERVER["DOCUMENT_ROOT"] . "/models/rapports_model.php");

$rapId = "";
$bilan = "";
$produit1 = "";
$produit2 = "";
$saisiedef = 0;
$nbechantillon = 1;

// check if value are good 
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

/*if(filter_var($_POST['nbechantillon'], FILTER_VALIDATE_INT) == true){
    $nbechantillon = filter_var($_POST['nbechantillon'], FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Nombre d'échantillon Non Valide");
}*/


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

// check if rapport exist and if the user who want to update is the right



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

//////////////////////////////////////////////////////////////////////////
// update rapport in sql

$sqlupdate = "UPDATE rapportvisite SET rapBilan = ?, prod1 = ?, prod2 = ?, saisiedef = ?  WHERE id = ?";
$stmtupdate= $connexion->prepare($sqlupdate);
$stmtupdate->execute([$bilan, $produit1, $produit2, $saisiedef, $rapId]);


/* It's inserting the samples into the database */
$prodsql = "INSERT INTO offrir(visMatricule, rapNum, medDepotlegal, offQte) VALUES(?, ?, ?, ?)";
for($i = 1; $i <= $nbechantillon; $i++){
    $stmt = $connexion->prepare($prodsql);
    $echantillon = $_POST["PRA_ECH{$i}"];
    $qteechantillon = $_POST["PRA_QTE{$i}"];

    if(empty($echantillon) || empty($qteechantillon)){
        return;
    }

    $stmt->execute(array($_SESSION["userId"], $rapId, $echantillon, $qteechantillon));
}

echo("Success");