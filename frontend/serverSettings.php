<?php
    session_start();

    $basePath = dirname(__DIR__, 1);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    if(!isset($_SESSION["logged_in"])){
        header("Location:../index.php");
    }else{
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
        }
    }

    $guild_id = $_GET["guildId"]; // deine Guild-ID hier
    $_SESSION["currentGuildId"] = $guild_id;
    $bot_token = $_ENV["bot_token"]; // dein Bot-Token hier

    // API-URL zum Abrufen von Server-Informationen
    $url = "https://discord.com/api/guilds/{$guild_id}";
    $url2 = "https://discord.com/api/guilds/{$guild_id}/members?limit=1000&offset=0";
    $url3 = "https://discord.com/api/guilds/{$guild_id}/channels";
    include_once "../services/oAuth/oAuthService.php";

    $oAuthService = new oAuthService();

    //curl options for server infos
    $curlOptions = [
        CURLOPT_URL=>$url,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
                                "Authorization: Bot {$bot_token}",
                                "Content-Type: application/json"
                            ]
    ];

     //curl options for member infos
     $curlOptions2 = [
        CURLOPT_URL=>$url2,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
                                "Authorization: Bot {$bot_token}",
                                "Content-Type: application/json"
                            ]
    ];

    //curl options for channel infos
    $curlOptions3 = [
        CURLOPT_URL=>$url3,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
                                "Authorization: Bot {$bot_token}",
                                "Content-Type: application/json"
                            ]
    ];
    $result = $oAuthService->doCurl($curlOptions);
    $serverInfos = json_decode($result, true);


    $result2 = $oAuthService->doCurl($curlOptions2);
    $members = json_decode($result2, true);
    $memberCount = count($members);
    if($memberCount == 1000){
        $memberCount = "1000+";
    }

    $result3 = $oAuthService->doCurl($curlOptions3);
    $channels = json_decode($result3, true);
    $channelsCount = count($channels);

    //get roles of the current User
    $loggedInUserRoles = null;
    foreach($members as $member){
        if($member["user"]["id"] == $_SESSION["userData"]["discord_id"]){
            $loggedInUserRoles = $member["roles"];
        }
    }

    //get all roles with its permissions from the server
    $rolePermissionDic = [];
    foreach($serverInfos["roles"] as $roles){
        $rolePermissionDic[$roles["id"]] = $roles["permissions"];
    }

    $userIsAdmin = false;
    //check if user has admin permissions
    foreach($rolePermissionDic as $roleId => $permission){
        if(in_array($roleId, $loggedInUserRoles)){
            $admin = 8;
            if (($permission & 8) != 0) {
                $userIsAdmin = true;
                break;
            }
        }
    }

    //also set admin if user is the server owner
    if($_SESSION["userData"]["discord_id"] == $serverInfos["owner_id"]){
        $userIsAdmin = true;
    }

    if($userIsAdmin == false){
        header("Location:../dashboard.php?error=NoPermissions");
        exit();
    }

    include_once "../services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbActivitiesSelection = $databaseService->selectData("activities", "guild_id=?", [$guild_id]);


    $dbSelection = $databaseService->selectData("guilds", "guild_id=?", [$guild_id]);
    if(empty($dbSelection)){
        $dbSelection = array("admin_role_id" => "");
    }else{
       $dbSelection = $dbSelection[0]; 
    }

    print_r($dbSelection);

    include_once "../services/apiRequestService.php";
    $ApiRequests = new ApiRequests();
    $roles = $ApiRequests->getDiscordEntity($guild_id, $bot_token, "roles");


    include_once "../services/dataHandler.php";
    $dataHandler = new DataHandler();
    $rolesIdDic = $dataHandler->inputToDictionaryFilter($roles, "id", "name");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Settings</title>
    <link rel="stylesheet" href="<?php echo $_ENV["app_root"];?>general.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>
<body>
    <?php include_once "navbar.php"; ?>
    <div class="container-fluid" style="margin-top: 1%;">
        <div>
            <?php if($serverInfos["icon"] != ""){?>
                <img id="serverSettingsIcon" src="https://cdn.discordapp.com/icons/<?php echo $serverInfos["id"]."/".$serverInfos["icon"]; ?>.png">
            <?php }else{?>
                <img id="serverSettingsIcon" src="<?php echo $_ENV["app_root"];?>media/bergfestBot_logo_v2.png" width=110px height=100px>
            <?php }?>
            <span id="serverSettingsName"><?php echo $serverInfos["name"]; ?></span>
        </div>
        
        <div class="card displayInfoCard text-bg-dark">
            <div class="card-body">
                <h2>Server Informations</h2>
                <p>Member Count: <b><?php echo $memberCount; ?></b></p>
                <p>Roles Count: <b><?php echo count($serverInfos["roles"]); ?></b></p>
                <p>Channels Count: <b><?php echo $channelsCount; ?></b></p>
            </div>
        </div>


        <div class="card displayInfoCard text-bg-dark">
            <div class="card-body">
                <form action="<?php echo $_ENV["app_root"];?>doTransaction.php" method="post">
                    <input type="hidden" name="method" value="changeGeneralSettings">
                    <h2>General Settings</h2>
                    <h5 class="card-title">Admin ID</h5>
                    <div class="input-group mb-3">
                        <select class="form-select text-bg-secondary" name="adminId" aria-label="Admin Id">
                            <option>Open this select menu</option>
                            <?php foreach($rolesIdDic as $roleId => $roleName){ ?>
                                    <option value="<?php echo $roleId; ?>" <?php if($roleId == $dbSelection["admin_role_id"]){ echo "selected";} ?>><?php echo $roleName;?></option>
                            <?php }?>
                        </select>
                    </div>
                    <input type="submit" class="btn btn-success" value="Save">
                </form>
            </div>
        </div>

        <div class="card displayInfoCard text-bg-dark">
            <div class="card-body">
                <h2>Bot Features</h2>

                <div class="row row-cols-1 row-cols-md-4 g-2">
                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="moduleSwitch"><label class="switch"><input id="box1" type="checkbox" name="changeStatus" onchange="changeModulStatus(this,'cs')" <?php if($dbSelection["cs"] == 1){ echo "checked";}?>><span class="slider round"></span></label></div>
                                <div class="card-header featureHeader">Faceit elo</div>
                                <div class="card-body text-secondary">
                                    <h3 class="card-title secondaryModuleTitel">Check and display your Elo!</h3>
                                    <p class="card-text">
                                     With the faceit elo module you can display your elo history for a freely selected period of time.
                                    </p>
                                </div>
                            </div>  
                        </div>

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="moduleSwitch"><label class="switch"><input id="box1" type="checkbox" name="changeStatus"  onchange="changeModulStatus(this,'reaction_role')" <?php if($dbSelection["reaction_role"] == 1){ echo "checked";}?>><span class="slider round"></span></label></div>
                            <a class="featureLinkWrapp" href="<?php echo $_ENV["app_root"];?>frontend/modules/roleManager/roleManager.php">
                                <div class="card-header featureHeader">Role Manager</div>
                                <div class="card-body text-secondary">
                                    <h5 class="card-title secondaryModuleTitel">Comming soon</h5>
                                    <p class="card-text">In the future we plan to expand our variety of modules. Stay tuned.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="moduleSwitch"><label class="switch"><input id="box1" type="checkbox" name="changeStatus"  onchange="changeModulStatus(this,'gamble')" <?php if($dbSelection["gamble"] == 1){ echo "checked";}?>><span class="slider round"></span></label></div>
                            <div class="card-header featureHeader">Casino</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title secondaryModuleTitel">Comming soon</h5>
                                <p class="card-text">In the future we plan to expand our variety of modules. Stay tuned.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="moduleSwitch"><label class="switch"><input id="box1" type="checkbox" name="changeStatus"  onchange="changeModulStatus(this,'lol')" <?php if($dbSelection["lol"] == 1){ echo "checked";}?>><span class="slider round"></span></label></div>
                            <div class="card-header featureHeader">League Of Legends</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title secondaryModuleTitel">Comming soon</h5>
                                <p class="card-text">In the future we plan to expand our variety of modules. Stay tuned.</p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="card displayInfoCard text-bg-dark">
            <div class="card-body">
                <h2>Recent Activities</h2>
                <div class="activityLogBody">
                    <?php foreach($dbActivitiesSelection as $activitie){?>
                        <div class="alert alert-success activityLogMsg" role="alert">
                            <span class="logAuthor"><?php echo $activitie["author"]?></span>
                            <span class="logAction"><?php echo $activitie["action"]?></span>
                            <span class="logDate"><?php echo $activitie["date"]?></span>
                        </div>
                    <?php }?>
                    <?php if(empty($dbActivitiesSelection)){?>
                        <p>There is no recent activity yet</p>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>

    function changeModulStatus(elem, moduleName){
        let moduleStatus;
        if(elem.checked == true){
            moduleStatus = 1;
        }else{
            moduleStatus = 0;
        }
        $.ajax({
            type: "POST",
            url: "../doTransaction.php",
            data: {
                method: "changeModulStatus",
                moduleName: moduleName,
                moduleStatus: moduleStatus
            },
					success: function(response, message, result) {
						console.log(response);
						console.log(message);
						console.log(result);
					}
        });
    }

</script>