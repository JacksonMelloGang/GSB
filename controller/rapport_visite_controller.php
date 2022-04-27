<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    function converttodate($datetoconvert){
        $year = substr($datetoconvert, 0, 4);
        $month = substr($datetoconvert, 5, 2);
        $day = substr($datetoconvert, 8, 2);
        $date = "$year-$month-$day";
        return date_format(new DateTime("{$date}T00:00:00"), "m-d-Y");
    }

    // validate & sanitize user-inputs to avoid sql injection or exploits with filter_var() or htmlspecialchars()
    /*
        Validating data = Determine if the data is in proper form.
        Sanitizing data = Remove any illegal character from the data.
        -------------------------------------------------------------

        numéro: RAP_NUM (text)
        date visite: RAP_DATEVISITE (date)
        praticien: PRA_NUM (text)
        coefficient: PRA_COEFF (text)
        remplacant: PRA_REMPLACANT (checkbox, select !important)
        date: RAP_DATE (date)
        motif: RAP_MOTIF (select -> PRD, ACT, REL, SOL, AUT)
        bilan: RAP_BILAN (textarea)
        produit 1: PROD1 (select)
        produit 2: PROD2 (select)
        documentation offerte: RAP_DOC (checkbox)
        echantillon: PRA_ECH1, PRA_QTE1 (select, input)
        saisie definitive: RAP_LOCK (checkbox)
    */

    // 1ere partie
    $num = filter_var($_POST['RAP_NUM'], FILTER_VALIDATE_INT) ? filter_var($_POST['RAP_NUM'], FILTER_SANITIZE_NUMBER_INT) : die("Numéro Non Valide"); // verify if it's an int first, and if yes convert value into sanitize to convert into valid INT 
    $datevisite = converttodate($_POST['RAP_DATEVISITE']);
    $practicien = filter_var($_POST['PRA_NUM'], FILTER_VALIDATE_INT) ? filter_var($_POST['PRA_NUM'], FILTER_SANITIZE_NUMBER_INT) : die("Numéro Practicien Non Valide");
    $coefficient = filter_var($_POST['PRA_COEFF'], FILTER_VALIDATE_INT) ? filter_var($_POST['PRA_COEFF'], FILTER_SANITIZE_NUMBER_INT) : die("Coefficient Non valide");
    $remplacant = isset($_POST['PRA_REMPLACANT']) == true ? htmlspecialchars($_POST['PRA_REMPLACANT']) : "Pas de Remplacant";
    $date = converttodate($_POST['RAP_DATE']); // convert into date
    $motif = htmlspecialchars($_POST['RAP_MOTIF']); // sanitize motif
    $bilan = htmlspecialchars($_POST['RAP_BILAN']); // sanitize bilan

    // 2e partie
    $produit1 = htmlspecialchars($_POST['PROD1']);
    $produit2 = htmlspecialchars($_POST['PROD2']);
    $documentation = isset($_POST['RAP_DOC']) ? "true" : "false";

    // 3e partie
    $nbechantillon = filter_var($_POST['nbechantillon'], FILTER_VALIDATE_INT) ? $_POST['nbechantillon'] : 1; // get number of samples (default: 1) 
    $prodarray = [];
    // insert each samples into an array
    for($i = 1; $i <= $nbechantillon; $i++){
        array_push($prodarray, $_POST["PRA_QTE{$i}"]);
    } 

    $saisiedef = isset($_POST['RAP_LOCK']) ? "true" : "false";

    ob_start();
?>  
    <div id="table-recap-div">
        <table class="table-recap">
            <th>Donnée</th><th>User-Input</th>
            <tr>
                <td>Numéro</td>
                <td><?= $num ?></td>
            </tr>
            <tr>
                <td>Date de visite</td>
                <td><?= $datevisite ?></td>
            </tr>
            <tr>
                <td>Praticien</td>
                <td><?= $practicien ?></td>
            </tr>
            <tr>
                <td>Coefficient</td>
                <td><?= $coefficient ?></td>
            </tr>
            <tr>
                <td>Remplacant</td>
                <td><?= $remplacant ?></td>
            </tr>
            <tr>
                <td>Date</td>
                <td><?= $date ?></td>
            </tr>
            <tr>
                <td>Motif</td>
                <td><?= $motif ?></td>
            </tr>
            <tr>
                <td>Bilan</td>
                <td><?= $bilan ?></td>
            </tr>
            <tr>
                <td>produit1</td>
                <td><?= $produit1 ?></td>
            </tr>
            <tr>
                <td>Produit N°2</td>
                <td><?= $produit2 ?></td>
            </tr>
            <tr>
                <td>Docummentation Offerte</td>
                <td><?= $documentation ?></td>
            </tr> 
            <?php
                for($i=0; $i < sizeof($prodarray); $i++){
                    $echantillon = empty($prodarray[$i]) ? "Pas d'échantillon proposé" : $prodarray[$i];
                    echo("<tr>");
                    echo("<td>Echantillon N°". intval($i+1) ."</td><td>{$echantillon}</td>");
                    echo("</td>");
                }
            ?>
            <tr>
                <td>Saisie Definitif</td>
                <td><?= $saisiedef ?></td>
            </tr>
        </table>
        <button onclick="history.go(-1)">Annuler l'envoi</button>
        <button onclick="">Confirmer l'envoi</button>
    </div>

<?php
    // Render default page
    $title="GSB - Traitement En cours...";
    $content = ob_get_clean();
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");

    

?>