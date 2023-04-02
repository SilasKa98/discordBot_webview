<?php
    session_start();
    if(!isset($_SESSION["logged_in"])){
        header("Location:../index.php");
    }else{
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = "/discordbot_webview/media/profileDefault_avatar.png";
        }
    }

    $basePath = dirname(__DIR__, 1);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

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

    #print_r($_SESSION["userData"]);
   # print "<pre>";
    #print_r($members);
    #print "</pre>";
    
    $result3 = $oAuthService->doCurl($curlOptions3);
    $channels = json_decode($result3, true);
    $channelsCount = count($channels);


/*
    //check if the user has permission to edit the settings for the bot (needs to be admin on the discord server)
    $loggedInUserRoles = null;
    foreach($members as $member){
        if($member["user"]["id"] == $_SESSION["userData"]["discord_id"]){
            $loggedInUserRoles = $member["roles"];
            print "<pre>";
            print_r($member);
            print "</pre>";
        }
    }

    // Überprüfen, ob der Nutzer Administratorrechte hat
    $isAdmin = false;
    foreach ($loggedInUserRoles as $role_id) {
        echo $role_id."<br>";
        echo $guild_id."<br>";
        $role_url = "https://discord.com/api/guilds/{$guild_id}/roles/{$role_id}";

        $curlOptions_adminCheck = [
            CURLOPT_URL => $role_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bot {$bot_token}",
                "Content-Type: application/json"
            ]
        ];

        $result_adminCheck = $oAuthService->doCurl($curlOptions_adminCheck);
        $role = json_decode($result_adminCheck, true);

        print_r($result_adminCheck);

        if (in_array("ADMINISTRATOR", $role['permissions'])) {
            $isAdmin = true;
            break;
        }
    }

    if ($isAdmin) {
        echo "Der Nutzer hat Administratorrechte auf diesem Server.";
    } else {
        echo "Der Nutzer hat keine Administratorrechte auf diesem Server.";
    }
*/


    include_once "../services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbActivitiesSelection = $databaseService->selectData("activities", "discord_id=?", [$guild_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Settings</title>
    <link rel="stylesheet" href="/discordbot_webview/general.css">
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
                <img id="serverSettingsIcon" src="/discordbot_webview/media/bergfestBot_logo_v2.png" width=110px height=100px>
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
                <h2>Bot Features</h2>

                <div class="row row-cols-1 row-cols-md-4 g-2">

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <a class="featureLinkWrapp" href="/discordbot_webview/frontend/eloChecker/eloCheckerSettings.php">
                                <div class="card-header featureHeader">Elo Checker</div>
                                <div class="card-body text-secondary">
                                    <h5 class="card-title">Secondary card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                </div>
                            </a>
                        </div>  
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="card-header featureHeader">Foo</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title">Secondary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="card-header featureHeader">Bar</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title">Secondary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3 innerYourServersCard" style="max-width: 18rem;">
                            <div class="card-header featureHeader">Buz</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title">Secondary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
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