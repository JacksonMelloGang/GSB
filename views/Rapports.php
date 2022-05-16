<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    function showrapports($connexion){
        // 2 Query
        $personnalrapport_sql = "SELECT id, rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif, saisiedef FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule AND visiteur.visMatricule = '{$_SESSION["userId"]}'";
        $other_rapport_sql = "SELECT rapNum, visNom, visPrenom, rapDate, rapBilan, rapMotif FROM rapportvisite, visiteur WHERE rapportvisite.visMatricule = visiteur.visMatricule AND NOT visiteur.visMatricule = '{$_SESSION["userId"]}'";

        // Get Rapports from database & display in table
        $personnalrapport_result = $connexion->query($personnalrapport_sql);
        ob_start();
                echo("<h1>Vos Rapports</h1>");
                echo("<div class='table-center'>");
                    echo("<table>");
                        echo("<th>Numéro Rapport</th><th>Date</th><th>Bilan</th><th>Motif</th>");
                        $row = $personnalrapport_result->fetch();
                        while($row){
                            echo("<tr>");
                                $editable = "";
                                if($row["saisiedef"] == 0){
                                    $editable = "<td><a href='/views/Rapports.php?&action=edit&rapid={$row['id']}'>Editer</a></td>";
                                }
                                echo("<td>{$row['rapNum']}</td><td>{$row['rapDate']}</td><td>{$row['rapBilan']}</td><td>{$row['rapMotif']}</td>$editable");
                            echo("</tr>");
                            $row = $personnalrapport_result->fetch();
                        }
                    echo("</table>");
                echo("</div>");

                ///////////////////////////////////////////////////////////////////////
                echo("<hr style='width: 100%;margin-top: 10px; margin-bottom:10px;'>");
                ///////////////////////////////////////////////////////////////////////

                echo("<h2>Autres Rapports</h2>");
                echo("<div class='table-center'>");
                    $resultrapport = $connexion->query($other_rapport_sql);
                        echo("<table>");
                            $row = $resultrapport->fetch();
                            if($row == false){
                                echo("Aucun rapport.");
                            } else {
                                echo("<th>Numéro Rapport</th><th>Nom</th><th>Prénom</th><th>Date</th><th>Bilan</th><th>Motif</th>");
                                while($row != false){
                                    echo("<tr>");
                                        echo("<td>{$row['rapNum']}</td><td>{$row['visNom']}</td><td>{$row['visPrenom']}</td><td>{$row['rapDate']}</td><td>{$row['rapBilan']}</td><td>{$row['rapMotif']}</td>");
                                    echo("</tr>");
                                    $row = $resultrapport->fetch();
                            }
                        }
                    echo("</table>");
                echo("</div>");
        return ob_get_clean();
    }

    function show_specific_rapport($rapportId, $connexion){

        $sqlcheckauthor = "SELECT visMatricule FROM rapportvisite WHERE id = $rapportId";
        $stmtauthor = $connexion->query($sqlcheckauthor);
        $resultauthor = $stmtauthor->fetch();
        if($resultauthor === false){
            echo("Vous n'avez pas la permission de modifier ce rapport!");
            die();
        } else {
            if($resultauthor['visMatricule'] != $_SESSION['userId']){
                echo("Vous n'avez pas la permission de modifier ce rapport!");
                die();
            }
        }

        $sql = "SELECT rapNum, rapDate, rapBilan, rapportvisite.praNum, praPrenom, praNom, praAdresse, rapMotif FROM rapportvisite, praticien WHERE rapportvisite.praNum = praticien.praNum AND rapportvisite.id = '{$rapportId}';";
        $stmt = $connexion->query($sql);
        $result = $stmt->fetch();

        ob_start();
        ?>
        <h1>Rapport N°<?= $result['rapNum'] ?></h1>
        <div class='table-center'>
            <form id='updateform' method='POST'>
                <h2>Rapport:</h2>
                <table style='width: 50%;margin-left: auto;margin-right: auto'>
                    <tr>
                        <td>Date</td>
                        <td><?= $result['rapDate']?></td>
                    </tr>

                    <tr>
                        <td>Bilan</td>
                        <td><textarea id='bilan' rows="5" cols="40"><?=  $result['rapBilan'] ?></textarea></td>
                    </tr>

                    <tr>
                        <td>Motif</td>
                        <td><?= $result['rapMotif'] ?></td>
                    </tr>
                </table>

                <h2>Praticien:</h2>
                <table style='width: 50%;margin-left: auto;margin-right: auto'>
                    <tr>
                        <td>Numéro</td>
                        <td><?= $result['rapNum'] ?></td>
                    </tr>
                    <tr>
                        <td>Nom & Prénom</td>
                        <td><?= $result['praNom'] ?>-<?= $result['praPrenom'] ?></td>
                    </tr>

                </table>

                <input type="submit" value="Enregistrer">
                <input type="reset" value="Annuler">
            </form>

            <script>
                $("#updateform").submit((e) => {
                    var formdata = {
                        bilan: $("#bilan").val()
                    };

                    $.ajax({
                        type: "POST",
                        url: "https://beta-gsb-lycee.ga/controller/update_rapport_controller.php",
                        data: formdata
                        
                    })

                    e.preventDefault();
                });
            </script>

        </div>
        
<?php
        return ob_get_clean();
    }

    $action = "";
    if(isset($_GET["action"])){
        $action = $_GET["action"];
    }

    switch($action){
        case "new":
            ob_start();
            /* Creating a form to add a new rapport de visite. */
        ?>
            <div id="new_rapportvisite">
                <form name="formRAPPORT_VISITE" method="post" action="/controller/rapport_visite_controller.php" >
                    <h1> Rapport de visite </h1>
                    <label class="titre">NUMERO :</label>
                    <input type="text" size="10" name="RAP_NUM" class="zone" required/><br>
                    
                    <label class="titre">DATE VISITE :</label>
                    <input type="datetime-local" size="10" name="RAP_DATEVISITE" class="zone" required/><br>
                    
                    <label class="titre">PRATICIEN :</label>
                    <select name="PRA_NUM" class="zone" required>
                        <option value='*' selected>Choisisez un praticien</option>
                        <?php
                            $query = $connexion->query("SELECT praNum, praNom, praPrenom FROM praticien");
                            $result = $query->fetch();
                            while($result != false){
                                echo("<option value='{$result['praNum']}'>{$result['praNom']} {$result['praPrenom']}</option>");
                                
                                $result = $query->fetch();
                            }
                        ?>
                    </select><br>
                    
                    <label class="titre">COEFFICIENT :</label>
                    <input type="text" size="6" name="PRA_COEFF" class="zone" required/><br>
                    
                    <label class="titre">REMPLACANT :</label>
                    <input type="checkbox" class="zone" checked="false" onClick="selectionne(true,this.checked,'PRA_REMPLACANT');"/>
                        <select name="PRA_REMPLACANT" disabled="disabled" class="zone">
                            <?php
                                $query = $connexion->query("SELECT praNum, praNom, praPrenom FROM praticien");
                                $result = $query->fetch();
                                while($result != false){
                                    echo("<option value='{$result['praNum']}'>{$result['praNom']} {$result['praPrenom']}</option>");
                                    
                                    $result = $query->fetch();
                                }
                            ?>
                        </select>
                    <br>

                    <!--
                    <label class="titre">DATE :</label>
                    <input type="date" size="19" name="RAP_DATE" class="zone" required/><br>
                    -->
                    
                    <label class="titre">MOTIF :</label>
                    <select name="RAP_MOTIF" class="zone" onClick="selectionne('AUT',this.value,'RAP_MOTIFAUTRE');" required> 
                        <option value="PRD">Périodicité</option>
                        <option value="ACT">Actualisation</option>
                        <option value="REL">Relance</option>
                        <option value="SOL">Sollicitation praticien</option>
                        <option value="AUT">Autre</option>
                    </select>
                    <input type="text" name="RAP_MOTIFAUTRE" class="zone" disabled="disabled" />
                    <br>
                    
                    <label class="titre">BILAN :</label>
                    <br><textarea rows="5" cols="50" name="RAP_BILAN" class="zone" spellcheck="true" required ></textarea><br>
                    
                    <label class="titre">
                        <h3> Eléments présentés </h3>
                    </label>
                    
                    <label class="titre">PRODUIT 1 : </label>
                    <select name="PROD1" class="zone">
                        <option value='NONE' selected>Medicament</option>
                        <?php
                                $query = $connexion->query("SELECT medNomCommercial, medDepotLegal FROM medicament");
                                $result = $query->fetch();
                                while($result != false){
                                    echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
                                    
                                    $result = $query->fetch();
                                }
                        ?>
                    </select>
                    <br>

                    <label class="titre">PRODUIT 2 : </label>
                    <select name="PROD2" class="zone">
                        <option value='NONE' selected>Medicament</option>
                            <?php
                                    $query = $connexion->query("SELECT medNomCommercial, medDepotLegal FROM medicament");
                                    $result = $query->fetch();
                                    while($result != false){
                                        echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
                                        
                                        $result = $query->fetch();
                                    }
                            ?>
                    </select>
                    <br>

                    <label class="titre">DOCUMENTATION OFFERTE :</label>
                    <input name="RAP_DOC" type="checkbox" class="zone" checked="false" /><br>
                    
                    <!-- 3e Partie, Echantillon -->
                    <label class="titre">
                        <h3>Echantillons</h3>
                    </label>
                    <div class="titre" id="lignes">
                        <label class="titre">Produit : </label>
                        <select name="PRA_ECH1" class="zone">
                            <option value='NONE' selected>Medicament</option>
                                <?php
                                        $sql = "SELECT medNomCommercial, medDepotLegal FROM medicament";
                                        $query = $connexion->query($sql);
                                        $result = $query->fetch();
                                        while($result != false){
                                            echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
                                            
                                            $result = $query->fetch();
                                        }
                                ?>
                        </select>
                        <label for="PRA_QTE1">Qté : </label>
                        <input type="text" name="PRA_QTE1" size="2" class="zone" />
                        <input type="button" id="but1" value="+" onclick="ajoutLigne(1);" class="zone" />
                    </div>
                    <input type="hidden" value="1" name="nbechantillon">

                    <label class="titre">SAISIE DEFINITIVE :</label>
                    <input name="RAP_LOCK" type="checkbox" class="zone" checked="false" /><br>

                    <label class="titre"></label>
                    <div class="zone">
                        <input type="reset" value="Annuler"></input>    
                        <input type="submit" value="Envoyer"></input>
                    </div>
                </form>
            </div>

        <?php
            // still action = new
            $title = "GSB - Rapports";
            $content = ob_get_clean();
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;

        case "consult":

            if(isset($_GET["rapid"])){
                $rapportId = htmlspecialchars($_GET["rapid"]);
                $content = show_specific_rapport($rapportId, $connexion);
            } else {
                $content = showrapports($connexion);
            }

            $title = "GSB - Rapports";
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
        case "edit":
            if(isset($_GET["rapid"])){
                $rapid = htmlspecialchars($_GET["rapid"]);
                $content = show_specific_rapport($rapid, $connexion);
            } else {
                $title = "GSB - Rapports";
                $content = showrapports($connexion);
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
            $title = "GSB - Rapports";
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
        /* It's the default case, if no action is specified, it will show the default page. */
        default:
            $title = "GSB - Rapports";
            $content = showrapports($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }



