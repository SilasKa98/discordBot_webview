<?php

    session_start();

    if(!isset($_SESSION["logged_in"])){
        header("Location:../../index.php");
        exit();
    }else{
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
        }
    }

    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    $guild_id = $_SESSION["currentGuildId"];


    include_once "../../../services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("guilds", "guild_id=?", [$guild_id]);
    if(empty($dbSelection)){
        $dbSelection = array();
    }else{
        $dbSelection = $dbSelection[0];
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point System</title>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="<?php echo $_ENV["app_root"];?>frontend/modules/pointSystem/pointSystemStyles.css" rel="stylesheet">
    <link href="<?php echo $_ENV["app_root"];?>general.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>
<?php include_once "../../navbar.php"; ?>
    <div class="container-fluid">
        <div id="eloCheckerWrapper">
            <h1 id="pointSystemHeader">Point System</h1>
            
            <input type="hidden" value="<?php echo $_GET["guildId"];?>" name="getGuildId" id="getGuildId">
            <input type="hidden" name="method" value="faceitEloChecker">
                    
                <div class="card text-bg-dark mb-3">

                    <div class="card-body">
                        <h5 class="card-title">Payment per Minute</h5>
                        <div class="input-group mb-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">💰</span>
                                <input type="number" min="1" value="<?php echo $dbSelection["payment_per_minute"];?>" class="form-control" id="ppm" aria-label="ppm" aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-secondary">
                        <p class="card-text">
                            <small class="text-body-dark smallDescText">
                                &#9432; Set your desired PPM here. Our suggested Payment per Minute amount is somewhere around 10.
                            </small>
                        </p>
                    </div>

                </div>           

                <button class="btn btn-success" onclick="savePointSystem()"type="button" style="width: 98%; margin-left: 1%;">Save</button>
        </div>
    </div>
    <?php include_once "../../../frontend/footer.php";?>
    <?php include_once "../../notificationToast.php";?>
</body>
</html>

<script>
    function savePointSystem(){
        let ppm = document.getElementById("ppm").value;
        let getGuildId = document.getElementById("getGuildId").value;
        $.ajax({
            type: "POST",
            url: "../../../doTransaction.php",
            data: {
                method: "payment_per_minute",
                ppm: ppm,
                getGuildId: getGuildId       
            },
            success: function(response, message, result) {
                if(response == "illegalGuildId"){
                    location.href="../../../dashboard.php?error=illegalGuildId";
                }

                if(message == "success"){
                    $(".toast").toast('show');
                    $("#toastMsgBody").html("Changes saved successfully!");
                }
            }
        });  
    }
</script>