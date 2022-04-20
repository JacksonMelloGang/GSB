<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php  header("Cache-Control: max-age=100") ?>
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
    <?php  require("./views/components/sidebar-select.php"); ?>
    <div class="page-content">
        <nav class="topbar">
            <div id="search">
                <input type="text" class="search-input" placeholder="Search...">
                <div id="search-result" class='loading-effect' style="display:none; position: absolute; color: white; background-color: red;  "></div>
            </div>
        </nav>
        
        <div class="content">
            <?= $content ?>
        </div>
    </div>
    
        <script>
            $(".search-input").on("input", function() {
                // empty result
                document.getElementById("search-result").innerHTML = "";

                // when search input is empty, hide result
                if ($(".search-input").val() == "") {
                    $('#search-result').toggle(false);
                    return;
                };

                // otherwise show it and add loading effect & disable scrolling
                $('#search-result').toggle(true);
                $('#search-result').addClass('loading-effect');
                document.getElementById('search-result').style.overflow = 'hidden';

                // make ajax request to get data from search.php
                $.ajax({
                    async: true,
                    url: "./search.php?search="+ $(".search-input").val(),
                    timeout: 5000,
                    success: function(data) {
                        $('#search-result').removeClass('loading-effect')
                        document.getElementById('search-result').style.overflow = 'scroll';
                        document.getElementById("search-result").innerHTML = "<ul style='list-style: none;'>" + data + "</ul>";
                    }
                });
            });
        </script>
</body>

</html>