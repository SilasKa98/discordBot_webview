<?php
    session_start();
    if(!$_SESSION["logged_in"]){
        header("Location:index.php");
        exit();
    }
    extract($_SESSION["userData"]);
    extract($_SESSION["userServerData"]);
    if(isset($avatar)){
      $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
    }else{
        $avatar_url = "/discordbot_webview/media/profileDefault_avatar.png";
    }

    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    include_once "services/apiRequestService.php";
    $apiRequests = new ApiRequests();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>


    <link rel="stylesheet" href="general.css">

    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>
<body>
    <?php include_once "frontend/navbar.php"; ?>

    <!--display no permissions notification-->
    <?php if(isset($_GET["error"]) && $_GET["error"] == "NoPermissions"){?>    
        <div class="alert alert-danger" id="noPermissionsWarn" role="alert">
            You don't have permissions to edit the Bot-Settings on this Server.
        </div>
        <script>
            setTimeout(function(){
                document.getElementById("noPermissionsWarn").style.display = "none";
            }, 4000);
        </script>
    <?php }?>

    <div class="container-fluid">
        <div class="card mb-3" id="profileCard" style="max-width: 540px;">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="<?php echo $avatar_url; ?>" class="img-fluid rounded-start" id="profileAvatar" alt="Your Avatar">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $name; ?></h5>
                        <p class="card-text">Here could be some information about the user or other links to something.</p>
                        <p class="card-text"><small class="text-muted" id="smallProfileCardText">Last updated 3 mins ago</small></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card text-center" id="yourServersWrapper">
            <div class="card-header">
                <h3 class="card-title" id="yourServersTitel">Your Servers</h3>
            </div>
            <div class="card-body" id="allServersBody">
                <div class="row row-cols-1 row-cols-md-6 g-4">


                <div class="card" aria-hidden="true">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title placeholder-glow">
      <span class="placeholder col-6"></span>
    </h5>
    <p class="card-text placeholder-glow">
      <span class="placeholder col-7"></span>
      <span class="placeholder col-4"></span>
      <span class="placeholder col-4"></span>
      <span class="placeholder col-6"></span>
      <span class="placeholder col-8"></span>
    </p>
    <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-6"></a>
  </div>
</div>






                    <?php if(count($serverNames) == 0){?>
                       <p id="noServerYet">It seems like you are not connected to any Server yet.</p>
                     <?php } ?>
                    <?php for($i=0;$i<count($serverNames);$i++){ ?>
                        <?php
                            $getUserGuildInfo = $apiRequests->getUserGuildInfos($guildIds[$i], $_SESSION["userData"]["discord_id"]);
                            $bot_id = $_ENV["client_id"];
                            $isBotOnServer = $apiRequests->checkUserOnServer($guildIds[$i], $bot_id);
                            
                            if(isset($getUserGuildInfo["joined_at"])){
                                $userServerJoinDate = "Joined on ".date("Y/m/d",strtotime($getUserGuildInfo["joined_at"]));
                            }else{
                                $userServerJoinDate = "Not Joined Yet";
                            }
                        ?>
                        <?php
                            if(isset($serverIcons[$i])){
                                $serverImg = "https://cdn.discordapp.com/icons/".$guildIds[$i]."/".$serverIcons[$i].".png";
                            }else{
                                $serverImg ="/discordBot_webview/media/bergfestBot_logo_v2.png";
                            }
                        ?>

                        <div class="col">
                            <a class="dashboardServerCardLinkWrapper" href="services/oAuth/server_oAuth/initServerAuth.php?guildId=<?php echo $guildIds[$i];?>">
                                <div class="card h-100 innerYourServersCard">
                                    <img src='<?php echo $serverImg; ?>' class="card-img-top" alt="Server Icon">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $serverNames[$i]; ?></h5>
                                        <?php if(!isset($isBotOnServer['user']['bot'])){?>
                                            <p class="card-text">The Bergfest Bot is not on this server yet. Join him now! </p>
                                        <?php }else{?>
                                            <p class="card-text">The Bergfest Bot is on this server. Nice! </p>
                                        <?php }?>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted" style="color: white !important;"><?php echo $userServerJoinDate;?></small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
    

</body>
</html>


<?php
/*
        for($i=0;$i<count($serverNames);$i++){
           echo "<label>".$serverNames[$i];
           echo "<img src='https://cdn.discordapp.com/icons/".$guildIds[$i]."/".$serverIcons[$i].".png' width='60' height='48'></label>";
        }

    */

    ?>
