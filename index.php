<?php
    session_start();

    //check if cookies exist to loggin user automatically
    if (isset($_COOKIE['logged_in'])) {
        $_SESSION["userData"] = unserialize($_COOKIE["userData"]);
        $_SESSION["userServerData"] = [
            "serverNames" => unserialize($_COOKIE["serverNames"]),
            "serverIcons" => unserialize($_COOKIE["serverIcons"]),
            "guildIds" => unserialize($_COOKIE["guildIds"]),
            "guildOwnerStatus" => unserialize($_COOKIE["guildOwnerStatus"]),
            "guildPermissions" => unserialize($_COOKIE["guildPermissions"])
        ];
        $_SESSION["logged_in"] = $_COOKIE["logged_in"];
    }


    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    if(isset($_SESSION["logged_in"])){
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="general.css">
    <title>Bergfest Bot</title>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>

<body style="background-color: #cbc8c8">
<?php include_once "frontend/navbar.php"; ?>
<div id="topSection"></div>

    <div class="card wrapperCard wrapp2" id="loginSection">
        <div class="px-4 py-5 my-5 text-center">
            <img class="d-block mx-auto mb-4" src="<?php echo $_ENV["app_root"];?>media/bergfestBot_logo_v2.png" width=300px>
            <h1 class="display-5 fw-bold">Bergfest Bot</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">A free customizable Discord bot for your server! All settings are easily customizable via our web interface. Get the bot now and enrich your server!</p>
            </div>
        </div>
    </div>

    <?php if(!isset($_SESSION["logged_in"])){ ?>
    <div class="card wrapperCard wrapp2" id="loginSection">
        <div class="container col-xl-10 col-xxl-8 px-4 py-5">
            <div class="row align-items-center g-lg-5 py-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <h1 class="display-4 fw-bold lh-1 mb-3">Start now</h1>
                    <p class="col-lg-10 fs-4">
                        Log in with discord now and manage which servers you want to have the bot on
                    </p>
                </div>
                <div class="col-md-10 mx-auto col-lg-5">
                <a href="<?php echo $_ENV["app_root"];?>services/oAuth/init-oauth.php">
                    <button type="button" class="btn btn-primary" id="bigSignInBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
                            <path d="M13.545 2.907a13.227 13.227 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.19 12.19 0 0 0-3.658 0 8.258 8.258 0 0 0-.412-.833.051.051 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.041.041 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032c.001.014.01.028.021.037a13.276 13.276 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019c.308-.42.582-.863.818-1.329a.05.05 0 0 0-.01-.059.051.051 0 0 0-.018-.011 8.875 8.875 0 0 1-1.248-.595.05.05 0 0 1-.02-.066.051.051 0 0 1 .015-.019c.084-.063.168-.129.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.052.052 0 0 1 .053.007c.08.066.164.132.248.195a.051.051 0 0 1-.004.085 8.254 8.254 0 0 1-1.249.594.05.05 0 0 0-.03.03.052.052 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.235 13.235 0 0 0 4.001-2.02.049.049 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.034.034 0 0 0-.02-.019Zm-8.198 7.307c-.789 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612Zm5.316 0c-.788 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612Z"></path>
                        </svg>
                        Login with discord
                    </button>
                </a>
                </div>
            </div>
        </div>
    </div>
<?php }?>

    <div class="card wrapperCard wrapp2" id="translateSection">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold lh-1 mb-3">Choose from many useful modules</h1>
                    <p class="lead">
                        With our webview you can always see which modules are selected and active for your server. 
                        You can adjust everything interactively and easily.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
                <div class="col-10 col-sm-8 col-lg-6">
                    <img src="<?php echo $_ENV["app_root"];?>media/manageServersExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" width="500" height="300" alt="Bootstrap Themes" loading="lazy">
                </div>
            </div>
        </div>
    </div>

    <div class="card wrapperCard" id="connectWithOthersSection">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-10 col-sm-8 col-lg-6">
                    <!--<img src="/quizVerwaltung/media/insertExamplePicture.png" style="border-radius: 7px;" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="700" height="500" loading="lazy">-->
                    <img src="<?php echo $_ENV["app_root"];?>media/roleManagerExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" alt="Bootstrap Themes"  loading="lazy">
                </div>
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold lh-1 mb-3">Make the server roles assignable for your users themselves</h1>
                    <p class="lead">
                    Through the role manager, you can make all server roles available to other users. 
                    Each user can assign himself a role by a reaction. Our web application supports you with a very easy administration.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard wrapp2" id="translateSection">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold lh-1 mb-3">Create personalized welcome messages for new users</h1>
                    <p class="lead">
                    With our greeting manager you can easily create welcome messages for new users. 
                    You can freely decide whether they are welcomed via a personal message or publicly in the discord.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
                <div class="col-10 col-sm-8 col-lg-6">
                    <img src="<?php echo $_ENV["app_root"];?>media/greetingManagerExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" width="500" height="300" alt="Bootstrap Themes" loading="lazy">
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard" id="uploadSection">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-10 col-sm-8 col-lg-6">
                    <!--<img src="/quizVerwaltung/media/insertExamplePicture.png" style="border-radius: 7px;" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="700" height="500" loading="lazy">-->
                    <img src="<?php echo $_ENV["app_root"];?>media/faceitEloCurveExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" alt="Bootstrap Themes"  loading="lazy">
                </div>
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold lh-1 mb-3">Check your Faceit Elo History</h1>
                    <p class="lead">
                        This Module is especially for all our Counter Strike fans. With the Faceit Elo Module you can check and display your or anyone else's faceit elo. 
                        You can print a nice graph in every channel of your discord by using /faceit_elo
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard wrapp2" id="translateSection">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold lh-1 mb-3">Earn and Gamble Points with enabling our rewarding point System</h1>
                    <p class="lead">
                        Reward loyal members with points for staying active in the voice Channels. 
                        The lucky gamblers of you might be lucky multiplying their points on the slot machine.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
                <div class="col-10 col-sm-8 col-lg-6">
                    <img src="<?php echo $_ENV["app_root"];?>media/pointSystemExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" width="500" height="300" alt="Bootstrap Themes" loading="lazy">
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard wrapp2" id="contactUs">
        <div class="px-4 py-5 my-5 text-center">
                <h1 class="display-5 fw-bold">Contact us</h1>
                <div class="col-lg-6 mx-auto" style="width: 70%;">
                <p class="lead mb-4">
                    Do you have any questions or suggestions for us? Feel free to leave us a message using the form below
                </p>
                <form>
                    <div class="form-group">
                        <label for="FormControlInput1" class="contactFormLabel">Your Name*</label>
                        <input type="text" class="form-control" id="FormControlInput1">
                    </div>
                    <div class="form-group">
                        <label for="FormControlInput2" class="contactFormLabel">Your Email*</label>
                        <input type="email" class="form-control" id="FormControlInput2">
                    </div>
                    <div class="form-group">
                        <label for="FormControlTextarea3" class="contactFormLabel">Your Message*</label>
                        <textarea class="form-control" id="FormControlTextarea3" rows="3"></textarea>
                    </div>
                    <input id="submitContactFormular" type="submit" value="SEND MESSAGE" class="btn btn-success">
                </form>
            </div>
        </div>
    </div>
        <?php include_once "frontend/footer.php"; ?>
</body>





</html>