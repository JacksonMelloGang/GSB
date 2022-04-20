<?php
    if(session_status() == PHP_SESSION_NONE){
        session_start();
    }

    // if not set {authorization}, return to login.php
    if(!isset($_SESSION['authorization'])){
        header('Location: login.php');
    }

    $userrank = $_SESSION['authorization'];

    switch($userrank){
        case 'USER':
            require("./views/layout/sidebar/sidebar-user.html");
            break;
        case 'RESPONSABLE':
            break; 
        default:
            header('Location: login.php'); 
    }

