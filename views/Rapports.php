<?php
    require("../db/DbConnexion.php");

    function showrapports($connexion){
        // Get Rapports from database & display in table
        $result = $connexion->query("SELECT * FROM rapportvisite");
        ob_start();
            echo("<table>");
                $row = $result->fetch();
                while($row){
                    echo("<tr>");
                    for($i =0; $i < $result->columnCount(); $i++){
                        echo("<td>$row[$i]<br></td>");
                    }
                    echo("</tr>");
                    $row = $result->fetch();
                }
            echo("</table>");
        return ob_get_clean();
    }
    

    $title = "GSB - Rapports";
    $content = showrapports($connexion);
    require("./layout/layout.php");