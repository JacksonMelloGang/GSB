<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");


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

    $action = "";
    if(isset($_GET["action"])){
        $action = $_GET["action"];
    }

    switch($action){
        case "new":
            ob_start();
        ?>

        <div id="new_rapportvisite">
            <form name="formRAPPORT_VISITE" method="post" action="/controller/rapport_visite_controller.php" >
                <h1> Rapport de visite </h1>
                <label class="titre">NUMERO :</label>
                <input type="text" size="10" name="RAP_NUM" class="zone"/><br>
                
                <label class="titre">DATE VISITE :</label>
                <input type="text" size="10" name="RAP_DATEVISITE" class="zone"/><br>
                
                <label class="titre">PRATICIEN :</label>
                <select name="PRA_NUM" class="zone">
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
                <input type="text" size="6" name="PRA_COEFF" class="zone"/><br>
                
                <label class="titre">REMPLACANT :</label>
                <input type="checkbox" class="zone" checked="false" onClick="selectionne(true,this.checked,'PRA_REMPLACANT');"/>
                    <select name="PRA_REMPLACANT" disabled="disabled" class="zone">

                    </select>
                <br>

                <label class="titre">DATE :</label>
                <input type="text" size="19" name="RAP_DATE" class="zone" /><br>
                
                <label class="titre">MOTIF :</label>
                <select name="RAP_MOTIF" class="zone" onClick="selectionne('AUT',this.value,'RAP_MOTIFAUTRE');">
                    <option value="PRD">Périodicité</option>
                    <option value="ACT">Actualisation</option>
                    <option value="REL">Relance</option>
                    <option value="SOL">Sollicitation praticien</option>
                    <option value="AUT">Autre</option>
                </select>
                <input type="text" name="RAP_MOTIFAUTRE" class="zone" disabled="disabled" />
                <br>
                
                <label class="titre">BILAN :</label>
                <br><textarea rows="5" cols="50" name="RAP_BILAN" class="zone"></textarea><br>
                
                <label class="titre">
                    <h3> Eléments présentés </h3>
                </label>
                
                <label class="titre">PRODUIT 1 : </label>
                <select name="PROD1" class="zone">

                </select>
                <br>

                <label class="titre">PRODUIT 2 : </label>
                <select name="PROD2" class="zone">

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
                        <option>Produits</option>
                    </select><input type="text" name="PRA_QTE1" size="2" class="zone" />
                    <input type="button" id="but1" value="+" onclick="ajoutLigne(1);" class="zone" />
                </div>

                <label class="titre">SAISIE DEFINITIVE :</label>
                <input name="RAP_LOCK" type="checkbox" class="zone" checked="false" /><br>

                <label class="titre"></label>
                <div class="zone">
                    <input type="reset" value="annuler"></input>    <input type="submit"></input>
                </div>
            </form>
        </div>

        <?php
            $title = "GSB - Rapports";
            $content = ob_get_clean();
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
        default:
            $title = "GSB - Rapports";
            $content = showrapports($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }



