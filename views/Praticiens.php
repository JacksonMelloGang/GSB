<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

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
            if(isset($_GET['pratid'])) {

                $sql = "SELECT praNum, praNom, praPrenom, praAdresse, praCp, praCoefNotoriete, typLibelle FROM praticien, typepraticien WHERE praticien.typCode = typepraticien.typCode AND praNum = {$_GET['pratid']}";

                // Prepare Request to avoid SQL Injection
                $stmt = $connexion->prepare("SELECT praNum, praNom, praPrenom, praAdresse, praCp, praCoefNotoriete, typLibelle FROM praticien, typepraticien WHERE praticien.typCode = typepraticien.typCode AND praNum = :pranum");
                $stmt->execute(array(':pranum' => $_GET['pratid']));

                // Fetch data, supposed to have one row 
                $result = $stmt->fetch();
                
                $praNumero = $result['praNum'];
                $praNom = $result['praNom'];
                $praPrenom = $result['praPrenom'];
                $praAdresse = $result['praAdresse'];
                $praAdresse = str_replace(array(" r ", " av ", " pl "), array(" Rue ", " Avenue ", " Place "), $praAdresse);
                $praCp = $result['praCp'];
                $praCoefNotoriete = $result['praCoefNotoriete'];
                $pratypCode = $result['typLibelle'];

                // if result from query is not empty, then start tempo & create table filled with data from the result set
                // else, display, "no result"
                ob_start(); // start temp

                if ($result !== false) {
                    echo("<h1>Information à propos du praticien</h1>");
                        echo("<table style='text-align: center;' id=''>");
                                echo("<tr><td>Numéro</td><td>{$praNumero}</td></tr>");
                                echo("<tr><td>Nom</td><td>{$praNom}</td></tr>");
                                echo("<tr><td>Prenom</td><td>{$praPrenom}</td></tr>");
                                echo("<tr><td>Adresse</td><td>{$praAdresse}</td></tr>");
                                echo("<tr><td>Code Postal</td><td>{$praCp}</td></tr>");
                                echo("<tr><td>Coef Notoriete</td><td>{$praCoefNotoriete}</td></tr>");
                                echo("<tr><td>Type Praticien</td><td>{$pratypCode}</td></tr>");
                        echo ("</table>");
                    echo("<br>");

                    /* == 2nd Part, Rapports == */
                    // Prepare Request to avoid SQL Injection
                    $rapport_stmt = $connexion->prepare("SELECT id, rapDate FROM praticien, rapportvisite WHERE praticien.praNum = rapportvisite.praNum AND rapportvisite.praNum = :pranum");
                    $rapport_stmt->execute(array(':pranum' => $_GET['pratid']));
                    $rapport_result = $rapport_stmt->fetch();
                    
                    echo("<h2>Rapports le concernant</h2>");
                        if($rapport_result !== false){
                            echo("<div>");
                            while($rapport_result != false){

                                echo("&nbsp;&nbsp;&nbsp;&nbsp;<a href='/views/Rapports.php?action=consult&rapid={$rapport_result['id']}'>Rapport N°{$rapport_result['id']} Datant du {$rapport_result['rapDate']}</a><br>");
                                
                                $rapport_result = $rapport_stmt->fetch();
                            }        

                            echo("</div>");
                        } else {
                            echo("&nbsp;&nbsp;&nbsp;&nbsp;Aucun Rapport le concernant.");
                        }
                    } else {
                    // if no result
                    echo("Aucun résultat ne correspond à votre recherche.");
                }

                


                // Define values for layout.php
                $title= "GSB - Praticien " . $praNom;
                $content = ob_get_clean();
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
            break;
        default:
            // Render default page
            $title="GSB - Liste des Praticiens";
            $content = showAllPraticienstable($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }