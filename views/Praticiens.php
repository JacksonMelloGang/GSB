<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed(array('USER')); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // require praticiens model
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/praticiens_model.php");

    $action = "";
    if(isset($_GET["action"])){
        $action= $_GET["action"];
    }

    switch ($action) {

        case "showprac": // show info about a prat

            // https://gsb-lycee.ga/views/Praticiens.php?action=showprac&pratid=3
            if(isset($_GET['pratid'])) {
                    
                $pratinfo = getPraticienInfosById($connexion, $_GET['pratid']);

                // if result from query is not empty, then start tempo & create table filled with data from the result set
                // else, display, "no result"
                ob_start(); // start temp

                // if praticien exist
                if ($pratinfo != false) {
                    echo("<h1>Information à propos du praticien</h1>");
                        echo("<table style='text-align: center;' id=''>");
                                echo("<tr><td>Numéro</td><td>{$pratinfo['Numero']}</td></tr>");
                                echo("<tr><td>Nom</td><td>{$pratinfo['Nom']}</td></tr>");
                                echo("<tr><td>Prenom</td><td>{$pratinfo['Prenom']}</td></tr>");
                                echo("<tr><td>Adresse</td><td>{$pratinfo['Adresse']}</td></tr>");
                                echo("<tr><td>Code Postal</td><td>{$pratinfo['Cp']}</td></tr>");
                                echo("<tr><td>Coef Notoriete</td><td>{$pratinfo['Notoriete']}</td></tr>");
                                echo("<tr><td>Type Praticien</td><td>{$pratinfo['Libelle']}</td></tr>");
                        echo ("</table>");
                    echo("<br>");

                    /* == 2nd Part, Rapports == */
                    echo("<h2>Rapports le concernant</h2>");
                        // get all rapports by Numero of praticien
                        $rapports = getRapportsByPraticiens($connexion, $pratinfo['Numero']);
                        echo("<div>");
                            // if result is empty (false), then display "Aucun rapport...."
                            if($rapports == false){
                                echo("&nbsp;&nbsp;&nbsp;&nbsp;Aucun rapport lui concernant n'a été trouvé.");
                            } else {
                                /* otherwise, as $rapports looks like 
                                [
                                    ['id' => '', 'rapDate' => '',...]
                                    ['id' => '', 'rapDate' => '',...]
                                    ['id' => '', 'rapDate' => '',...]
                                    ['id' => '', 'rapDate' => '',...]
                                ]
                                */
                                foreach($rapports as $row){
                                    echo("&nbsp;&nbsp;&nbsp;&nbsp;<a href='/views/Rapports.php?action=consult&rapid={$row['id']}'>Rapport N°{$row['id']} Datant du {$row['rapDate']}</a><br>");
                                }
                            }
                        echo("</div>");
                } else {
                    // if no result
                    echo("Aucun résultat ne correspond à votre recherche.");
                }

                // Define values for layout.php
                $title= "GSB - Praticien " . $pratinfo['Nom'];
                $content = ob_get_clean();
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
            break;
        
        // https://gsb-lycee.ga/views/Praticiens.php?action=consult
        default:
            // Render default page
            $title="GSB - Liste des Praticiens";
            $content = showAllPraticienstable($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }