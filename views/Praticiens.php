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
                                    $columndata = str_replace(array(" r ", " av ", " pl ", " bd ", "résid"), array(" Rue ", " Avenue ", " Place ", " Boulevard ", " Résidence "), $ligne[$i]);
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
        case "showprac": // show info about a prat
            if(isset($_GET['pratid'])) {
                // Prepare Request to avoid SQL Injection
                $stmt = $connexion->prepare("SELECT praNum, praNom, praPrenom, praAdresse, praCp, praCoefNotoriete, typLibelle FROM praticien, typepraticien WHERE praticien.typCode = typepraticien.typCode AND praNum = :pranum");
                $stmt->execute(array(':pranum' => $_GET['pratid']));

                // Fetch data, supposed to have one row 
                $result = $stmt->fetch();
                
                $praNumero = $result['praNum'];
                $praNom = $result['praNom'];
                $praPrenom = $result['praPrenom'];
                $praAdresse = $result['praAdresse'];
                $praAdresse = str_replace(array(" r ", " av ", " pl "), array(" Rue ", " Avenue ", " Place "), $praAdresse);
                $praCp = $result['praCp'];
                $praCoefNotoriete = $result['praCoefNotoriete'];
                $pratypCode = $result['typLibelle'];

                // if result from query is not empty, then start tempo & create table filled with data from the result set
                // else, display, "no result"
                ob_start(); // start temp

                if ($stmt->rowCount() !== 0) {
                    echo("<h1>Information à propos du praticien</h1>");
                        echo("<table style='text-align: center;' id=''>");
                                echo("<tr><td>Numéro</td><td>{$praNumero}</td></tr>");
                                echo("<tr><td>Nom</td><td>{$praNom}</td></tr>");
                                echo("<tr><td>Prenom</td><td>{$praPrenom}</td></tr>");
                                echo("<tr><td>Adresse</td><td>{$praAdresse}</td></tr>");
                                echo("<tr><td>Code Postal</td><td>{$praCp}</td></tr>");
                                echo("<tr><td>Coef Notoriete</td><td>{$praCoefNotoriete}</td></tr>");
                                echo("<tr><td>Type Praticien</td><td>{$pratypCode}</td></tr>");
                        echo ("</table>");
                    echo("<br>");

                    /* == 2nd Part, Rapports == */
                    // Prepare Request to avoid SQL Injection
                    $rapport_stmt = $connexion->prepare("SELECT id, rapDate FROM praticien, rapportvisite WHERE praticien.praNum = rapportvisite.praNum AND rapportvisite.praNum = :pranum");
                    $rapport_stmt->execute(array(':pranum' => $_GET['pratid']));
                    $rapport_result = $rapport_stmt->fetch();
                    
                    echo("<h2>Rapports le concernant</h2>");
                        if($rapport_stmt->rowCount() !== 0){
                            echo("<div>");
                            while($rapport_result != false){
                                for($i=0; $i < $rapport_stmt->rowCount(); $i++){
                                    echo("&nbsp;&nbsp;&nbsp;&nbsp;<a href='/views/Rapports.php?action=consult&rapid={$rapport_result['id']}'>Rapport N°{$rapport_result['id']} Datant du {$rapport_result['rapDate']}</a>");
                                }
                                
                                $rapport_result = $rapport_stmt->fetch();
                            }        

                            echo("</div>");
                        } else {
                            echo("&nbsp;&nbsp;&nbsp;&nbsp;Aucun Rapport le concernant");
                        }
                    } else {
                    // if no result
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