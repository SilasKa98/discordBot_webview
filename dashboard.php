<?php
    session_start();
    if(!$_SESSION["logged_in"]){
        header("Location:index.php");
        exit();
    }
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    extract($_SESSION["userData"]);
    extract($_SESSION["userServerData"]);
    if(isset($avatar)){
      $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
    }else{
        $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
    }


    include_once "services/apiRequestService.php";
    $apiRequests = new ApiRequests();

    $totalServerNumber = count($serverNames);
    $totalServersAdminCount = count(array_keys($guildOwnerStatus, 1));
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
                        <p class="card-text placeholder-glow"><span id="userTotalServersInfo" class="placeholder">Total Servers joined: <?php echo $totalServerNumber;?></span></p>
                        <p class="card-text placeholder-glow"><span id="userServerAdminInfo" class="placeholder">Admin on <?php echo $totalServersAdminCount;?> Server(s)</span></p>
                        <p class="card-text placeholder-glow"><small class="text-muted placeholder" id="smallProfileCardText">Last updated just now</small></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" id="allServersBody">

            <div class="card-header" id="dashboardTitelWrapper">
                <h3 class="card-title" id="yourServersTitel">Your Servers</h3>
            </div>

                <div class="row row-cols-1 row-cols-md-6 g-4" id="placeholderLoadWrapper">
                    <?php $opacity = 1;?>
                    <?php for($i=0;$i<6;$i++){?>
                        <div class="col">
                            <div class="card h-100 innerYourServersCard" aria-hidden="true" style="opacity:<?php echo $opacity;?>;">
                                <img src='<?php echo $_ENV["app_root"];?>media/bergfestBot_logo_v2.png' class="card-img-top" alt="Server Icon">
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
                                    <div class="card-footer"></div>
                                </div>
                            </div>
                        </div>
                        <?php $opacity = $opacity - 0.1;?>
                    <?php }?>
                </div>

                <div class="row row-cols-1 row-cols-md-6 g-4" id="actualCardWrapper" style="display:none;">
                    
                    <?php if(count($serverNames) == 0){?>
                       <p id="noServerYet">It seems like you are not connected to any Server yet.</p>
                     <?php } ?>
                     <?php 
                        //first collecting all curl options for every server in an array
                        $curlOptionsArray = [];
                        for($i=0;$i<count($serverNames);$i++){
                            $curlOptions= $apiRequests->generateCurlOptionsForUserGuildInfos($guildIds[$i], $_ENV["client_id"]); 
                            array_push($curlOptionsArray,$curlOptions);
                        }
                        //send all options to the async function
                        include_once "services/oAuth/oAuthService.php";
                        $oAuthService = new oAuthService();
                        $getUserGuildInfo = $oAuthService->doAsyncCurl($curlOptionsArray);

                        //decode each result from json to array and push it in a new array which contains the final result
                        $userGuideInfoArray = [];
                        for($i=0;$i<count($getUserGuildInfo);$i++){
                            array_push($userGuideInfoArray,json_decode($getUserGuildInfo[$i], true));
                        }

                     ?>
                    <?php for($i=0;$i<count($serverNames);$i++){ ?>
                        <?php
                            if(isset($userGuideInfoArray[$i]["joined_at"])){
                                $userServerJoinDate = "<span class='badge rounded-pill text-bg-success'>Bergfest Bot joined on ".date("Y/m/d",strtotime($userGuideInfoArray[$i]["joined_at"]))."</span>";
                            }else{
                                $userServerJoinDate = "<span class='badge rounded-pill text-bg-warning'>Not Joined Yet</span>";
                            }                            
                        ?>
                        <?php
                            if(isset($serverIcons[$i])){
                                $serverImg = "https://cdn.discordapp.com/icons/".$guildIds[$i]."/".$serverIcons[$i].".png";
                            }else{
                                $serverImg =$_ENV["app_root"]."media/bergfestBot_logo_v2.png";
                            }
                        ?>

                        <div class="col">
                            <a class="dashboardServerCardLinkWrapper" href="services/oAuth/server_oAuth/initServerAuth.php?guildId=<?php echo $guildIds[$i];?>">
                                <div class="card h-100 innerYourServersCard">
                                    <img src='<?php echo $serverImg; ?>' class="card-img-top" alt="Server Icon">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $serverNames[$i]; ?></h5>
                                        <?php if(!isset($userGuideInfoArray[$i]["joined_at"])){?>
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
    <?php 
        if(!isset($_COOKIE['logged_in'])){
            include_once "frontend/cookie_alert.php";
        }
    ?>

    <?php
        include_once "frontend/notificationToast.php";
        if(isset($_GET["error"])){
            if($_GET["error"] == "illegalGuildId"){
                print"
                <script>
                    setTimeout(function(){ 
                        $(\".toast\").toast('show');
                        $(\"#toastMsgBody\").html(\"An Error occured, please try again.\");
                    }, 1000);
                </script>";
            }
        }
    ?>
</body>
</html>

<script>

    //hide cookie question if pressed reject.
    let cookieState = localStorage.getItem("cookies");
    if(cookieState == "rejected"){
        document.getElementById("cookie_check").style.display = "none";
    }

    function acceptCookies(){
        $.ajax({
            type: "POST",
            url: "doTransaction.php",
            data: {
                method: "acceptCookies"
            },
            success: function(response, message, result) {
                console.log(response);
                console.log(message);
                console.log(result);
                document.getElementById("cookie_check").style.display = "none";
            }
        });
    }

    function rejectCookies(){
        document.getElementById("cookie_check").style.display = "none";
        localStorage.setItem("cookies", "rejected");
    }
    
    window.addEventListener('load', function() {
        document.getElementById("placeholderLoadWrapper").style.display = "none";
        document.getElementById("actualCardWrapper").style.display = "flex";
        const timestampReload = Date.now();
    })
    


    // Get the timestamp of the page load
    const startTime = Date.now();

    // Update the timer every second
    setInterval(function() {
        const elapsedTime = Date.now() - startTime;
        
        // Calculate the hours, minutes and seconds from elapsed time
        const hours = Math.floor(elapsedTime / (60 * 60 * 1000));
        const minutes = Math.floor((elapsedTime % (60 * 60 * 1000)) / (60 * 1000));

        // Update the timer display
        if(minutes === 0){
            var lastUpdated = "just now";
        }else if(minutes <= 59){
            var lastUpdated = minutes+" minute(s) ago";
        }else{
            var lastUpdated = hours+" hour(s) ago";
        }
        document.getElementById('smallProfileCardText').textContent = "Last updated "+lastUpdated;
    }, 1000);

    //remove placeholders from user info card
    document.getElementById('smallProfileCardText').classList.remove("placeholder");
    document.getElementById('userTotalServersInfo').classList.remove("placeholder");
    document.getElementById('userServerAdminInfo').classList.remove("placeholder");

</script>

