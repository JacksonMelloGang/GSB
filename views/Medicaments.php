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
                $med = getMedicamentById($connexion, $_GET['medic']);
                    ob_start(); // start temp

                    if($med != false){
                        // if result from query is not empty, then start tempo & create table filled with data from the result set
                        // else, display, "no result"
                        
                            $depotlegal = $med["medDepotlegal"];
                            $nom = $med["medNomcommercial"];
                            $code = $med["famLibelle"];
                            $compo = $med["medComposition"];
                            $effets = $med["medEffets"];
                            $contreindic = $med["medContreindic"];
                            $prix = empty($med["medPrix"]) == true ? "<b>Non Renseigné.</b>" : $result[$i];
                        
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
            $content = showAllMedicamentsTable($connexion);
            require($_SERVER["DOCUMENT_ROOT"]. "/views/layout/layout.php");
        break;
    }


