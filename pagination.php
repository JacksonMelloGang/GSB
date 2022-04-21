<?php
    require("./db/DbConnexion.php");
    $page= isset($_GET["nbpage"]) ? $_GET["nbpage"] : 5;
    $nbr_elements_par_page= 5;
    $result = $connexion->query("SELECT count(medDepotlegal) AS cpt FROM medicament");
    $tcount = $result->fetchAll();
    $nbr_de_pages=ceil($tcount[0]["cpt"]/$nbr_elements_par_page);
    $debut=($page-1)*$nbr_elements_par_page;
    
    $connexion->query("SELECT *  FROM medicament")
?>  

    <div id="pagination">
        <?php
            for($i=1;$i<=$nbr_de_pages;$i++){
                if($page!=$i)
                    echo "<a href='page=$i'>$i</a>&nbsp;";
                else
                    echo"<a>$i</a>&nbsp";
            }
        ?>

    </div>