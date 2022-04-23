<?php
    function showrapports($connexion){
        // Get Rapports from database & display in table
        $result = $connexion->query("SELECT rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule");
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

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed


    require_once($_SERVER["DOCUMENT_ROOT"]. "/db/DbConnexion.php");



    if(isset($_GET["action"])){
        $action = $_GET["action"];
        switch($action){
            case "new":
                    $title = "GSB - Rapports";
                    $content = "aa";
                    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
                    return;
                break;
            default:
                
        }
    }


    $title = "GSB - Rapports";
    $content = showrapports($connexion);
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");