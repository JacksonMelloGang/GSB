<?php

/**
 * It gets the username of a user from the database.
 * 
 * @param PDO connexion the PDO object
 * @param int userId The user's ID.
 * 
 * @return string The username of the user with the given id.
 */
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


/**
 * It gets the information of a user by his id.
 * 
 * @param PDO connexion the PDO object
 * @param int userId the id of the user
 * 
 * @return array the information of the user with the id .
 */
function getVisiteurInfoById($connexion, $userId){
    $sql = "SELECT visMatricule, visNom, visPrenom, visAdresse, visCp, visVille, visDateembauche, secCode, labNom FROM visiteur, labo WHERE visMatricule = ? AND labo.labCode = visiteur.labCode";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(array($userId));

    $ligne = $stmt->fetch();

    return $ligne;
}

/**
 * It returns an array of all the rows in the table "visiteur".
 * 
 * @param PDO connexion The connection to the database.
 * 
 * @return string the result of the query.
 */
function getVisiteursInfos($connexion){
    $sql = "SELECT * FROM visiteur";
    $result = $connexion->query($sql);
    $ligne = $result->fetchAll();

    return $ligne;
}
