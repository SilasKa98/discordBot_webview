<?php
session_start();
$basePath = dirname(__DIR__, 2);
require $basePath.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

$allowedUsers = [];
array_push($allowedUsers,$_ENV["admin_silas"]);
array_push($allowedUsers,$_ENV["admin_thilo"]);
if(!in_array($_SESSION["userData"]["discord_id"],$allowedUsers)){
    header("Location:../../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <style>
    #buttonWrapper{
        margin: 0 auto;
        overflow: auto;
        display: inline-block;
        margin-top: 6%;
    }
    #mainHeader{
        margin-top: 2%;
    }
    </style>
</head>
<body>
    <div class="container-fluid" style="text-align: center;">
        <?php if(isset($_GET["info"]) && $_GET["info"] == "botStarted"){ ?>
            <div class="alert alert-success" role="alert" style="text-align: center;">
                The bot has been started!
            </div>
        <?php }?>
        <?php if(isset($_GET["info"]) && $_GET["info"] == "botStopped"){ ?>
            <div class="alert alert-warning" role="alert" style="text-align: center;">
                The bot has been stopped!
            </div>
        <?php }?>
        <h1 id="mainHeader">Admin Dashboard</h1>
        <div id="buttonWrapper">
            <form action="adminBackend.php" method="post" style="float:left; margin-right: 10px;">
                <input type="hidden" name="method" value="startBot">
                <button class="btn btn-success" type="submit">Start Bot</button>
            </form>

            <form action="adminBackend.php" method="post" style="float:left;">
                <input type="hidden" name="method" value="stopBot">
                <button type="submit" class="btn btn-danger">Stop Bot</button>
            </form>
        </div>
    </div>
</body>
</html>