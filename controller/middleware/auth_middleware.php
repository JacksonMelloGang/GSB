<?php

    function check_if_allowed($permission_needed){
        session_start();
        $allowed = false;

        if(!isset($_SESSION['authorization'])){
            header("Location: login.php?&error=norank&page=". $_SERVER["PHP_SELF"]);
            exit;
        }

        // $permission_needed can be an array or a string so we have to check if it's an array or a string

        // check if it's an array
        if(is_array($permission_needed)){
            $i = 0;
            while($i < sizeof($permission_needed) || $allowed == false){
                if($_SESSION['authorization'] == $permission_needed[$i]){
                    $allowed = true;   
                }
                $i++;
            }
        } 

        // check if $permission_needed is a string otherwise 'error'
        if(is_string($permission_needed)){
            if($_SESSION['authorization'] == $permission_needed){
                $allowed = true;
            }
        } else {
            die("value permission_needed($permission_needed) is not a string or an array !");
        }

        // if not allowed redirect to login.php otherwise do nothing 
        if($allowed == false){
            header("Location: /login.php?&error=notallowed&page=". substr($_SERVER["PHP_SELF"], 1) . "&rank=". $_SESSION['authorization']);
        }
    }


    // Function to check if user is into the database or not
    function check($username, $password) {
        require("./db/DbConnexion.php");

        // if username or password is empty return false and error message
        if(empty($username) || empty($password)){
            return array(false, "Username or Password is required !");
        }

        // otherwise executer request to check if user is in the database
        $query = $connexion->prepare("SELECT * FROM visiteur WHERE visNom = ? AND visDateembauche = ?");
        $query->execute([$username, $password]);
        $result = $query->fetch();
        
        // if result empty / false return false & error message
        if($result === false){
            return array(false, "Invalid Username / Password");
        }

        // return true as user passed all conditions
        return array(true, "Success");
    }
    