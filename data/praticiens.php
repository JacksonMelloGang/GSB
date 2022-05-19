<?php

// Restricted Access
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/auth_middleware.php");
check_if_allowed('USER'); // Rank Needed

// require sql connection
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/DbConnexion.php");

require_once($_SERVER["DOCUMENT_ROOT"]. "/models/praticiens_model.php");


// cancel if 'selectionned' doesn't exist
if (!isset($_POST['selectioned'])) {
    die("Value 'selectioned' is not set.");
}

$action = $_POST['selectioned'];

// if 2nd args is not set
if(!isset($_POST['selectedinfotype'])){
    
    switch ($action) {
        case 0:
            echo("<script>document.getElementsByClassName('table')[0].innerHTML = " . showAllPraticienstable($connexion) . "</script>");
            echo("<option selected=''>Séléctionner une option</option>");
            break;
        case 1:
            $sql = "SELECT praVille FROM praticien GROUP BY praVille";
            $result = $connexion->query($sql);
            $ligne = $result->fetch();

            while ($ligne != false) {
                echo("<option value='{$ligne['praVille']}'>{$ligne['praVille']}</option>");
                $ligne = $result->fetch();
            }
            break;

        case 2:
            $sql = "SELECT praCp, praVille FROM praticien GROUP BY praCP, praVille";
            $result = $connexion->query($sql);
            $ligne = $result->fetch();

            while ($ligne != false) {
                echo("<option value='{$ligne['praCp']}'>{$ligne['praCp']}-{$ligne['praVille']}</option>");
                $ligne = $result->fetch();

            }
            break;


        case 3:
            $sql = "SELECT typCode FROM praticien GROUP BY typCode";
            $result = $connexion->query($sql);
            $ligne = $result->fetch();

            while ($ligne != false) {
                echo("<option value='{$ligne['typCode']}'>{$ligne['typCode']}</option>");
                $ligne = $result->fetch();

            }
            break;

        default:
            $content = "AIE AIE AIE";
            return $content;

    }
}

if (isset($_POST['selectedinfotype'])) {

    // action & infotype
    $infotype = $_POST['selectedinfotype'];

    $sql = "SELECT * FROM praticien WHERE ";

    if($action == 1){
        $firstpart = "praVille = '$infotype'";
        $sql = $sql . $firstpart;
    } else {
        if($action == 2){
            $firstpart = "praCp = '$infotype'";
            $sql = $sql . $firstpart;
        } else {
            if($action == 3){
                $firstpart = "typCode = '$infotype'";
                $sql = $sql . $firstpart;
            }
        }
    }

    $stmt = $connexion->query($sql);
    $result = $stmt->fetch();
    
    if($result === false){
        die("Pas de résultat trouvé.");
    }

    echo("<th>N° Praticien</th><th>Nom</th><th>Prenom</th><th>Adresse</th><th>Code Postal</th><th>Ville</th><th>Reputation</th><th>Type</th>");
    while ($result != false) {
        echo("<tr>");
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $columndata = empty($ligne[$i]) == true ? "<b>NR</b>" : $result[$i]; // check if data is not 'null' --> Variable Conditonnel
                $columndata = str_replace(array(" r ", " av ", " pl ", " bd ", "résid"), array(" Rue ", " Avenue ", " Place ", " Boulevard ", " Résidence "), $result[$i]);
                echo("<td class='table-item'><a href='?action=showprac&pratid=$result[0]'>" . $columndata . "</td>");
            }
        echo("</tr>");

        $result = $stmt->fetch();
    }

}
