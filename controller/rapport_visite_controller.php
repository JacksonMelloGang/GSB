<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // Render default page
    $title="GSB - Traitement En cours...";
    $content = "aabb";
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");

    

?>