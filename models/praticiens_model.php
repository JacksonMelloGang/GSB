<?php

function showAllPraticienstable($connexion){
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
    $sql = "SELECT * FROM praticien LIMIT $debut, $nbr_elements_par_page";
    $result = $connexion->query($sql);
    $ligne = $result->fetch();
    ob_start();
?>  

        <span style='color: black; font-size: 32px; text-align: center;'>Listes des Praticiens</span>


        <!-- Fonction Recherche -->

        <div id="searchby" style="text-align: left">
            <select id="orderby_type">
                <option value='0' selected>Trier par</option>
                <option value='1'>Ville</option>
                <option value='2' >Code Postal</option>
                <option value='3' >Type</option>
            </select>

            <select id="orderby_infotype">
                <option selected>Séléctionner une option</option>
            </select>

            <input name="orderby_input" id="orderby_input" type="text">
            <button id="startorderby">Rechercher</button>
        </div>




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


function getMostReputations($connexion){
    $sql = "SELECT praNom, praCoefnotoriete FROM praticien ORDER BY praCoefnotoriete DESC LIMIT 4 ";
    $stmt = $connexion->query($sql);
    $result = $stmt->fetchAll();

    return $result;
}

function getPraticienInfosById($connexion, $praNum){
    $sql = "SELECT praNum, praNom, praPrenom, praAdresse, praCp, praVille, praCoefnotoriete, typLibelle, typLieu FROM praticien, typepraticien WHERE praNum = ? AND praticien.typCode = typepraticien.typCode";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(array($praNum));
    
    $result = $stmt->fetch();

    if($result == false){
        $returned = false;
    } else {
        $praNumero = $result['praNum'];
        $praNom = $result['praNom'];
        $praPrenom = $result['praPrenom'];
        $praAdresse = $result['praAdresse'];
        $praAdresse = str_replace(array(" r ", " av ", " pl "), array(" Rue ", " Avenue ", " Place "), $praAdresse);
        $praCp = $result['praCp'];
        $praCoefNotoriete = $result['praCoefnotoriete'];
        $typLibelle = $result['typLibelle'];

        $returned = ['Numero' => $praNumero, 'Nom' => $praNom, 'Prenom' => $praPrenom, 'Adresse' => $praAdresse, 'Cp' => $praCp, 'Notoriete' => $praCoefNotoriete, 'Libelle' => $typLibelle];

    }

    return $returned;
}

function getRapportsByPraticiens($connexion, $praNum){
    $sql = "SELECT id, rapDate FROM praticien, rapportvisite WHERE praticien.praNum = rapportvisite.praNum AND rapportvisite.praNum = ?";
    $rapport_stmt = $connexion->prepare($sql);
    $rapport_stmt->execute(array($praNum));
    $result = $rapport_stmt->fetchAll();

    if($result === false){
        $result = false;
    }

    return $result;
}