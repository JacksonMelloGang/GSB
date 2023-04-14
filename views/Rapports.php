<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // import functions to separate sql request from the view 
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/rapports_model.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/medicaments_model.php");

    /**
     * It displays a table with all the reports written by the user and a table with all the reports
     * written by other users
     * 
     * @param PDO connexion The connection to the database.
     * 
     * @return string every written reports grouped by user
     */
    function showrapports($connexion){
        // 2 Query to get rapport written by the user and rapports written by others people
        $personnalrapport = getPersonnalRapports($connexion, $_SESSION["userId"]);
        $otherrapport = getOthersRapport($connexion, $_SESSION["userId"]);

        // Get User Rapport's from database & display in table
        ob_start();
                echo("<h1>Vos Rapports</h1>");
                echo("<div><label>Activer le mode suppression: </label><input type='checkbox' id='toggledeleterap'></div><br>");
                
                echo("<div class='table-center'>");
                    echo("<table>");
                            // if we have some rapports written by the user
                            if($personnalrapport != false){
                                echo("<th>Numéro Rapport</th><th>Date de visite</th><th>Bilan</th><th>Motif</th>");
                                // display each rapports
                                foreach($personnalrapport as $row){
                                    echo("<tr>");

                                        // we check if the report is editable, if that's the case it automatically add a button 'Editer'
                                        $editable = "<td></td>";
                                        if($row["saisiedef"] == 0){
                                            $editable = "<td><a href='/views/EditRapport.php?&edit=1&rapid={$row['id']}'>Editer</a></td>";
                                        }

                                        // we create a line in the table
                                        echo("<td>{$row['rapNum']}</td><td>{$row['rapDate']}</td><td><a href='/views/EditRapport.php?rapid={$row['id']}'>{$row['rapBilan']}</a></td><td>{$row['rapMotif']}</td>$editable <td><a class='deleterap' href='/views/Rapports.php?action=delete&rapid={$row['id']}'>Supprimer</a></td>");
                                    echo("</tr>");                                    
                                }
                            } else {
                                echo("Aucun rapport saisi par l'utilisateur n'a été trouvé.");
                            }
                    echo("</table>");
                echo("</div>");

                ///////////////////////////////////////////////////////////////////////
                echo("<hr style='width: 100%;margin-top: 10px; margin-bottom:10px;'>");
                ///////////////////////////////////////////////////////////////////////

                // get Other user rapport's
                echo("<h2>Autres Rapports</h2>");
                echo("<div class='table-center'>");
                        echo("<table>");
                            if($otherrapport != false){
                                echo("<th>Numéro Rapport</th><th>Date de visite</th><th>Nom</th><th>Prénom</th><th>Bilan</th><th>Motif</th>");
                                foreach($otherrapport as $rowother){
                                    echo("<tr>");
                                        echo("<td>{$rowother['rapNum']}</td><td>{$rowother['rapDate']}</td><td>{$rowother['visNom']}</td><td>{$rowother['visPrenom']}</td><td><a href='/views/EditRapport.php?rapid={$rowother["id"]}'>{$rowother['rapBilan']}</a></td><td>{$rowother['rapMotif']}</td>");
                                    echo("</tr>");
                                }
                            } else {
                                echo("Aucun rapport écrit par d'autre utilisateur n'a été trouvé.");
                            }
                        
                    echo("</table>");
                echo("</div>");
        return ob_get_clean();
    }


    // PARTIE: ACTION (AFFICHER / MODIFIER RAPPORTS)
    $action = "";
    if(isset($_GET["action"])){
        $action = $_GET["action"];
    }

    switch($action){

        // http://gsb.test:8080/views/Rapports.php?&action=delete&rapid=<RAPID>
        case "delete":
            if(isset($_GET["rapid"])){
                $rapid = htmlspecialchars($_GET["rapid"]);

                $title = "GSB - Rapports";
                $content = deleteRapportByID($connexion, $rapid);
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            } else {
                $title = "GSB - Rapports";
                $content = showrapports($connexion);
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
            break;

        /* It's the default case, if no action is specified, it will show the default page. */
        // http://gsb.test:8080/views/Rapports.php?&action=consult
        default:
            $title = "GSB - Rapports";
            $content = showrapports($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }



