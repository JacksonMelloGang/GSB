<?php
    // Restricted Access
    require('./includes/auth_middleware.php');
    check_if_allowed("USER");

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    // require sql model to get info from database
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/medicaments_model.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/models/praticiens_model.php");


    $samplesresult = getMostProposedSamples($connexion);
    $reputationresult = getMostReputations($connexion);

    ob_start();
?>
        <div class="card-row">
            <div class="card card-red">
                <canvas id="myChart"></canvas>
            </div>
            <div class="card card-blue">
                <canvas id="chart2" width="50px" height="50px"></canvas>
            </div>
            <div class="card card-purple">
                <canvas id="chart3" width="50px" height="50px"></canvas>
            </div>
            <div class="card card-green">
                <canvas id="chart4" width="50px" height="50px"></canvas>
            </div>
        </div>

        <div id="stylish-table" style="margin-top: auto;margin-bottom: 50px;border: 1px solid black; width: 50%">
            <table>
                <?php 
                    if(!isset($_SESSION["userId"])){
                        echo("<tr>Erreur, vous ne posséder pas de matricule.</tr>");
                    } else {
                        echo("<th>Vos Derniers Rapports</th>");
                        /* A php code that is fetching rapports from the database and displaying it in a table. */
                        $result = $connexion->query("SELECT * FROM rapportvisite WHERE visMatricule='{$_SESSION['userId']}' ORDER BY rapDate");
                        $ligne = $result->fetch();

                        while($ligne != false){
                            echo("<tr>");
                                echo("<td><a href='/views/EditRapport.php?&rapid={$ligne['id']}'>Rapport N°{$ligne['rapNum']}</a></td>");
                            echo("</tr>");

                            $ligne = $result->fetch();
                        }
                    }
                ?>
            </table>
        </div>

        <script>
            const ctx = document.getElementById('myChart');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php
                            // echo name of medic
                            if(sizeof($samplesresult) < 5){
                                for($i=0; $i < 4; $i++){
                                    echo("'NO DATA', ");
                                }
                            } else {
                                for($i=0; $i < 4; $i++){
                                    for($j=0; $j < 1; $j++){
                                        echo("'{$samplesresult[$i][1]}', ");
                                    }
                                }    
                            }
                        
                        ?>
                    ],
                    datasets: [{
                        label: 'Nombre d\'échantillon',
                        data: [
                            <?php
                                // echo number of medic               
                                if(sizeof($samplesresult) < 5){
                                    for($i=0; $i < 4; $i++){
                                        echo("0, ");
                                    }
                                } else {
                                    for($i=0; $i < 4; $i++){
                                        for($j=0; $j < 1; $j++){
                                            echo("{$samplesresult[$i][0]}, ");
                                        }
                                    }
                                }                                                                                
                            ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                }
            });

            const chart2 = document.getElementById('chart2');
            const data_chart2 = {
                labels: [
                    <?php                                                               
                        /* This is a php code that is checking if the size of the reputation result
                        is equal to 0, if it is, it will display 0, else it will display the
                        reputation result. */
                        if(sizeof($reputationresult) == 0){
                            for($i=0; $i < 4; $i++){
                                echo("'NO DATA', ");
                            }
                        } else {
                            for($i=0; $i < 4; $i++){
                                for($j=0; $j < 1; $j++){
                                    echo("'Réputation {$reputationresult[$i][0]}', ");
                                }
                            }
                        }
                    ?>
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [

                        <?php           
                            // if no data from mysql request, only display fill var data of 0 value, else set value from request
                            /* This is a php code that is checking if the size of the reputation result
                            is equal to 0, if it is, it will display 0, else it will display the
                            reputation result. */
                            if(sizeof($reputationresult) == 0){
                                for($i=0; $i < 4; $i++){
                                    echo("0, ");
                                }
                            } else {
                                for($i=0; $i < 4; $i++){
                                    for($j=0; $j < 1; $j++){
                                        echo("{$reputationresult[$i][1]}, ");
                                    }
                                }
                            }
                        ?>
                        
                    ],
                    backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                    ],
                    hoverOffset: 4
                }]
            };
            const config = {
                type: 'pie',
                data: data_chart2,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                }
            };
            new Chart(chart2, config);
        </script>

<?php
    $title = "GSB - Dashboard";
    $content = ob_get_clean();
    require("./views/layout/layout-index.php");
