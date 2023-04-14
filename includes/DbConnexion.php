<?php

    require($_SERVER["DOCUMENT_ROOT"]. "/config/configDb.php");

    try {
        $connexion = new PDO("mysql:dbname=".DB_NAME.";host=" .DB_HOST.";port=" .DB_PORT, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch( Exception $e ){
        die("Exception: " . $e->getMessage());
    }

?>