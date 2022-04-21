<?php
    // require sql connection
    require("./db/DbConnexion.php");

    // user-input, define number of elements in page
    $nbr_elements_par_page= isset($_GET["nbpage"]) ? $_GET["nbpage"] : 7;

    //=============================================// 
        // if query param page is set and is an int, then set $page to $_get["page"] otherwise set to 1 (to avoid warning) 
        $page= isset($_GET["page"]) && intval($_GET["page"]) ? $_GET["page"] : 1; 

        // get number of element in table
        $count = $connexion->query("SELECT count(medDepotlegal) AS cpt FROM medicament");
        $tcount = $count->fetchAll();

        $nbr_de_pages=ceil($tcount[0]["cpt"]/$nbr_elements_par_page+1);
        $debut=($page-1)*$nbr_elements_par_page;
    //=============================================//


    // Get Data from medicament table
    $sql = "SELECT *  FROM medicament LIMIT $debut, $nbr_elements_par_page";
    $result = $connexion->query($sql);
    $ligne = $result->fetch();
    ob_start();
?>  

    <table>
        <?php

            while($ligne != false){
                echo("<tr>");
                    for($i=0; $i < $result->columnCount(); $i++){
                        echo("<td>$ligne[$i]</td>");
                    }
                echo("</tr>");
                $ligne = $result->fetch();
            }

        ?>
    </table>

    <div id="pagination">
        <?php
            for($i=1;$i<=$nbr_de_pages-1;$i++){
                if($page!=$i)
                    echo "<a href='?page=$i'>$i</a>&nbsp;";
                else
                    echo"<a>$i</a>&nbsp";
            }
        ?>

    </div>
    
<?php
    $title = "GSB - Medicaments";
    $content = ob_get_clean();
    require("./views/layout/layout.php");