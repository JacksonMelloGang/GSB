<?php

    /**
     * It checks if the user is logged in and if the user has the right permission to access the page
     * 
     * @param string|array permission_needed this is the permission that the user needs to have to access the page.
     */
    function check_if_allowed($permission_needed){
        session_start();
        $allowed = false;

        if(!isset($_SESSION['authorization'])){
            header("Location: /index.php?&error=norank&page=". $_SERVER["PHP_SELF"]);
            exit;
        }

        // check if $permission_needed is a string otherwise 'error'
        if(is_string($permission_needed)){
            if($_SESSION['authorization'] == $permission_needed){
                $allowed = true;
            }
        } else {
            // check if it's an array
            if(is_array($permission_needed)){
                $i = 0;
                while($i < sizeof($permission_needed) && $allowed == false){
                    if($_SESSION['authorization'] == $permission_needed[$i]){
                        $allowed = true;   
                    }
                    $i++;
                }
            } else {
                die("value permission_needed($permission_needed) is not a string or an array !");
            }
        }

        // if not allowed redirect to login.php otherwise do nothing 
        if($allowed == false){
            header("Location: /index.php?&error=notallowed&page=". substr($_SERVER["PHP_SELF"], 1) . "&rank=". $_SESSION['authorization']);
        }
    }


    /**
     * > The function checks if the username and password are empty, if not it executes a request to
     * check if the user is in the database, if the result is empty / false it returns an error
     * message, otherwise it returns true
     * 
     * @param string username The username of the user trying to log in.
     * @param string password The password to check.
     * @param PDO connexion The PDO object that is used to connect to the database.
     * 
     * @return array array with a boolean and a string.
     */
    function check($username, $password, $connexion) {

        // if username or password is empty return false and error message
        if(empty($username) || empty($password)){
            return array(false, "Username or Password is required !");
        }
        
        // otherwise executer request to check if user is in the database
        $query = $connexion->prepare("SELECT motdepasse FROM utilisateur WHERE nom = ?");
        $query->execute([$username]);
        $result = $query->fetch();
        
        $mdpDb = isset($result["motdepasse"]) ? $result["motdepasse"] : "wrong";

        $checkup_password = password_verify($password, $mdpDb);

        // if result empty / false return false & error message
        if($result === false || $checkup_password == false){
            return array(false, "Invalid Username / Password");
        }      

        // return true as user passed all conditions
        return array(true, "Success");
    }
    

    /**
     * It takes a username and password, and returns an array with a boolean and a message
     * 
     * @param string username The username of the user
     * @param PDO connexion the PDO object
     * 
     * @return array array with two values. The first value is a boolean, which is true if the user is
     * authenticated, and false if the user is not authenticated. The second value is a string, which
     * is null if the user is authenticated, and contains an error message if the user is not
     * authenticated.
     */
    function setUserId($username, $connexion){

        $query = $connexion->prepare("SELECT visMatricule FROM visiteur WHERE visNom = ?");
        $query->execute([$username]);
        $result = $query->fetch();
        
        // close sql connexion
        $connexion = null;

        // if result empty / false return false & error message
        if($result === false){
            return array(false, "Invalid Username / Password");
        }

        $_SESSION["userId"] = $result['visMatricule'];

        return array(true, null);

    }

    /**
     * It returns the rank of a user, given his userId
     * 
     * @param string userId The user's ID
     * @param PDO connexion the connection to the database
     * 
     * @return string The rank of the user.
     */
    function getrankbyuserid($userId, $connexion){
        
        $query = $connexion->query("SELECT rang FROM utilisateur WHERE visMatricule = '$userId'");
        $result = $query->fetch();

        if($result === false){
            return "USER";
        }

        return $result["rang"];
    }



    /**
     * It takes a user id, a message and a database connection as parameters, and returns the user's
     * email address
     * 
     * @param string userId The user's ID
     * @param string message The message you want to send to the user.
     * @param PDO connexion The PDO object
     */
    function mailuser($userId, $message, $connexion){
        $query = $connexion->query("SELECT mail FROM ustilisateur WHERE visMatricule = '$userId'");
        $result = $query->fetch();
        
        if($result === false){
            return false;
        }
    }