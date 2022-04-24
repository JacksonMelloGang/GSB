<?php

    // Restricted Access
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/auth_middleware.php");
    check_if_allowed('USER'); // Rank Needed

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");


    function showtable($connexion){
        // user-input, define number of elements in page
        $nbr_elements_par_page= isset($_GET["nbpage"]) ? intval($_GET["nbpage"]) : 10;
        
        //================= PAGINATION =================// 
            // if query param page is set and is an int, then set $page to $_get["page"] otherwise set to 1 (to avoid warning) 
            $page= isset($_GET["page"]) && intval($_GET["page"]) ? $_GET["page"] : 1; 

            // get number of element in table
            $count = $connexion->query("SELECT count(praNum) AS cpt FROM praticien");
            $tcount = $count->fetchAll();

            $nbr_de_pages=ceil($tcount[0]["cpt"]/$nbr_elements_par_page+1);
            $debut=($page-1)*$nbr_elements_par_page;
        //=============================================//


        // Get Data from medicament table
        $sql = "SELECT *  FROM praticien LIMIT $debut, $nbr_elements_par_page";
        $result = $connexion->query($sql);
        $ligne = $result->fetch();
        ob_start();
    ?>  
        <div class="table">
    
            <span style='color: black; font-size: 32px; text-align: center;'>Listes des Praticiens</span>
            
            <table class='table'>
               <th>N° Praticien</th><th>Nom</th><th>Prenom</th><th>Adresse</th><th>Code Postal</th><th>Ville</th><th>Reputation</th><th>Type</th>
                <?php
                        //display result from query
                        while($ligne != false){
                            echo("<tr>");
                                for($i=0; $i < $result->columnCount(); $i++){
                                    $columndata = empty($ligne[$i]) == true ? "<b>NR</b>" : $ligne[$i]; // check if data is not 'null' --> Variable Conditonnel
                                    $adresse = $ligne['praAdresse'];
                                    
                                    // get addresse & replace 'r' or 'pl' with 'Rue' or  'Place' 
                                    if($result->getColumnMeta(3)['name'] == 'praAdresse'){
                                        
                                        $lettertoreplace = explode(' ', $ligne['praAdresse']);    
                                        switch($lettertoreplace[1]){
                                            case "r":
                                                $columndata = str_replace(' r ', ' Rue ', $columndata);
                                                break;
                                            case "pl":
                                                break;
                                            case "av":
                                                break;
                                        }
                                    }


                                    echo("<td class='table-item'><a href='?action=showprac&pratid=$ligne[0]'>". $columndata ."</td>");
                                }
                            echo("</tr>");
                            $ligne = $result->fetch();
                        }

                    ?>
            </table>
        </div>

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
        return ob_get_clean();
    }

    $action = "";
    if(isset($_GET["action"])){
        $action= $_GET["action"];
    }

    switch ($action) {
        case "showprac":
            if(isset($_GET['pratid'])) {
                // Prepare Request to avoid SQL Injection
                $stmt = $connexion->prepare("SELECT * FROM praticien WHERE praNum = :pranum");
                $stmt->execute(array(':pranum' => $_GET['pratid']));

                // Fetch data, supposed to have one row 
                $result = $stmt->fetch();
                $praNom = $result['praNom'];

                // if result from query is not empty, then start tempo & create table filled with data from the result set
                // else, display, "no result"
                ob_start(); // start temp
                if ($stmt->rowCount() !== 0) {
                    echo("<div style='display: flex; justify-content: center'>");
                        echo("<table id='table-info'>");
                            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                                $columndata = empty($result[$i]) == true ? "<b>Non définie dans la base de donnée.</b>" : $result[$i]; // check if data is not null --> Variable Conditonnel
                                $columnname = substr($stmt->getColumnMeta($i)['name'], 3);

                                echo ("<tr class='table-info-item'>");
                                echo ("<td>$columnname</td>");
                                echo ("<td>$columndata</td>");
                                echo ("</tr>");
                            }
                        echo ("</table>");
                    echo("</div");
                } else {
                    // if no medic id (code legal)
                    echo("Aucun résultat ne correspond à votre recherche.");
                }

                // Define values for layout.php
                $title= "GSB - Praticien " . $praNom;
                $content = ob_get_clean();
                require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
            }
            break;
        default:
            // Render default page
            $title="GSB - Liste des Praticiens";
            $content = showtable($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }