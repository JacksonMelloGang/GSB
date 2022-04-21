<?php

    function showtable($connexion)
    {
        $query = $connexion->query("SELECT * FROM medicament");
        $result = $query->fetch();
        ob_start();
        echo "<span style='color: black; font-size: 32px; text-align: center;'>Listes des médicaments</span>";
        echo ("<table class='medicament-table' style='overflow: scroll; height: 10px;'>");
        while ($result) {
            echo ("<tr class='medic-item'>");
            echo ("<td><a href='" . $_SERVER['PHP_SELF'] . "?&action=showmedic&medic=$result[0]'>$result[0]</a></td>");
            echo ("<td>$result[1]</td>");
            echo ("<td>$result[2]</td>");
            echo ("<td>$result[3]</td>");
            echo ("<td>$result[4]</td>");
            echo ("<td>$result[5]</td>");
            echo ("<td>$result[6]</td>");
            echo ("</tr>");
            $result = $query->fetch();
        }
        echo ("</table>");
        return ob_get_clean();
    }


    require_once("../db/DbConnexion.php");

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        switch ($action) {
            case "showmedic":
                if (isset($_GET['medic'])) {
                    // Prepare Request to avoid SQL Injection
                    $stmt = $connexion->prepare("SELECT * FROM medicament WHERE medDepotLegal = :medic");
                    $stmt->execute(array(':medic' => $_GET['medic']));

                    // Fetch data, supposed to have one row 
                    $result = $stmt->fetch();

                    // if result empty
                    if ($stmt->rowCount() === 0) {
                        echo ("Aucun résultat ne correspond");
                        return;
                    }

                    // Start Temp
                    ob_start();
                    echo ("<table>");
                    for ($i = 0; $i < $stmt->columnCount(); $i++) {
                        $columndata = $result[$i] ? $result[$i] : "Non définie dans la base de donnée."; // check if data is not null --> Variable Conditonnel


                        $columnname = substr($stmt->getColumnMeta($i)['name'], 3);

                        echo ("<tr>");
                        echo ("<td>$columnname</td>");
                        echo ("<td>$columndata</td>");
                        echo ("</tr>");
                    }
                    echo ("</table>");

                    // Define values for layout.php
                    $title= "GSB - Medicament " . $_GET['medic'];
                    $content = ob_get_clean();
                    require("./views/layout/layout.php");

                    return;
                }
                break;
        }
    }

    // Render default page
    $title="GSB - Liste des Medicaments";
    $content = showtable($connexion);
    require("layout.php");

?>
