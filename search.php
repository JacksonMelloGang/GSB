<?php

require_once("./db/DbConnexion.php");

$medicquery = "SELECT * FROM medicament";
$practicienquery = "SELECT * FROM praticien";
$rapportquery = "SELECT rapNum, praNum, rapDate FROM rapportvisite";
$visiteurquery = "SELECT visMatricule, visNom, visPrenom, visCp, visVille FROM visiteur";

$resultmedicquery = $connexion->query($medicquery);
$resultpracticienquery = $connexion->query($practicienquery);
$resultrapportquery = $connexion->query($rapportquery);
$resultvisiteurquery = $connexion->query($visiteurquery);

try {
    $resultmedicquery = $resultmedicquery->fetchAll();
    $resultpracticienquery = $resultpracticienquery->fetchAll();
    $resultrapportquery = $resultrapportquery->fetchAll();
    $resultvisiteurquery = $resultvisiteurquery->fetchAll();
} catch (Exception $e) {
    echo ($e);
}


// type -> row n°x of result -> column
$resultarray = array(0 => $resultmedicquery, 1 => $resultpracticienquery, 2 => $resultrapportquery, 3 => $resultvisiteurquery);


ob_start();

// Generic Items
echo ("<a href='./Rapports.php?fromsearch=true'>Rapports</a><br>");
echo ("<a href='./Medicaments.php?fromsearch=true'>Medicaments</a><br>");
echo ("<a href='./Practicien.php?fromsearch=true'>Praticien</a><br>");
echo("<br>");

for ($i = 0; $i < sizeof($resultarray); $i++) { // type (medic, rapport, praticien, visiteur)
    echo("<br>");
    for ($j = 0; $j < sizeof($resultarray[$i]); $j++) { // row
            switch ($i) {
                case 0:
                    echo ("<a href='Medicaments.php?medicid={$resultarray[0][$j][0]}'>". $resultarray[0][$j][1] . "</a><br>");
                    break;
                case 1:
                    echo ("<a href='Praticien.php?pratid={$resultarray[1][$j][0]}'>Praticien {$resultarray[1][$j][1]} {$resultarray[1][$j][2]}</a><br>");
                    break;
                case 2:
                    echo ("<a href='Rapports.php?rapportid={$resultarray[2][$j][0]}'>Rapport N°{$resultarray[2][$j][0]}</a><br>");
                    break;
                case 3:
                    echo ("<a href='Visiteurs.php?visiteurid={$resultarray[3][$j][0]}'>{$resultarray[3][$j][1]} {$resultarray[3][$j][2]}</a><br>");
                    break;
                default:
                    echo ("<a href='index.php'>Element Inconnu</a><br>");
                break;
        }
    }
}
echo("<br><a class='last-search-item' href='search-help.php'>Help !</a>");

ob_end_flush();
