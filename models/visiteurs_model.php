<?php

function getUsernameByVisiteurId($connexion, $userId){

    $sql = "SELECT visNom, visPrenom FROM visiteur WHERE visMatricule = '$userId'";
    $result = $connexion->query($sql);
    $ligne = $result->fetch();
    if($ligne === false){
        $user = "ERROR";
        echo("<script>console.error(\"Couldn't get username from database.\")</script>");
    } else {
        $user = "{$ligne['visNom']} {$ligne['visPrenom']}";
    }

    return $user;
}


function getVisiteurInfoById($connexion, $userId){
    $sql = "SELECT * FROM visiteur WHERE visMatricule = '$userId'";
    $result = $connexion->query($sql);
    $ligne = $result->fetchAll();

    return $ligne;
}

function getVisiteursInfos($connexion){
    $sql = "SELECT * FROM visiteur";
    $result = $connexion->query($sql);
    $ligne = $result->fetchAll();

    return $ligne;

}