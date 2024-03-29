<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed(array('USER')); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // require visiteur model
    require_once($_SERVER['DOCUMENT_ROOT'] . "/models/visiteurs_model.php");

    $userId = $_SESSION["userId"];

    $userInfo = getVisiteurInfoById($connexion, $userId);
    
    $matricule = $userInfo["visMatricule"];
    $nom = $userInfo["visNom"];
    $prenom = $userInfo["visPrenom"];
    $adresse = $userInfo["visAdresse"];
    $ville = "{$userInfo["visCp"]} - {$userInfo["visVille"]}";
    $laboratoire = $userInfo["labNom"];

    ob_start();
?>

    <div id="userinfo">
            <form id="mdp">
                <table style="width:80%">
                    <tr>
                        <td>Matricule</td><td><input type="text" value=<?php echo("\"{$matricule} \"") ?> disabled></td>
                    </tr>
                    <tr>
                        <td>Nom</td><td><input type="text" value=<?php echo("\"{$nom} \"") ?> disabled></td>
                    </tr>
                    <tr>
                        <td>Prenom</td><td><input type="text" value=<?php echo("\"{$prenom} \"") ?> disabled></td>
                    </tr>
                    <tr>
                        <td>Adresse</td><td><input type="text" value=<?php echo("\"{$adresse} \"") ?> disabled></td>
                    </tr>
                    <tr>
                        <td>Ville</td><td><input type="text" value=<?php echo("\"{$ville} \"") ?> disabled></td>
                    </tr>
                    <tr>
                        <td>Laboratoire</td><td><input type="text" value=<?php echo("\"{$laboratoire} \"") ?> disabled></td>
                    </tr>
                    <tr>
                        <td>Nouveau Mot de passe: </td>
                        <td>
                            <input id="mdp" type="text" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirmer votre mot de passe: </td>
                        <td>
                            <input id="cfmmdp" type="text" disabled>
                        </td>
                    </tr>
                    <tr><td></td><td>
                        <input type="submit" style="text-align: center" value="Enregistrer">
                    </td></tr>
                </table>

            </form>

            <script>
                var mdp = $("#mdp").val();
                var cfmmdp = $("#cfmmdp").val();
                
                

            </script>


    </div>

<?php

    $content = ob_get_clean();
    $title = "GSB - Paramètres";
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
