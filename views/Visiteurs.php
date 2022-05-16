<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('RESP'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // Render default page
    $title="GSB - Liste des Visiteurs";
    $content = "ab";
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");

