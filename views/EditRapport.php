<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // import functions to separate sql request from the view 
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/rapports_model.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/medicaments_model.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/praticiens_model.php");


    // rapid required, so we check if rapid is in the url (exemple: http://gsb.test:8080/views/EditRapport.php&rapid=38)
    if(isset($_GET['rapid'])){
        $rapportId = $_GET['rapid'];
    } else {
        die("ID du rapport non fourni.");
    }


    // if edit not equal to 1 or not in url, we disable edit mode
    if(isset($_GET['edit'])){
        $get_edit = $_GET['edit'];

        if($get_edit == 1){
            $edit = true;
        } else {
            $edit = false;
        }
    } else {
        $edit =false;
    }


    $praticiensoptions = getPraticiensOptions($connexion);
    $medicamentoptions = getMedicamentsOptions($connexion);

    // get rapport informations
    $row = getRapportInformationsById($connexion, $rapportId);

    if ($row == false) {
        die("Numéro de rapport invalide.");
    }

    // check if report is editable & is allowed to edit the report, if not return error
    if($edit == true) {
        $isallowed = isAllowedtoEdit($connexion, $rapportId, $_SESSION['userId']);
        if ($isallowed == false) {
            die("Vous n'avez pas la permission de modifier ce rapport !");
        }

        $isEditable = isEditable($connexion, $rapportId);
        if ($isEditable == false) {
            die("Ce Rapport n'est pas modifiable !");
        }
    }


    $rapportnum = $row['rapNum'];
    $datevisite = $row['rapDate'];
    $bilan = $row['rapBilan'];
    $motif = $row['rapMotif'];
    $datesaisie = $row['rapDateSaisie'];
    $saisiedef = $row['saisiedef'] == 1 ? "Oui" : "Non";
    $docfourni = $row['docfourni'] == 1 ? "Oui" : "Non";
    $produit1 = $row['prod1'];
    if (empty($produit1)) {
        $produit1 = "Non renseignée.";
    }
    $produit2 = $row['prod2'];
    if (empty($produit2)) {
        $produit2 = "Non renseignée.";
    }
    $numpra = $row['rapNum'];
    $pranom = $row['praNom'];
    $praprenom = $row['praPrenom'];
    $praadresse = $row['praAdresse'];
    $pranotoriete = $row['praCoefnotoriete'];
    $pracp = $row['praCp'];
    $praville = $row['praVille'];

    ob_start();
?>
<h1>Rapport N°<?= $rapportnum ?></h1>
<div class='table-center'>
    <form id='updateform' method='POST' name="formRAPPORT_VISITE">
        <h2>Rapport:</h2>
        <table style='width: 50%;margin-left: auto;margin-right: auto' id="table-info">
            <tr>
                <td>Date de visite</td>
                <td><?= $datevisite ?></td>
            </tr>

            <tr>
                <td>Bilan</td>
                <?php

                if ($edit == false) {
                    echo ("<td><textarea id='bilan' rows='5' cols='40' name='readonly-bilan' readonly>{$bilan} </textarea></td>");
                } else {
                    echo ("<td><textarea id='bilan' rows='5' cols='40' '>{$bilan}</textarea></td>");
                }

                ?>
            </tr>
            <tr>
                <td>Produit N°1:</td>
                <td>
                    <?php
                    if ($edit == true) {
                        echo ("<select id='produit1' name='PROD2'>");
                            echo ("<option value=''>Aucun Médicament Séléctioné</option>");
                            echo (getSelectedMedicamentsAsOption($connexion, $produit1));
                        echo ("</select>");
                    } else {
                        echo ("$produit1");
                    }
                    ?>

                </td>
            </tr>
            <tr>
                <td>Produit N°2:</td>
                <td>
                    <?php
                    if ($edit == true) {
                        echo ("<select id='produit2'> name='PROD2'");
                            echo ("<option value=''>Aucun Médicament Séléctioné</option>");
                            // apply selected attributes to the selected medicament from the report
                            echo (getSelectedMedicamentsAsOption($connexion, $produit2));
                        echo ("</select>");
                    } else {
                        echo ("$produit1");
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
                <td><?= $saisiedef ?></td>
            </tr>
            <tr>
                <td>Documentation Fourni</td>
                <td><?= $docfourni ?></td>
            </tr>
        </table>

        <h2>Echantillons:</h2>
        <table style='width: 50%;margin-left: auto;margin-right: auto' id="table-info">
            <?php
            $echantillon = getEchantillonsByRapport($connexion, $rapportId);
                
            if ($echantillon === false) {
                echo ("<tr><td>Pas d'échantillon.</td></tr>");
            } else {
                echo ("<th>N° Echantillon</th><th>Nom Echantillon</th>");
                $i = 0;
                foreach ($echantillon as $echantillonrow) {
                    $echantillonname = $echantillonrow["medNomCommercial"];
                    $echantilloncode = $echantillonrow["medDepotlegal"];
                    $echantillonfullname = "{$echantillonname} - {$echantilloncode}";

                    echo ("<tr>");
                    echo ("<td>Echantillon N°{$i}</td><td>$echantillonfullname</td>");
                    echo ("</tr>");
                    $i++;
                }
            }
            ?>
        </table>

        <?php
        if($edit == true){

                echo("<div class='titre' id='lignes'>");
                    echo("<label class='titre'>Produit : </label>");
                        echo("<select name='PRA_ECH1' class='zone'>");
                            echo("<option value='' selected>Medicament</option>");
                                echo("$medicamentoptions");
                        echo("</select>");
                    echo("<label for='PRA_QTE1'>Qté : </label>");
                    echo("<input type='text' name='PRA_QTE1' size='2' class='zone'/>");
                    echo("<input type='button' id='but1' value='+' onclick='ajoutLigne(1);' class='zone' />");
                echo("</div>");

            }
        ?>

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

        if ($edit == true) {
            //Saisie Def
            echo ("<label for='saisiedef'>Saisie Définitif:</label>");
            echo ("<input type='checkbox' name='saisiedef' id='saisiedef'>");
            echo ("<br>");

            // Button Confirm or Cancel
            echo ("<input type='submit' value='Enregistrer'>");
            echo ("<input type='reset' value='Annuler'>");


            
            // Hidden Infos
            echo ("<input type='hidden' name='rapid' id='rapid' value='{$row['id']}'>");
            
            if($echantillon == false){
                echo ("<input type='hidden' value='1' name='nbechantillon'>");
            } else {
                echo ("<input type='hidden' value='{$echantillon[0]['counted']}' name='nbechantillon'>");
            }

        }
        ?>

        <br><br>
        <span id="info"></span>
        <br>

    </form>

</div>

<?php

    $title = "GSB - Rapports";
    $content = ob_get_clean();
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
?>