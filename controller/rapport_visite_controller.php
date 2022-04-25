<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // sanitize user-inputs to avoid sql injection or exploits with filter()
    /*
        numéro: RAP_NUM (text)
        date visite: RAP_DATEVISITE (date)
        praticien: PRA_NUM (text)
        coefficient: PRA_COEFF (text)
        remplacant: PRA_REMPLACANT (checkbox, select)
        date: RAP_DATE (date)
        motif: RAP_MOTIF (select -> PRD, ACT, REL, SOL, AUT)
        bilan: RAP_BILAN (textarea)
        produit 1: PROD1 (select)
        produit 2: PROD2 (select)
        documentation offerte: RAP_DOC (checkbox)
        echantillon: PRA_ECH1, PRA_QTE1 (select,, input)
        saisie definitive: RAP_LOCK (checkbox)
    */

    // Render default page
    $title="GSB - Traitement En cours...";
    $content = "aabb";
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");

    

?>