<?php

    function isAllowedtoEdit($connexion, $rapportId){

        $allowed = true;

        $sqlcheckauthor = "SELECT visMatricule FROM rapportvisite WHERE id = $rapportId";
        $stmtauthor = $connexion->query($sqlcheckauthor);
        $resultauthor = $stmtauthor->fetch();

        if($resultauthor === false){
            $allowed = false;
        } else {
            if($resultauthor['visMatricule'] != $_SESSION['userId']){
                $allowed = false;
            }
        }

        return $allowed;
    }

    function isEditable($connexion, $rapportId){
        $editable = true;

        $sqlcheckauthor = "SELECT saisiedef FROM rapportvisite WHERE id = $rapportId";
        $stmtauthor = $connexion->query($sqlcheckauthor);
        $resultauthor = $stmtauthor->fetch();

        if($resultauthor === false){
            $editable = false;
        } else {
            if($resultauthor['saisiedef'] != 0){
                $editable = false;
            }
        }

        return $editable;
    }


    function getAuthor($connexion, $rapportId){
        $sqlcheckauthor = "SELECT visMatricule FROM rapportvisite WHERE id = $rapportId";
        $stmtauthor = $connexion->query($sqlcheckauthor);
        $resultauthor = $stmtauthor->fetch();

        return $resultauthor['visMatricule'];
    }

    function deleteRapportByID($connexion, $rapportId){

        // get author from rapport Id
        $bddAuthor = getAuthor($connexion, $rapportId);

        // if author from database is not the same from user, cancel task
        if($bddAuthor != $_SESSION["userId"]){
            die("Vous n'êtes pas l'auteur du rapport !");
        }

        // delete rapport
        $sqldeleterap = "DELETE FROM rapportvisite WHERE id = '$rapportId'";
        $stmt = $connexion->exec($sqldeleterap);
        
        if($stmt === 0){
            return "Couldn't delete rapport.";
        } else {
            return "Votre rapport a bien été supprimé.";
        }
    }