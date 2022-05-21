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
    $sql = "SELECT visMatricule, visNom, visPrenom, visAdresse, visCp, visVille, visDateembauche, secCode, labNom FROM visiteur, labo WHERE visMatricule = ? AND labo.labCode = visiteur.labCode";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(array($userId));

    $ligne = $stmt->fetch();

    return $ligne;
}

function getVisiteursInfos($connexion){
    $sql = "SELECT * FROM visiteur";
    $result = $connexion->query($sql);
    $ligne = $result->fetchAll();

    return $ligne;
}
