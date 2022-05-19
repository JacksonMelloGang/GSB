<?php

    function getPraticiensOptions($connexion){

        ob_start();

        $query = $connexion->query("SELECT praNum, praNom, praPrenom FROM praticien");
        $result = $query->fetch();

        if($result === false){
            echo("Aucun Praticien n'a pu être trouvé.");
        }

        while($result != false){
            echo("<option value='{$result['praNum']}'>{$result['praNom']} {$result['praPrenom']}</option>");
            $result = $query->fetch();
        } 

        $content = ob_get_clean();
        return $content;
    }


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