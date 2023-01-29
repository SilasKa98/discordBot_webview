<?php
    session_start();
    if(!$_SESSION["logged_in"]){
        header("Location:index.php");
        exit();
    }
    extract($_SESSION["userData"]);
    extract($_SESSION["userServerData"]);
    $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>


    <link rel="stylesheet" href="general.css">

    <!--Documentation:-->
    <!--https://getbootstrap.com/docs/5.3/getting-started/introduction/-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="media/bergfestBot_logo.svg" alt="Logo" width="60" height="48" class="d-inline-block align-text-top">
                <p id="headerName">Bergfest Bot</p>
            </a>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" aria-current="page" href="#">Commands</a>
                    <a class="nav-link" href="#">Join Our Discord</a>
                    <a class="nav-link" href="#">Help</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">

        <a href="logout.php" id="logoutBtn"><button>Logout</button></a>
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
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-6 g-4">
                    
                    <?php for($i=0;$i<count($serverNames);$i++){ ?>
                        <?php
                            if(isset($serverIcons[$i])){
                                $serverImg = "https://cdn.discordapp.com/icons/".$guildIds[$i]."/".$serverIcons[$i].".png";
                            }else{
                                $serverImg ="media/missingServerIcon.png";
                            }
                        ?>
                        <div class="col">
                            <div class="card h-100 innerYourServersCard">
                                <img src='<?php echo $serverImg; ?>' class="card-img-top" alt="Server Icon">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $serverNames[$i]; ?></h5>
                                    <p class="card-text">Here could be some nice Information about the server</p>
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted" style="color: white !important;">Last updated 3 mins ago</small>
                                </div>
                            </div>
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
