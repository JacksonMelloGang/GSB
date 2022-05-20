<?php

    function getMedicamentById($connexion, $medicId){
        // Prepare Request to avoid SQL Injection
        $stmt = $connexion->prepare("SELECT medDepotlegal, medNomcommercial, famLibelle, medComposition, medEffets, medContreindic, medPrixechantillon FROM medicament, famille WHERE medicament.famCode = famille.famCode AND medDepotLegal = :medic ");
        $stmt->execute(array(':medic' => $medicId));

        // Fetch data, supposed to have one row 
        $result = $stmt->fetch();

        //if result empty / false
        if($result === false){
            return false;
        }

        return $result;
    }

    function getMedicamentsOptions($connexion){
        ob_start();
        $query = $connexion->query("SELECT medNomCommercial, medDepotLegal FROM medicament");
        $result = $query->fetch();

        while($result != false){
            echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
            $result = $query->fetch();
        }

        return ob_get_clean();
    }

    function getSelectedMedicamentsAsOption($connexion, $medicname){
        ob_start();
        $query = $connexion->query("SELECT medNomCommercial, medDepotLegal FROM medicament");
        $result = $query->fetch();

        while($result != false){
            if($result['medNomCommercial'] == $medicname){
                echo("<option value='{$result['medDepotLegal']}' selected>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
            } else {
                echo("<option value='{$result['medDepotLegal']}'>{$result['medNomCommercial']} - {$result['medDepotLegal']}</option>");
            }

            $result = $query->fetch();
        }

        return ob_get_clean();
    }



    function getMostProposedSamples($connexion){

        $sql = "SELECT SUM(offQte), medicament.medNomcommercial FROM offrir, medicament WHERE offrir.medDepotlegal = medicament.medDepotlegal GROUP BY offrir.medDepotlegal ORDER BY SUM(offQte) DESC LIMIT 4";
        $stmt = $connexion->query($sql);
        $result = $stmt->fetchAll();

        return $result;
    }


    function showAllMedicamentsTable($connexion){
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
        return $content;
    }