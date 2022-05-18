<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // require medicaments model
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/medicaments_model.php");

    $action = "";
    if(isset($_GET["action"])){
        $action= $_GET["action"];
    }

    switch ($action) {
        case "showmedic":
            if(isset($_GET['medic'])) {
                // Prepare Request to avoid SQL Injection
                $stmt = $connexion->prepare("SELECT medDepotlegal, medNomcommercial, famLibelle, medComposition, medEffets, medContreindic, medPrixechantillon FROM medicament, famille WHERE medicament.famCode = famille.famCode AND medDepotLegal = :medic ");
                $stmt->execute(array(':medic' => $_GET['medic']));

                // Fetch data, supposed to have one row 
                $result = $stmt->fetch();

                // if result from query is not empty, then start tempo & create table filled with data from the result set
                // else, display, "no result"
                ob_start(); // start temp

                if ($stmt->rowCount() !== 0) {
                    $depotlegal = $result["medDepotlegal"];
                    $nom = $result["medNomcommercial"];
                    $code = $result["famLibelle"];
                    $compo = $result["medComposition"];
                    $effets = $result["medEffets"];
                    $contreindic = $result["medContreindic"];
                    $prix = empty($result[""]) == true ? "<b>Non Renseigné.</b>" : $result[$i];
                ?>
                    <div style='display: flex; justify-content: center'>
                        <table id='table-info'>
                               
                                <tr class='table-info-item'>
                                    <td>Depot Légal</td><td><?= $depotlegal ?></td>
                                </tr>

                                <tr class='table-info-item'>
                                    <td>Nom Commercial</td><td><?= $nom ?></td>
                                </tr>

                                <tr class='table-info-item'>
                                    <td>Code</td><td><?= $code ?></td>
                                </tr>

                                <tr class='table-info-item'>
                                    <td>Effets</td><td><?= $effets ?></td>
                                </tr>

                                <tr class='table-info-item'>
                                    <td>Contre Indication</td><td><?= $contreindic ?></td>
                                </tr>

                                <tr class='table-info-item'>
                                    <td>Prix</td><td><?= $prix ?></td>
                                </tr>

                        </table>
                    </div>
                <?php
                } else {
                    // if no medic id (code legal)
                    echo("Aucun résultat ne correspond à votre recherche.");
                }

                // Define values for layout.php
                $title= "GSB - Medicament " . $_GET['medic'];
                $content = ob_get_clean();
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
            break;
        default:
            // Render default page
            $title="GSB - Liste des Medicaments";
            ob_start();

        // user-input, define number of elements in page
        $nbr_elements_par_page= isset($_GET["nbpage"]) ? intval($_GET["nbpage"]) : 10;
        
        //================= PAGINATION =================// 
            // if query param page is set and is an int, then set $page to $_get["page"] otherwise set to 1 (to avoid warning) 
            $page= isset($_GET["page"]) && intval($_GET["page"]) ? $_GET["page"] : 1; 

            // get number of element in table
            $count = $connexion->query("SELECT count(medDepotlegal) AS cpt FROM medicament");
            $tcount = $count->fetchAll();

            $nbr_de_pages=ceil($tcount[0]["cpt"]/$nbr_elements_par_page+1);
            $debut=($page-1)*$nbr_elements_par_page;
        //=============================================//


        // Get Data from medicament table
        $sql = "SELECT *  FROM medicament ORDER BY medNomcommercial ASC LIMIT $debut, $nbr_elements_par_page ";
        $result = $connexion->query($sql);
        $ligne = $result->fetch();
        ob_start();
    ?>  
    
            <span style='color: black; font-size: 32px; text-align: center;'>Listes des médicaments</span>
            
            <table class='table'>
               <th>Depot Legal</th><th>Nom</th><th>Famille</th><th>Composition</th><th>Effets</th><th>Contre Indication</th><th>Prix</th>
                <?php
                        //display result from query
                        while($ligne != false){
                            echo("<tr>");
                                for($i=0; $i < $result->columnCount(); $i++){
                                    $columndata = empty($ligne[$i]) == true ? "<b>NR</b>" : $ligne[$i]; // check if data is not 'null' --> Variable Conditonnel
                                    echo("<td class='table-item'><a href='?action=showmedic&medic=$ligne[0]'>$columndata</td>");
                                }
                            echo("</tr>");
                            $ligne = $result->fetch();
                        }

                    ?>
            </table>

        <div id="pagination">
            <?php
                // select page
                for($i=1;$i<=$nbr_de_pages-1;$i++){
                    if($page!=$i)
                        echo "<a href='?page=$i&nbpage=$nbr_elements_par_page'>$i</a>&nbsp;";
                    else
                        echo"<a>$i</a>&nbsp";
                }
            ?>
        </div>

    <?php
            $content = ob_get_clean();
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }


