<?php
    session_start();
    if(!isset($_SESSION["logged_in"])){
        header("Location:../../index.php");
    }else{
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = "/discordbot_webview/media/profileDefault_avatar.png";
        }
    }

    $basePath = dirname(__DIR__, 2);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    $guild_id = $_SESSION["currentGuildId"];
    $bot_token = $_ENV["bot_token"];

    $url = "https://discord.com/api/guilds/{$guild_id}/roles";

    $url2 = "https://discord.com/api/guilds/{$guild_id}/channels";

    include_once "../../services/oAuth/oAuthService.php";
    $oAuthService = new oAuthService();


     //curl options for role infos
     $curlOptions = [
        CURLOPT_URL=>$url,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
                                "Authorization: Bot {$bot_token}",
                                "Content-Type: application/json"
                            ]
    ];

    //curl options for channel infos
    $curlOptions2 = [
        CURLOPT_URL=>$url2,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
                                "Authorization: Bot {$bot_token}",
                                "Content-Type: application/json"
                            ]
    ];
    
    $result = $oAuthService->doCurl($curlOptions);
    $roles = json_decode($result, true);
    
    $roleNames = [];
    $rolePermissionDic = [];
    foreach($roles as $role){
        $rolePermissionDic[$role["id"]] = $role["name"];
        array_push($roleNames,$role["name"]);
    }

    $result2 = $oAuthService->doCurl($curlOptions2);
    $channels = json_decode($result2, true);

    #print_r($channels);

    $channelNames = [];
    $channelNameIdDic = [];
    foreach($channels as $cha){
        if($cha["type"] == 0){
            $channelNameIdDic[$cha["id"]] = $cha["name"];
            array_push($channelNames,$cha["name"]);
        }
    }

    include_once "../../services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("server_settings", "discord_id=?", [$guild_id]);
    if(empty($dbSelection)){
        $dbSelection = array("admin_role_id" => "", "mvp_update" => "",
        "csgo_text_channel" => "", "lol_text_channel" => "", "ff_1_role_id" => "", "ff_2_role_id" => "");
    }else{
       $dbSelection = $dbSelection[0]; 
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/discordbot_webview/general.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Elo Checker</title>
</head>
<body>
    <?php include_once "../navbar.php"; ?>
    <div class="container-fluid">
        <div id="eloCheckerWrapper">
            <h1 id="faceitEloCheckerHeader">General Elo Checker Settings</h1>
            <form action="/discordbot_webview/doTransaction.php" method="POST">
                <input type="hidden" name="method" value="faceitEloChecker">
                <div class="row row-cols-1 row-cols-md-2 g-4">

                    <div class="col">
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
                    </div>

                    <div class="col">
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">MVP Update Time</h5>
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-bg-secondary" id="basic-addon1">Pick the Date</span>
                                    <input type="date" value="<?php echo explode(" ",$dbSelection["mvp_update"])[0];?>" class="form-control text-bg-secondary" name="mvpTime" aria-label="mvpTime" aria-describedby="basic-addon1">
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
                </div>

                <hr class="deviderHr">

                <h2 class="subHeadingEloChecker">Teams<h2>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <div class="col">
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">Role for Team 1</h5>
                                <select class="form-select text-bg-secondary" name="roleTeam1" aria-label="Role for Team 1">
                                    <option selected>Open this select menu</option>
                                    <?php foreach($rolePermissionDic as $roleId => $roleName){ ?>
                                            <option value="<?php echo $roleId; ?>"<?php if($roleId == $dbSelection["ff_1_role_id"]){ echo "selected";} ?>><?php echo $roleName;?></option>
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
                    </div>

                    <div class="col">
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">Role for Team 2</h5>
                                <select class="form-select text-bg-secondary" name="roleTeam2" aria-label="Role for Team 1">
                                    <option selected>Open this select menu</option>
                                    <?php foreach($rolePermissionDic as $roleId => $roleName){ ?>
                                            <option value="<?php echo $roleId; ?>"<?php if($roleId == $dbSelection["ff_2_role_id"]){ echo "selected";} ?>><?php echo $roleName;?></option>
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
                    </div>
                </div>

                <hr class="deviderHr">

                <h2 class="subHeadingEloChecker">Update Channels</h2>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <div class="col">
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">Faceit-Elo Update Channel</h5>
                                <select class="form-select text-bg-secondary" name="updateChannelFaceit" aria-label="Update Channel">
                                    <option selected>Open this select menu</option>
                                    <?php foreach($channelNameIdDic as $channelId => $channelName){ ?>
                                            <option value="<?php echo $channelId; ?>" <?php if($channelId == $dbSelection["csgo_text_channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
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
                    </div>

                    <div class="col">
                        <div class="card text-bg-dark mb-3">

                            <div class="card-body">
                                <h5 class="card-title">League of Legends Update Channel</h5>
                                <select class="form-select text-bg-secondary" name="updateChannelLol" aria-label="Update Channel">
                                    <option selected>Open this select menu</option>
                                    <?php foreach($channelNameIdDic as $channelId => $channelName){ ?>
                                            <option value="<?php echo $channelId; ?>" <?php if($channelId == $dbSelection["lol_text_channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
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
                    </div>
                </div>
                <hr>
            
                    <br>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" type="submit">Save</button>
                    </div>
                    <br>
            </form>
        </div>
    </div>
</body>
</html>