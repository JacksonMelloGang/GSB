<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- Style -->
    <link href="./public/css/style.css" rel="stylesheet" type="text/css">
    <!--Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <nav class="sidebar">
        <img src="./public/img/gsblogo.png">
        <li class="first-sidebar"><a href="#">Rapports</a></li>
        <li><a href="#">Consulter</a></li>
        <hr>
        <li><a href="#">T</a></li>
        <li><a href="#">TT</a></li>

        <a class="logout" href="#">Se Déconnecter</a>
    </nav>
    <div class="page-content">
        <nav class="topbar">
            <div id="search">
                <form><input type="text" class="search-input" placeholder="Search..."></form>
                <div id="search-result" class='loading-effect' style="display:none; color: white; position: absolute; background-color: red;  "></div>
            </div>
        </nav>
        
        <div class="content" style="margin-top: 10px; overflow: scroll;">
            <?= $content ?>
        </div>
    </div>
    
        <script>
            $(".search-input").on("input", function() {

                if ($(".search-input").val() == "") {
                    $('#search-result').toggle(false);
                    return;
                };

                $('#search-result').toggle(true);
                $('#search-result').addClass('loading-effect');

                $.ajax({
                    async: true,
                    url: "./search.php",
                    success: function(data) {
                        $('#search-result').removeClass('loading-effect')
                        document.getElementById("search-result").innerHTML = "<ul style='list-style: none;'>" + data + "</ul>";
                    }
                });
            });
        </script>
</body>

</html>