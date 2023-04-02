<?php
    session_start();
    if(!isset($_SESSION["logged_in"])){
        header("Location:../index.php");
    }

    $basePath = dirname(__DIR__, 1);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    $guild_id = $_GET["guildId"]; // deine Guild-ID hier
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
    print "<pre>";
    print_r($serverInfos);
    print "</pre>";


    $result2 = $oAuthService->doCurl($curlOptions2);
    $members = json_decode($result2, true);
    $memberCount = count($members);
    if($memberCount == 1000){
        $memberCount = "1000+";
    }
    
    print "<pre>";
    print_r($members);
    print "</pre>";
    print $memberCount;

    $result3 = $oAuthService->doCurl($curlOptions3);
    $channels = json_decode($result3, true);
    $channelsCount = count($channels);
    print "<pre>";
    print_r($channels);
    print "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/discordbot_webview/general.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid">
        <div>
            <img id="serverSettingsIcon" src="https://cdn.discordapp.com/icons/<?php echo $serverInfos["id"]."/".$serverInfos["icon"]; ?>.png">
            <h1 id="serverSettingsName"><?php echo $serverInfos["name"]; ?></h1>
        </div>
        
        <div class="card displayInfoCard">
            <div class="card-body">
                <h2>Server Informations</h2>
                <p>Member Count: <?php echo $memberCount; ?></p>
                <p>Roles Count: <?php echo count($serverInfos["roles"]); ?></p>
                <p>Channels Count: <?php echo $channelsCount; ?></p>
            </div>
        </div>

        <div class="card displayInfoCard">
            <div class="card-body">
                <h2>Bot Features</h2>

                <div class="row row-cols-1 row-cols-md-4 g-2">

                    <div class="col">
                        <a href="/discordbot_webview/frontend/eloChecker/eloCheckerSettings.php">
                            <div class="card border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Elo Checker</div>
                                <div class="card-body text-secondary">
                                    <h5 class="card-title">Secondary card title</h5>
                                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Foo</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title">Secondary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Bar</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title">Secondary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-secondary mb-3" style="max-width: 18rem;">
                            <div class="card-header">Buz</div>
                            <div class="card-body text-secondary">
                                <h5 class="card-title">Secondary card title</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="card displayInfoCard">
            <div class="card-body">
                <h2>Bot Settings Activities</h2>
            </div>
        </div>
    </div>
</body>
</html>