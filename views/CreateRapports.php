<?php
    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // import functions to separate sql request from the view 
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/medicaments_model.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/praticiens_model.php");

    ob_start();
?>

<div id="new_rapportvisite">
    <form name="formRAPPORT_VISITE" method="post" action="/controller/rapport_visite_controller.php">
        <h1> Rapport de visite </h1>
        <label class="titre">NUMERO :</label>
        <input type="text" size="10" name="RAP_NUM" class="zone" required /><br>

        <label class="titre">DATE VISITE :</label>
        <input type="datetime-local" size="10" name="RAP_DATEVISITE" class="zone" required /><br>

        <label class="titre">PRATICIEN :</label>
        <select name="PRA_NUM" class="zone" required>
            <option value='NULL' selected>Choisisez un praticien</option>
            <?= getPraticiensOptions($connexion) ?>
        </select><br>

        <label class="titre">COEFFICIENT :</label>
        <input type="text" maxlength="1" name="PRA_COEFF" class="zone" required /><br>

        <label class="titre">REMPLACANT :</label>
        <input type="checkbox" class="zone" checked="false" onClick="selectionne(true,this.checked,'PRA_REMPLACANT');" />
        <input name="PRA_REMPLACANT" disabled="disabled" class="zone">
        <br>

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
        <br><textarea rows="5" cols="50" name="RAP_BILAN" class="zone" spellcheck="true" required></textarea><br>

        <label class="titre">
            <h3> Eléments présentés </h3>
        </label>

        <label class="titre">PRODUIT 1 : </label>
        <select name="PROD1" class="zone">
            <option value='NULL' selected>Medicament</option>
            <?= getMedicamentsOptions($connexion) ?>
        </select>
        <br>

        <label class="titre">PRODUIT 2 : </label>
        <select name="PROD2" class="zone">
            <option value='NULL' selected>Medicament</option>
            <?= getMedicamentsOptions($connexion) ?>
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
                <?= getMedicamentsOptions($connexion) ?>
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
require($_SERVER["DOCUMENT_ROOT"] . "/views/layout/layout.php");
