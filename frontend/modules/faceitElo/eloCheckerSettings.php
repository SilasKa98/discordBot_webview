<?php
    session_start();

    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    if(!isset($_SESSION["logged_in"])){
        header("Location:../../index.php");
    }else{
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
        }
    }

    $guild_id = $_SESSION["currentGuildId"];
    $bot_token = $_ENV["bot_token"];


    include_once "../../../services/apiRequestService.php";
    $ApiRequests = new ApiRequests();
    $roles = $ApiRequests->getDiscordEntity($guild_id, $bot_token, "roles");

    include_once "../../../services/apiRequestService.php";
    $ApiRequests = new ApiRequests();
    $channels = $ApiRequests->getDiscordEntity($guild_id, $bot_token, "channels");


    include_once "../../../services/dataHandler.php";
    $dataHandler = new DataHandler();

    //done in function
    $rolePermissionDic = $dataHandler->inputToDictionaryFilter($roles, "id", "name");
    $roleNames = $dataHandler->inputToArrayFilter($roles, "name");


    //done directly here and not in function because of type == 0
    $channelNames = [];
    $channelNameIdDic = [];
    foreach($channels as $cha){
        if($cha["type"] == 0){
            $channelNameIdDic[$cha["id"]] = $cha["name"];
            array_push($channelNames,$cha["name"]);
        }
    }

    include_once "../../../services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("server_settings", "guild_id=?", [$guild_id]);
    if(empty($dbSelection)){
        $dbSelection = array("admin_role_id" => "");
    }else{
       $dbSelection = $dbSelection[0]; 
    }

    $dbSelection_csgo = $databaseService->selectData("csgo_elo_settings", "guild_id=?", [$guild_id]);
    if(empty($dbSelection_csgo)){
        $dbSelection_csgo = array("text_channel_id" => "", "mvp_update_rhythm" => "d", "mvp_update_delta" => 0);
    }else{
       $dbSelection_csgo = $dbSelection_csgo[0]; 
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $_ENV["app_root"];?>general.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Elo Checker</title>
</head>
<body>
    <?php include_once "../../navbar.php"; ?>
    <div class="container-fluid">
        <div id="eloCheckerWrapper">
            <h1 id="faceitEloCheckerHeader">Faceit Elo Checker Settings</h1>
            <form action="<?php echo $_ENV["app_root"];?>doTransaction.php" method="POST">
                <input type="hidden" name="method" value="faceitEloChecker">
                

                    
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">Admin ID</h5>
                                <div class="input-group mb-3">
                                    <select class="form-select text-bg-secondary" name="adminId" aria-label="Admin Id">
                                        <option>Open this select menu</option>
                                        <?php foreach($rolePermissionDic as $roleId => $roleName){ ?>
                                                <option value="<?php echo $roleId; ?>" <?php if($roleId == $dbSelection["admin_role_id"]){ echo "selected";} ?>><?php echo $roleName;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-secondary">
                                <p class="card-text">
                                    <small class="text-body-dark smallDescText">
                                        &#9432; Description
                                    </small>
                                </p>
                            </div>

                        </div>
                   

                 
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">MVP Update Time</h5>
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-bg-secondary" id="basic-addon1">Update Rythm</span>
                                    <select class="form-select text-bg-secondary" name="mvpRythm">
                                        <option value="d"<?php if($dbSelection_csgo["mvp_update_rhythm"] == "d"){ echo "selected";}?>>Daily</option>
                                        <option value="w"<?php if($dbSelection_csgo["mvp_update_rhythm"] == "w"){ echo "selected";}?>>Weekly</option>
                                        <option value="m"<?php if($dbSelection_csgo["mvp_update_rhythm"] == "m"){ echo "selected";}?>>Monthly</option>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-bg-secondary" id="basic-addon1">Pick the posting time</span>
                                    <input type="time" value="<?php echo date("H:i",$dbSelection_csgo["mvp_update_delta"]);?>" class="form-control text-bg-secondary" name="mvpTime" aria-label="mvpTime" aria-describedby="basic-addon1">
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-secondary">
                                <p class="card-text">
                                    <small class="text-body-dark smallDescText">
                                        &#9432; Description
                                    </small>
                                </p>
                            </div>

                        </div>
                    </div>
               
                
                    <div class="card text-bg-dark mb-3">

                        <div class="card-body">
                            <h5 class="card-title">MVP Update Channel</h5>
                            <select class="form-select text-bg-secondary" name="updateChannelFaceit" aria-label="Update Channel">
                                <option selected>Open this select menu</option>
                                <?php foreach($channelNameIdDic as $channelId => $channelName){ ?>
                                        <option value="<?php echo $channelId; ?>" <?php if($channelId == $dbSelection_csgo["text_channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
                                <?php }?>
                            </select>
                        </div>

                        <div class="card-footer bg-transparent border-secondary">
                            <p class="card-text">
                                <small class="text-body-dark smallDescText">
                                    &#9432; Description
                                </small>
                            </p>
                        </div>
                    </div>
                <hr>
            
                    <br>
                    
                        <button class="btn btn-success" type="submit" style="width: 98%; margin-left: 1%;">Save</button>
                   
                    <br>
            </form>
        </div>
    
</body>
</html>