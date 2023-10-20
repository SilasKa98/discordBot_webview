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
$apiRequests = new ApiRequests();
$roles = $apiRequests->getDiscordEntity($guild_id, $bot_token, "roles");
$channels = $apiRequests->getDiscordEntity($guild_id, $bot_token, "channels");

include_once "../../../services/dataHandler.php";
$dataHandler = new DataHandler();
$roleDic = $dataHandler->inputToDictionaryFilter($roles, "position", "name");
$channelDic = $dataHandler->inputToDictionaryFilter($channels, "id", "name");

//sorting roles Dic by position (key)
ksort($roleDic);

$bergfestBotRoleKey = $dataHandler->getArrayKeyByValue($roleDic, "BergfestBot");
$slicedRoleDic = array_slice($roleDic, 0, $bergfestBotRoleKey);

print_r($slicedRoleDic);


include_once "../../../services/databaseService.php";
$databaseService = new DatabaseService;
$dbSelection = $databaseService->selectData("reaction_messages", "guild_id=?", [$guild_id]);
if(empty($dbSelection)){
    $dbSelection = array("channel_id" => "", "messages_id" => "", "message" => "");
}else{
   $dbSelection = $dbSelection[0]; 
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $_ENV["app_root"];?>general.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Manage Roles</title>
</head>
<body>

<?php include_once "../../navbar.php"; ?>
    <div class="container-fluid">
        <div id="eloCheckerWrapper">
            <h1 id="faceitEloCheckerHeader">Role Manager</h1>
            <form action="<?php echo $_ENV["app_root"];?>doTransaction.php" method="POST">
                <input type="hidden" name="method" value="roleManager">
            
                    <div class="card text-bg-dark mb-3">

                        <div class="card-body">
                            <h5 class="card-title">Role Selection Channel</h5>
                            <select class="form-select text-bg-secondary" name="updateChannelFaceit" aria-label="Update Channel">
                                <option selected>Open this select menu</option>
                                <?php foreach($channelDic as $channelId => $channelName){ ?>
                                        <option value="<?php echo $channelId; ?>" <?php if($channelId == $dbSelection["channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
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

                    
                    <div class="card text-bg-dark mb-3">

                        <div class="card-body">
                            <h5 class="card-title">Header Message</h5>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Personal text: </span>
                                <input type="text" class="form-control" placeholder="Hey @everyone Select your favorit games below!" aria-label="Username" aria-describedby="basic-addon1">
                            </div>    
                            

                            <div class="input-group mb-3 roleDisplay">
                                <div class="emojiSelWrapper">
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="descWrapper">
                                    <input type="text" class="form-control" placeholder="Description" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                                <div class="roleSelWrapper">
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
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
                    


                <hr>
            
                    <br>
                    
                        <button class="btn btn-success" type="submit" style="width: 98%; margin-left: 1%;">Save</button>
                   
                    <br>
            </form>
        </div>

</body>
</html>