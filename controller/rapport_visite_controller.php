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

        $hour = substr($datetoconvert, 11, 2);
        $minute = substr($datetoconvert, 14, 2);
        $secondes = substr($datetoconvert, 15, 2);

        $date = "{$year}-{$month}-{$day}T{$hour}:{$minute}:{$secondes}";
        return date_format(new DateTime("{$date}"), "Y-m-d\TH:i:s");
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
    // Valeur Conditionnel: Expression Logique (true|false) ? (true) valeur de la variable : (false/else) valeur de la variable
    // 
    // Pour résumé, on initialise les variables pour l'enregistrement du rapport puis, 
    // nous vérifions d'abord si les valeur entrées sont bien des nombres, date ou bien des chaînes de caractères,
    // si la condition est vraie, nous "sanitizons" / retire les caractères non valides et nous assigne le résultat du sanitize à la variable
    // si la condition est fausse, on prévient l'utilisateur (ex: Numéro Non Valide), et on arrête le script
    
    $rapnum = "";
    $datevisite = "";
    $praticien = "";
    $coefficient = "";
    $remplacant = "";
    $date = "";
    $motif = "";
    $bilan = "";

    $produit1 = "";
    $produit2 = "";
    $nbechantillon = "";
    $prodarray = [];
    $saisiedef = "";
    $documentation = "";

    // verify if it's an int first, and if yes, sanitize to convert into valid INT 
    if(filter_var($_POST['RAP_NUM'], FILTER_VALIDATE_INT) == true){
        $rapnum = filter_var($_POST['RAP_NUM'], FILTER_SANITIZE_NUMBER_INT);
    } else {
        echo("<a href='#' onClick='history.go(-1)'>Retour en arriere</a><br>");
        die("Numéro de rapport Non Valide");
    }

    // Conversion en format date sql (US)
    $datevisite = converttodate($_POST['RAP_DATEVISITE']);
    
    // verify if it's an int first, and if yes, sanitize to convert into valid INT 
    if(filter_var($_POST['PRA_NUM'], FILTER_VALIDATE_INT) == true){
        $praticien = filter_var($_POST['PRA_NUM'], FILTER_SANITIZE_NUMBER_INT);
    } else {
        echo("<a href='#' onClick='history.go(-1)'>Retour en arriere</a><br>");
        die("Numéro du praticien Non Valide");
    }
    
    // verify if it's an int first, and if yes, sanitize to convert into valid INT 
    if(filter_var($_POST['PRA_COEFF'], FILTER_VALIDATE_INT) == true){
        $coefficient = filter_var($_POST['PRA_COEFF'], FILTER_SANITIZE_NUMBER_INT);
    } else {
        echo("<a href='#' onClick='history.go(-1)'>Retour en arriere</a><br>");
        die("Coefficient Non Valide");
    }
    
    if(isset($_POST['PRA_REMPLACANT']) == true && !empty($_POST['PRA_REMPLACANT']) == true){
        $remplacant = htmlspecialchars($_POST['PRA_REMPLACANT']);
    } else {
        $remplacant = "Pas de Remplacant";
    }

    // Conversion en format date sql (US)
    //$date = converttodate($_POST['RAP_DATE']);

    // motif
    if($_POST["RAP_MOTIF"] == "AUT"){
        $motif = htmlspecialchars("Autre: " + $_POST["RAP_MOTIFAUTRE"]);
    } else {
        switch(htmlspecialchars($_POST['RAP_MOTIF'])){
            case "PRD":
                $motif = "Périodicité";
                break;
            case "ACT":
                $motif = "Actualisation";
                break;
            case "REL":
                $motif = "Relance";
                break;
            case "SOL":
                $motif = "Solicitation Praticien";
                break;            
        }
    }
    
    $bilan = empty($_POST['RAP_BILAN']) == true ? die("Bilan obligatoire") : htmlspecialchars($_POST['RAP_BILAN']); // sanitize bilan

    // 2e partie
    $produit1 = htmlspecialchars($_POST['PROD1']);
    $produit2 = htmlspecialchars($_POST['PROD2']);
    $documentation = isset($_POST['RAP_DOC']) == true ? "1" : "0";

    // 3e partie
    if(filter_var($_POST['nbechantillon'], FILTER_VALIDATE_INT)){
        $nbechantillon = $_POST['nbechantillon'];
    } else {
        $nbechantillon = 0;
    } 


    /////////////////////////////////////////////////////////////////////////////////////////////////////
    $prodarray = [];
    // insert each samples into an array
    for($i = 1; $i <= $nbechantillon; $i++){
        array_push($prodarray, $_POST["PRA_ECH{$i}"]);
        array_push($prodarray, $_POST["PRA_QTE{$i}"]);
    } 
    // Example: ["AXOMIL", 2, "TRIMYCINE", 1]
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    


    $saisiedef = isset($_POST['RAP_LOCK']) ? "1" : "0";

    ob_start();
?>  
    <div id="table-recap-div">
    <h1>Récapitulatif de votre rapport</h1>
        <table class="table-recap">
            <th>Donnée</th><th>Entrée Utilisateur</th>
            <tr>
                <td>Numéro</td>
                <td><?= $rapnum ?></td>
            </tr>
            <tr>
                <td>Date de visite</td>
                <td><?= $datevisite ?></td>
            </tr>
            <tr> 
                <td>Praticien</td>
                <td><?= $praticien ?></td>
            </tr>
            <tr>
                <td>Coefficient</td>
                <td><?= $coefficient ?></td>
            </tr>
            <tr>
                <td>Remplacant</td>
                <td><?= $remplacant ?></td>
            </tr>
            <!--

            <tr>
                <td>Date</td>
                <td><?= $date ?></td>
            </tr>

            -->
            <tr>
                <td>Motif</td>
                <td><?= $motif ?></td>
            </tr>
            <tr>
                <td>Bilan</td>
                <td><?= $bilan ?></td>
            </tr>
            <tr>
                <td>Produit N°1</td>
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
                $rapnumeroechantillon = 1;
                for($i=0; $i < sizeof($prodarray); $i+=2){
                    $echantillon = empty($prodarray[$i]) ? "Pas d'échantillon proposé" : $prodarray[$i];
                    $qte = intval($prodarray[$i+1]);

                    echo("<tr>");
                    echo("<td>Echantillon N°". intval($rapnumeroechantillon) ."</td><td>{$echantillon}, Qté: {$qte}</td>");
                    echo("</td>");

                    $rapnumeroechantillon+=1;
                }
            ?>
            <tr>
                <td>Saisie Definitif</td>
                <td><?= $saisiedef ?></td>
            </tr>
        </table>
        <span style="color: black">Votre rapport a bien été enregistré.</span>
        <button onclick="history.go(-1)">Retour</button>
    </div>

<?php

    /*
    
        Partie Insertion des données ici 

        Les variables PHP ont déjà été vérifié et définie auparavant, il ne reste plus qu'a les insérer dans la base de donnée, dans la table rapportvisite et offrir
        ATTENTION A PREPARER LES REQUETES POUR EVITER LES SQL INJECTIONS (mesure de sécurité en plus)
        AVEC LA FONCTION prepare et execute:
        Exemple:
            $sql = "SELECT * FROM TABLE WHERE userId=:param1 AND :param2" || "SELECT * FROM TABLE WHERE userId= ? AND pass= ?";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(array(":param1" => $variable1, ":param2" => $variable2)); ou $stlt->execute([$variable1, $variable2]);
        
    */

    // query to insert database
    $connexion->beginTransaction();

        // Insert Rapport
        $sql = "INSERT INTO rapportvisite(visMatricule, rapNum, praNum, rapDate, rapBilan, rapMotif, saisiedef, docfourni, prod1, prod2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$_SESSION["userId"], $rapnum, $praticien, $datevisite, $bilan, $motif, $saisiedef, $documentation, $produit1, $produit2]);

        $rapid = $connexion->lastInsertId();

        /* It's inserting the samples into the database */
        $prodsql = "INSERT INTO offrir(visMatricule, rapNum, medDepotlegal, offQte) VALUES(?, ?, ?, ?);";
        $echantillonnumeroqte = 0;
        for($i = 0; $i < sizeof($prodarray); $i+=2){
            $stmt = $connexion->prepare($prodsql);
            $stmt->execute([$_SESSION["userId"], $rapid, $prodarray[$i], $prodarray[$i+1]]);
        }

    $connexion->commit();
    
    // Render default page
    $title="GSB - Status du Rapport";
    $content = ob_get_clean();
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");

    
?>