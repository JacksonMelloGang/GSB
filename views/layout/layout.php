<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php  header("Cache-Control: max-age=10") ?>
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
    
    <script src="./public/js/search-bar.js"></script>
</body>
</html>