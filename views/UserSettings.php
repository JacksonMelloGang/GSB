<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed(array('USER')); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    session_start();
    $userId = $_SESSION["userId"];

    $query = $connexion->prepare("SELECT * FROM visiteur WHERE visMatricule=?");
    $query->execute([$userId]);

    $result = $query->fetch();
    

    ob_start();
?>

    <div style="display:flex;justify-content:center;align-content:center">

        <table>
            <tr>
                <td></td>
                <td></td>
            </tr>
        </table>

    </div>

<?php

    $content = ob_get_clean();
    $title = "GSB - Paramètres";
    require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");

    