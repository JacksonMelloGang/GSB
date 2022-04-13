<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicament</title>
    <link href="./css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <?php
        require_once("./db/DbConnexion.php");

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
                            echo ("OOF !");
                            return;
                        }

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

                        $content = ob_end_flush();
                        require("./layout/layout.php");

                        return;
                    }
                break;
            }
        }
        $content = showtable($connexion);
        require("./layout/layout.php");



        function showtable($connexion)
        {
            $query = $connexion->query("SELECT * FROM medicament");
            $result = $query->fetch();
            
            echo ("<table class='medicament-table'>");
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
        }




    ?>
</body>

</html>