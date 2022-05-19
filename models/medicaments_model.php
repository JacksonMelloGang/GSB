<?php

    function getMedicamentsOptions($connexion){
        ob_start();
        $query = $connexion->query("SELECT medNomCommercial, medDepotLegal FROM medicament");
        $result = $query->fetch();

        while($result != false){
            echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
            $result = $query->fetch();
        }

        return ob_get_clean();
    }

    function getSelectedMedicamentsAsOption($connexion, $medicname){
        ob_start();
        $query = $connexion->query("SELECT medNomCommercial, medDepotLegal FROM medicament");
        $result = $query->fetch();

        while($result != false){
            if($result['medNomCommercial'] == $medicname){
                echo("<option value='{$result['medDepotLegal']}' selected>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
            } else {
                echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
            }

            $result = $query->fetch();
        }

        return ob_get_clean();
    }



    function getMostProposedSamples($connexion){

        $sql = "SELECT SUM(offQte), medicament.medNomcommercial FROM offrir, medicament WHERE offrir.medDepotlegal = medicament.medDepotlegal GROUP BY offrir.medDepotlegal ORDER BY SUM(offQte) DESC LIMIT 4";
        $stmt = $connexion->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }

