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