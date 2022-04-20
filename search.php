<?php

require_once("./db/DbConnexion.php");
$searchfilter = isset($_GET['search']) ? $_GET['search'] : "";

$medicquery = "SELECT medDepotlegal, medNomcommercial FROM medicament WHERE medDepotlegal LIKE :userinput OR medNomcommercial LIKE :userinput";
$practicienquery = "SELECT praNum, praNom, praPrenom FROM praticien WHERE praNom LIKE :userinput OR praPrenom LIKE :userinput";
$rapportquery = "SELECT rapNum FROM rapportvisite WHERE rapNum LIKE :userinput";
$visiteurquery = "SELECT visMatricule, visNom, visPrenom FROM visiteur WHERE visMatricule LIKE :userinput OR visNom LIKE :userinput OR visPrenom LIKE :userinput";

// we prepare each request as we get data from user input
$resultmedicquery = $connexion->prepare($medicquery);
$resultmedicquery->execute(array(':userinput' => "%$searchfilter%"));

$resultpraticienquery = $connexion->prepare($practicienquery);
$resultpraticienquery->execute(array(':userinput' => "%$searchfilter%"));

$resultrapportquery = $connexion->prepare($rapportquery);
$resultrapportquery->execute(array(':userinput' => "%$searchfilter%"));

$resultvisiteurquery = $connexion->prepare($visiteurquery);
$resultvisiteurquery->execute(array(':userinput' => "%$searchfilter%"));

// Then we fetch all
try {
    $resultmedicquery = $resultmedicquery->fetchAll();
    $resultpraticienquery = $resultpraticienquery->fetchAll();
    $resultrapportquery = $resultrapportquery->fetchAll();
    $resultvisiteurquery = $resultvisiteurquery->fetchAll();
} catch (Exception $e) {
    echo ("Couldn't get data: <br>" . $e);
}


// type -> row n°x of result -> column
$resultarray = array(0 => $resultmedicquery, 1 => $resultpraticienquery, 2 => $resultrapportquery, 3 => $resultvisiteurquery);

 
ob_start();

$arrayisempty = 0; 
// Check first if result of queries are empty
for ($i = 0; $i < sizeof($resultarray); $i++) { // type (medic, rapport, praticien, visiteur)
    if($resultarray === false || sizeof($resultarray[$i])  == 0){
        $arrayisempty += 1;
    }
}

if($arrayisempty == sizeof($resultarray)){
    echo("Pas de résultat trouvé !");
    return;
}


// Generic Items
echo ("<a href='./Rapports.php?fromsearch=true'>Rapports</a><br>");
echo ("<a href='./Medicaments.php?fromsearch=true'>Medicaments</a><br>");
echo ("<a href='./Praticien.php?fromsearch=true'>Praticien</a><br>");

for ($i = 0; $i < sizeof($resultarray); $i++) { // type (medic, rapport, praticien, visiteur)
    echo("<br>");
    for ($j = 0; $j < sizeof($resultarray[$i]); $j++) { // row
            switch ($i) {
                case 0:
                    echo ("<a href='Medicaments.php?&action=showmedic&medic={$resultarray[$i][$j][0]}' class='search-link'>{$resultarray[$i][$j][1]}</a><br>");
                    break;
                case 1:
                    echo ("<a href='Praticien.php?pratid={$resultarray[$i][$j][0]}' class='search-link'>Praticien {$resultarray[$i][$j][1]} {$resultarray[$i][$j][2]}</a><br>");
                    break;
                case 2:
                    echo ("<a href='Rapports.php?rapportid={$resultarray[$i][$j][0]}' class='search-link'>Rapport N°{$resultarray[$i][$j][0]}</a><br>");
                    break;
                case 3:
                    echo ("<a href='Visiteurs.php?visiteurid={$resultarray[$i][$j][0]}' class='search-link'>Visiteur {$resultarray[$i][$j][1]} {$resultarray[$i][$j][2]}</a><br>");
                    break;
                default:
                    echo ("<a href='index.php'>Element Inconnu</a><br>");
                break;
        }
    }
}

echo("<br><a class='last-search-item' href='search-help.php'>Help !</a>");

echo(ob_get_clean());
