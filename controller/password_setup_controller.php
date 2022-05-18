<?php

require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

$code = htmlspecialchars($_POST["code"]);
$password = htmlspecialchars($_POST["password"]);

$connexion->beginTransaction();
ob_start();

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
$commitornot = true;

/* It's setting password of the user. */
$sqlsetpassword = "UPDATE utilisateur SET motdepasse = '{$hashedpassword}' WHERE nom = '{$result["nom"]}'";
$stmtupdatepassword = $connexion->exec($sqlsetpassword);

if($stmtupdatepassword === false | $stmtupdatepassword == 0){
    echo("ERREUR: Couldn't update password.");
    echo($connexion->errorInfo());
    $content = ob_get_clean();

    $commitornot = false;
    $allownexstep = false;
}

if($allownexstep == true){
    $deletesetupcodesql = "UPDATE utilisateur SET temp_setupcode= NULL WHERE nom='{$result["nom"]}'";
    $stmtdeletepassword = $connexion->exec($deletesetupcodesql);

    if($stmtdeletepassword === false | $stmtdeletepassword == 0){
        echo("Couldn't Delete tempcode.");
        $content = ob_get_clean();
        
        $commitornot = false;
    } else {
        $content = "Success !";
    }    

}

if($commitornot == false){
    $connexion->rollBack();
} else {
    $connexion->commit();
}
?>