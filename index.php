<?php
    // Restricted Access
    require('./includes/auth_middleware.php');
    check_if_allowed("USER");

    // require sql connection
    require_once($_SERVER["DOCUMENT_ROOT"]. "/includes/DbConnexion.php");

    ob_start();
?>
        <div class="card-row">
            <div class="card card-red">
                <canvas id="myChart"></canvas>
            </div>
            <div class="card">
                <canvas id="chart2"></canvas>
            </div>
            <div class="card">
                <canvas id="chart3"></canvas>
            </div>
            <div class="card">
                <canvas id="chart4"></canvas>
            </div>
        </div>

        <div id="stylish-table">
            <table>
                <th>Vos Derniers Rapports</th>
                <?php 
                
                    $result = $connexion->query("SELECT * FROM rapportvisite WHERE visMatricule='{$_SESSION['userId']}' ORDER BY rapDate");
                    $ligne = $result->fetch();

                    while($ligne != false){
                        echo("<tr>");
                            echo("<td><a href='/views/Rapports.php?action=consult&rapid={$ligne['rapNum']}'>Rapport N°{$ligne['rapNum']}</a></td>");
                        echo("</tr>");

                        $ligne = $result->fetch();
                    }

                ?>
            </table>
        </div>

        <script>
            const ctx = document.getElementById('myChart');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: '# of Votes',
                        data: [12, 19, 3, 5, 2, 3],
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
        </script>

<?php
    $title = "GSB - Dashboard";
    $content = ob_get_clean();
    require("./views/layout/layout-index.php");
