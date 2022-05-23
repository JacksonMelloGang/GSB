<?php

    /**
     * It checks if the user is allowed to edit a report
     * 
     * @param PDO connexion the PDO connection object
     * @param int rapportId the id of the report to be edited
     * @param int userId the id of the user who is trying to edit the report
     * 
     * @return boolean a boolean value.
     */
    function isAllowedtoEdit($connexion, $rapportId, $userId){

        $allowed = true;

        $sqlcheckauthor = "SELECT visMatricule FROM rapportvisite WHERE id = $rapportId";
        $stmtauthor = $connexion->query($sqlcheckauthor);
        $resultauthor = $stmtauthor->fetch();

        if($resultauthor === false){
            $allowed = false;
        } else {
            if($resultauthor['visMatricule'] != $userId){
                $allowed = false;
            }
        }

        return $allowed;
    }

    /**
     * It checks if a report is editable or not.
     * 
     * @param PDO connexion the connection to the database
     * @param int rapportId the id of the report you want to check
     * 
     * @return boolean The function isEditable is returning a boolean value.
     */
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


    /**
     * It returns the author of a report.
     * 
     * @param PDO connexion the PDO connection object
     * @param int rapportId the id of the report
     * 
     * @return string|boolean The author of the report or false if not found.
     */
    function getAuthor($connexion, $rapportId){
        $sqlcheckauthor = "SELECT visMatricule FROM rapportvisite WHERE id = $rapportId";
        $stmtauthor = $connexion->query($sqlcheckauthor);
        $resultauthor = $stmtauthor->fetch();

        if($resultauthor === false){
            return false;
        }

        return $resultauthor['visMatricule'];
    }

    /**
     * It deletes a report from the database
     * 
     * @param PDO connexion the connection to the database
     * @param int rapportId the id of the report to delete
     * 
     * @return string a string success or failed.
     */
    function deleteRapportByID($connexion, $rapportId){

        // get author from rapport Id
        $bddAuthor = getAuthor($connexion, $rapportId);

        if($bddAuthor == false){
            return "Le rapport n'existe pas!";
        }

        // if author from database is not the same from user, cancel task
        if($bddAuthor != $_SESSION["userId"]){
            return "Vous n'êtes pas l'auteur du rapport !";
        }

        // delete rapport
        $sql = "DELETE FROM `rapportvisite` WHERE `id` = ?";
        $stmt = $connexion->prepare($sql);
        $result = $stmt->execute(array($rapportId));

        if($result === false){
            return "Impossible de supprimer le rapport.";
        } else {
            return "Votre rapport a bien été supprimé.";
        }
    }

    /**
     * It returns the information of a report by its id
     * 
     * @param PDO connexion the PDO object
     * @param string rapportId the id of the report
     * 
     * @return array|boolean the information of the report with the id given in parameter or false if failed.
     */
    function getRapportInformationsById($connexion, $rapportId){
        $sql = "SELECT rapportvisite.id, rapNum, rapDate, rapBilan, rapportvisite.praNum, rapMotif, rapDateSaisie, saisiedef, docfourni, prod1, prod2, praPrenom, praNom, praAdresse, praCp, praVille, praCoefnotoriete FROM rapportvisite, praticien WHERE rapportvisite.praNum = praticien.praNum AND rapportvisite.id = ?;";
        $stmt = $connexion->prepare($sql);
        $resultrapport = $stmt->execute(array($rapportId));

        if($resultrapport === false){
            $result = false;
        } else {
            $result = $stmt->fetch();
        }

        return $result; 
    }

    /**
     * It gets the personal reports of the user.
     * 
     * @param PDO connexion the connection to the database
     * @param string userId the id of the user who is logged in
     * 
     * @return array|boolean the personal reports of the user .
     */
    function getPersonnalRapports($connexion, $userId){
        $personnalrapport_sql = "SELECT id, rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif, saisiedef FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule AND visiteur.visMatricule = ? ";
        $stmt = $connexion->prepare($personnalrapport_sql);
        $resultrapport = $stmt->execute(array($userId));
        
        if($resultrapport === false){
            $result = false;
        } else {
            $result = $stmt->fetchAll();
        }

        return $result;
    }

    /**
     * It returns an array of all the rows in the rapportvisite table where the visMatricule column is
     * not equal to the userId.
     * 
     * @param PDO connexion the connection to the database
     * @param string userId the id of the user who is logged in
     * 
     * @return array|boolean the result of the query.
     */
    function getOthersRapport($connexion, $userId){
        $other_rapport_sql = "SELECT id, rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule AND NOT visiteur.visMatricule = ? ";
        $stmt = $connexion->prepare($other_rapport_sql);
        $resultrapport = $stmt->execute(array($userId));

        // if error, return false else return result from query
        if($resultrapport === false){
            $result = false;
        } else {
            $result = $stmt->fetchAll();
        }

        return $result;
    }

    /**
     * It returns an array of all the medicines that were offered to a patient during a visit.
     * 
     * @param PDO connexion the connection to the database
     * @param int rapId the id of the report
     * 
     * @return array|false the result of the query.
     */
    function getEchantillonsByRapport($connexion, $rapId){
        $sql = "SELECT offrir.medDepotlegal, medicament.medNomCommercial, COUNT(offrir.medDepotlegal) AS counted FROM offrir, medicament WHERE rapNum = ? AND offrir.medDepotlegal = medicament.medDepotlegal";
        $stmt = $connexion->prepare($sql);
        $resultmedic = $stmt->execute(array($rapId));

        
        if($resultmedic === false){
            $result = false;
        } else {
                $result = $stmt->fetchAll();
                if($result[0]["counted"] == 0){
                    $result = false;
                }
        }
        return $result;
    }