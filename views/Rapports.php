<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // import functions to separate sql request from the view 
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/rapports_model.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/medicaments_model.php");

    function showrapports($connexion){
        // 2 Query
        $personnalrapport_sql = "SELECT id, rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif, saisiedef FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule AND visiteur.visMatricule = '{$_SESSION["userId"]}'";
        $other_rapport_sql = "SELECT id, rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule AND NOT visiteur.visMatricule = '{$_SESSION["userId"]}'";

        // Get User Rapport's from database & display in table
        $personnalrapport_result = $connexion->query($personnalrapport_sql);
        ob_start();
                echo("<h1>Vos Rapports</h1>");
                echo("<div><label>Activer le mode suppression: </label><input type='checkbox' id='toggledeleterap'></div><br>");
                
                echo("<div class='table-center'>");
                    echo("<table>");
                        echo("<th>Numéro Rapport</th><th>Date de visite</th><th>Bilan</th><th>Motif</th>");
                        $row = $personnalrapport_result->fetch();

                        while($row != false){
                            echo("<tr>");
                                $editable = "<td></td>";
                                if($row["saisiedef"] == 0){
                                    $editable = "<td><a href='/views/Rapports.php?&action=edit&rapid={$row['id']}'>Editer</a></td>";
                                }

                                echo("<td>{$row['rapNum']}</td><td>{$row['rapDate']}</td><td><a href='/views/Rapports.php?action=consult&rapid={$row['id']}'>{$row['rapBilan']}</a></td><td>{$row['rapMotif']}</td>$editable <td><a class='deleterap' href='/views/Rapports.php?action=delete&rapid={$row['id']}'>Supprimer</a></td>");
                            echo("</tr>");
                            $row = $personnalrapport_result->fetch();
                        }
                    echo("</table>");
                echo("</div>");

                ///////////////////////////////////////////////////////////////////////
                echo("<hr style='width: 100%;margin-top: 10px; margin-bottom:10px;'>");
                ///////////////////////////////////////////////////////////////////////

                // get Other user rapport's
                echo("<h2>Autres Rapports</h2>");
                echo("<div class='table-center'>");
                    $resultrapport = $connexion->query($other_rapport_sql);
                        echo("<table>");
                            $row = $resultrapport->fetch();
                            if($row == false){
                                echo("Aucun rapport.");
                            } else {
                                echo("<th>Numéro Rapport</th><th>Nom</th><th>Prénom</th><th>Date de visite</th><th>Bilan</th><th>Motif</th>");
                                while($row != false){
                                    echo("<tr>");
                                        echo("<td>{$row['rapNum']}</td><td>{$row['visNom']}</td><td>{$row['visPrenom']}</td><td>{$row['rapDate']}</td><td><a href='/views/Rapports.php?action=consult&rapid={$row["id"]}'>{$row['rapBilan']}</a></td><td>{$row['rapMotif']}</td>");
                                    echo("</tr>");
                                    $row = $resultrapport->fetch();
                            }
                        }
                    echo("</table>");
                echo("</div>");
        return ob_get_clean();
    }

    function show_specific_rapport($rapportId, $connexion, $edit = false){

        if($edit == true){
            $isallowed = isAllowedtoEdit($connexion, $rapportId);
            if($isallowed == false){
                die("Vous n'avez pas la permission de modifier ce rapport !");
            }

            $isEditable = isEditable($connexion, $rapportId);
            if($isEditable == false){
                die("Ce Rapport n'est pas modifiable !");
            }
        }

        $sql = "SELECT rapportvisite.id, rapNum, rapDate, rapBilan, rapportvisite.praNum, rapMotif, rapDateSaisie, saisiedef, docfourni, prod1, prod2, praPrenom, praNom, praAdresse, praCp, praVille, praCoefnotoriete FROM rapportvisite, praticien WHERE rapportvisite.praNum = praticien.praNum AND rapportvisite.id = '{$rapportId}';";
        $stmt = $connexion->query($sql);
        $result = $stmt->fetch();

        if($result === false){
            die("Numéro de rapport invalide.");
        }

        $rapportnum = $result['rapNum'];
        $datevisite = $result['rapDate'];
        $bilan = $result['rapBilan'];
        $motif = $result['rapMotif'];
        $datesaisie = $result['rapDateSaisie'];
        $saisiedef = $result['saisiedef'] == 1 ? "Oui" : "Non";
        $docfourni = $result['docfourni'] == 1 ? "Oui" : "Non";
        $produit1 = $result['prod1'];
        if(empty($produit1)){
            $produit1 = "Non renseignée.";
        }
        $produit2 = $result['prod2'];
        if(empty($produit2)){
            $produit2 = "Non renseignée.";
        }
        $numpra = $result['rapNum'];
        $pranom = $result['praNom'];
        $praprenom = $result['praPrenom'];
        $praadresse = $result['praAdresse'];
        $pranotoriete = $result['praCoefnotoriete'];
        $pracp = $result['praCp'];
        $praville = $result['praVille'];

        ob_start();
        ?>
        <h1>Rapport N°<?= $rapportnum ?></h1>
        <div class='table-center'>
            <form id='updateform' method='POST'>
                <h2>Rapport:</h2>
                <table style='width: 50%;margin-left: auto;margin-right: auto' id="table-info">
                    <tr>
                        <td>Date de visite</td>
                        <td><?= $datevisite ?></td>
                    </tr>

                    <tr>
                        <td>Bilan</td>
                        <?php
                        
                        if($edit == false){
                            echo("<td><textarea id='bilan' rows='5' cols='40' name='readonly-bilan' readonly>{$bilan} </textarea></td>");
                        } else {
                            echo("<td><textarea id='bilan' rows='5' cols='40' '>{$bilan}</textarea></td>");
                        }

                        ?>
                    </tr>
                    <tr>
                        <td>Produit N°1:</td>
                        <td>
                                <?php
                                   if($edit == true){
                                        echo("<select id='produit1'>");
                                            echo("<option value=''>Aucun Médicament Séléctioné</option>");
                                            echo(getSelectedMedicamentsAsOption($connexion, $produit1));
                                        echo("</select>");
                                    } else {
                                        echo("$produit1");
                                    }
                                ?>

                        </td>
                    </tr>
                    <tr>
                        <td>Produit N°2:</td>
                        <td>
                                <?php
                                   if($edit == true){
                                        echo("<select id='produit2'>");
                                            echo("<option value=''>Aucun Médicament Séléctioné</option>");
                                            echo(getSelectedMedicamentsAsOption($connexion, $produit2));
                                        echo("</select>");
                                    } else {
                                       echo("$produit1");
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Motif</td>
                        <td><?= $motif ?></td>
                    </tr>
                    <tr>
                        <td>Date de Saisie</td>
                        <td><?= $datesaisie ?></td>
                    </tr>
                    <tr>
                        <td>Saisie Définitif</td>
                        <td><?=  $saisiedef ?></td>
                    </tr>
                    <tr>
                        <td>Documentation Fourni</td>
                        <td><?=  $docfourni ?></td>
                    </tr>
                </table>

                <h2>Praticien:</h2>
                <table style='width: 50%;margin-left: auto;margin-right: auto' name='rapport-table-praticien' id="table-info">
                    <tr>
                        <td>Numéro</td>
                        <td><?= $numpra ?></td>
                    </tr>
                    <tr>
                        <td>Nom & Prénom</td>
                        <td><?= $pranom ?>-<?= $praprenom ?></td>
                    </tr>

                    <tr>
                        <td>Adresse</td>
                        <td><?= str_replace(array(" r ", " av ", " pl "), array(" Rue ", " Avenue ", " Place "), $praadresse) ?></td>
                    </tr>

                    <tr>
                        <td>Ville</td>
                        <td><?= "$pracp - $praville" ?></td>
                    </tr>

                    <tr>
                        <td>Notoriété</td>
                        <td><?= $pranotoriete ?></td>
                    </tr>

                </table>
                <br>

                <?php

                    if($edit == true){

                        echo("<input type='hidden' id='rapid' value='{$result['id']}'>");

                        //Saisie Def
                        echo("<label for='saisiedef'>Saisie Définitif:</label>");
                        echo("<input type='checkbox' name='saisiedef' id='saisiedef'>");
                        echo("<br>");
                        
                        // Button Confirm or Cancel
                        echo("<input type='submit' value='Enregistrer'>");
                        echo("<input type='reset' value='Annuler'>");
                    }
                ?>                

                <br><br>
                    <span id="info"></span>
                <br>

            </form>

        </div>
        
<?php
        return ob_get_clean();
    }






    // PARTIE: ACTION (AFFICHER / MODIFIER RAPPORTS)
    $action = "";
    if(isset($_GET["action"])){
        $action = $_GET["action"];
    }

    switch($action){

        // https://gsb-lycee.ga/views/Rapports.php?&action=consult&rapid=<RAPID>
        case "consult":
            //if rapid is set, show rapports without edit mode
            if(isset($_GET["rapid"])){
                $rapportId = htmlspecialchars($_GET["rapid"]);
                $content = show_specific_rapport($rapportId, $connexion, false);
            } else {
                //if rapid not set
                // https://gsb-lycee.ga/views/Rapports.php?&action=consult
                $content = showrapports($connexion);
            }

            $title = "GSB - Rapports";
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;

        // https://gsb-lycee.ga/views/Rapports.php?&action=edit&rapid=<RAPID>
        case "edit":
            if(isset($_GET["rapid"])){
                //get rapport id from url
                $rapid = htmlspecialchars($_GET["rapid"]);  

                $content = show_specific_rapport($rapid, $connexion, true);
                $title = "GSB - Rapports";
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            } else {
                $title = "GSB - Rapports";
                $content = showrapports($connexion);
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
        break;

        // https://gsb-lycee.ga/views/Rapports.php?&action=delete&rapid=<RAPID>
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
        // https://gsb-lycee.ga/views/Rapports.php?&action=consult
        default:
            $title = "GSB - Rapports";
            $content = showrapports($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }



