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
    <link rel="stylesheet" href="mediaQueryStyles.css">
    <title>Bergfest Bot</title>
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>

<body style="background-color: #cbc8c8">
<?php include_once "frontend/navbar.php"; ?>
<div id="topSection"></div>

    <div class="card wrapperCard wrapp2" id="startSection">
        <div class="px-4 py-5 my-5 text-center">
            <img class="d-block mx-auto mb-4" src="<?php echo $_ENV["app_root"];?>media/bergfestBot_logo_v2.png" width=300px>
            <h1 class="display-5 fw-bold">Bergfest Bot</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">
                    Enhance Your Server with Our Free, Customizable Discord Bot! Experience seamless customization with our user-friendly web interface. Elevate your server today by adding our versatile bot.
                </p>
            </div>
        </div>
    </div>

    <?php if(!isset($_SESSION["logged_in"])){ ?>
    <div class="card wrapperCard wrapp2" id="loginSection">
        <div class="container col-xl-10 col-xxl-8 px-4 py-5 hidden-element-right" id="index_inner_loginSection">
            <div class="row align-items-center g-lg-5 py-5">
                <div class="col-lg-7 text-center text-lg-start" id="loginInfoTextWrapper">
                    <h1 class="display-4 fw-bold lh-1 mb-3">START NOW</h1>
                    <p class="col-lg-10 fs-4">
                        Login with Discord to instantly start managing your bot!
                    </p>
                </div>
                <div class="col-md-10 mx-auto col-lg-5" id="loginBtnWrapper">
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

    <div class="card wrapperCard wrapp2" id="index_moduleSection">
        <div class="container col-xxl-8 px-4 py-5 infoImgWrapper hidden-element-left" id="index_inner_moduleSection">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6" id="modulesInfoTextWrapper">
                    <h1 class="display-5 fw-bold lh-1 mb-3">EXPLORE A WIDE RANGE OF MODULES</h1>
                    <p class="lead">
                    The intuitive Web Interface empowers you to browse our various modules for your server, all at your fingertips. Experience quick and effortless customization!
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
                <div class="col-10 col-sm-8 col-lg-6 indexImgWrapper" id="manageServersWrapper">
                    <img src="<?php echo $_ENV["app_root"];?>media/manageServersExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" width="500" height="300" alt="Bootstrap Themes" loading="lazy">
                </div>
            </div>
        </div>
    </div>

    <div class="card wrapperCard" id="index_recationRoleSection">
        <div class="container col-xxl-8 px-4 py-5 infoImgWrapper hidden-element-right" id="index_inner_recationRoleSection">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-10 col-sm-8 col-lg-6 indexImgWrapper" id="roleManagerWrapper">
                    <!--<img src="/quizVerwaltung/media/insertExamplePicture.png" style="border-radius: 7px;" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="700" height="500" loading="lazy">-->
                    <img src="<?php echo $_ENV["app_root"];?>media/roleManagerExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" alt="Bootstrap Themes"  loading="lazy">
                </div>
                <div class="col-lg-6" id="reactionRoleInfoTextWrapper">
                    <h1 class="display-5 fw-bold lh-1 mb-3">CREATE SELF ASSIGNABLE SERVER ROLES</h1>
                    <p class="lead">
                    Our role manager lets you create specific server roles accessible to other users, allowing each member to assign themselves a role through a simple reaction. 
                    Be ready to expect an effortlessly intuitive administration experience.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard wrapp2" id="index_welcomeMsgSection">
        <div class="container col-xxl-8 px-4 py-5 infoImgWrapper hidden-element-left"  id="index_inner_welcomeMsgSection">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6" id="welcomeMessageInfoTextWrapper">
                    <h1 class="display-5 fw-bold lh-1 mb-3">CREATE PERSONALIZED WELCOME MESSAGES</h1>
                    <p class="lead">
                    Use our greeting manager to  cast memorable welcomes:<br> personalize custom welcome messages for new users! 
                    You can freely choose between personal messages or public greetings within your discord.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
                <div class="col-10 col-sm-8 col-lg-6 indexImgWrapper" id="greetingManagerWrapper">
                    <img src="<?php echo $_ENV["app_root"];?>media/greetingManagerExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" width="500" height="300" alt="Bootstrap Themes" loading="lazy">
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard" id="index_faceitEloSection">
        <div class="container col-xxl-8 px-4 py-5 infoImgWrapper hidden-element-right" id="index_inner_faceitEloSection">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-10 col-sm-8 col-lg-6 indexImgWrapper" id="faceitEloWrapper">
                    <!--<img src="/quizVerwaltung/media/insertExamplePicture.png" style="border-radius: 7px;" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="700" height="500" loading="lazy">-->
                    <img src="<?php echo $_ENV["app_root"];?>media/faceitEloCurveExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" alt="Bootstrap Themes"  loading="lazy">
                </div>
                <div class="col-lg-6" id="faceitInfoTextWrapper">
                    <h1 class="display-5 fw-bold lh-1 mb-3">FACEIT ELO MODULE</h1>
                    <p class="lead">
                        Catering to Counter-Strike Enthusiasts: The FACEIT Elo Module allows you to analyse any player's FACEIT ELO. 
                        Share sleek graphs in any of your channels simply by typing the command /faceit_elo.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard wrapp2" id="index_pointSystemSection">
        <div class="container col-xxl-8 px-4 py-5 infoImgWrapper hidden-element-left" id="index_inner_pointSystemSection">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6" id="pointsInfoTextWrapper">
                    <h1 class="display-5 fw-bold lh-1 mb-3">EARN AND GAMBLE POINTS</h1>
                    <p class="lead">
                    Encourage Loyalty: Recognize and reward dedicated members of your community with points for their activity in voice channels. 
                    Adventurous souls may venture forth into the world of the slots to embrace a nerve wracking experience.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn btn-primary" href="#">Read more</a>
                    </div>
                </div>
                <div class="col-10 col-sm-8 col-lg-6 indexImgWrapper" id="pointSystemWrapper">
                    <img src="<?php echo $_ENV["app_root"];?>media/pointSystemExample.PNG" class="d-block mx-lg-auto img-fluid exampleImg" width="500" height="300" alt="Bootstrap Themes" loading="lazy">
                </div>
            </div>
        </div>
    </div>


    <div class="card wrapperCard wrapp2 " id="index_contactSection">
        <div class="px-4 py-5 my-5 text-center hidden-element-right" id="contactFormDivWrapper">
                <h1 class="display-5 fw-bold">CONTACT US</h1>
                <div class="col-lg-6 mx-auto" style="width: 70%;">
                <p class="lead mb-4">
                    Do you have any questions or suggestions for us? Feel free to leave us a message using the form below
                </p>
                <form id="contactForm">
                    <input type="hidden" name="method" value="sendContactForm">
                    <div class="form-group">
                        <label for="FormControlInput1" class="contactFormLabel">Your Name*</label>
                        <input type="text" class="form-control" name="name" id="FormControlInputName">
                    </div>
                    <div class="form-group">
                        <label for="FormControlInput2" class="contactFormLabel">Your Email*</label>
                        <input type="email" class="form-control" name="mailAdress" id="FormControlInputMail">
                    </div>
                    <div class="form-group">
                        <label for="FormControlTextarea3" class="contactFormLabel">Your Message*</label>
                        <textarea class="form-control" id="FormControlTextareaMessage" name="message" rows="4"></textarea>
                    </div>
                    <div id="recaptchaWrapper">
                        <div class="g-recaptcha" style="margin-top:2%;" data-sitekey="6Lc2RwQpAAAAAJZlCV5warFuvzEuVjCwIE_UbsDv"></div>
                        <label id="userEmailAgreeLabel">I agree to be contacted about the issue via the email address provided here
                            <input type="checkbox" id="userEmailAgreeCheckbox" name="userEmailAcceptance">
                        </label>
                    </div>
                    <input id="submitContactFormular" onclick="sendContactForm()" type="button" value="SEND MESSAGE" class="btn btn-success">
                </form>
            </div>
        </div>
    </div>
        <?php include_once "frontend/footer.php"; ?>
        <?php include_once "frontend/notificationToast.php"; ?>


    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function sendContactForm(){
            let name = document.getElementById("FormControlInputName");
            let mail = document.getElementById("FormControlInputMail");
            let message = document.getElementById("FormControlTextareaMessage");

            $.ajax({
                type: 'POST',
                url: "doTransaction.php",
                data: $("#contactForm").serialize(),
                success: function(response,message) {
                    console.log(response);
                    console.log(message);
                    if(response == "Thank you for your message, we will get back to you as soon as possible." || response == "Thank you for your message."){
                        document.getElementById("FormControlInputName").value = "";
                        document.getElementById("FormControlInputMail").value = "";
                        document.getElementById("FormControlTextareaMessage").value = "";
                    }
                    $(".toast").toast('show');
                    $("#toastMsgBody").html(response);
                    grecaptcha.reset();
                }
            });
            
        }
    </script>
    <script>
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        function bottomFadeInOnScroll(domElem) {
            $(domElem).each(function (i) {
                var bottom_of_object = $(this).offset().top + $(this).outerHeight()-100;
                var bottom_of_window = $(window).scrollTop() + $(window).height();
                
                /**only do if its not a mobile device */
                if(!isMobile){
                    /* If the object is completely or partially visible in the window, adjust opacity */
                    if (bottom_of_window > bottom_of_object) {
                        if (!$(this).hasClass('animated_bottomFadeIn')) {
                            $(this).addClass('animated_bottomFadeIn');
                        }
                    }
                }
            });
        }

        function leftSideFadeInOnScroll(domElem) {
            $(domElem).each(function (i) {
                var bottom_of_object = $(this).offset().top + $(this).outerHeight();
                var bottom_of_window = $(window).scrollTop() + $(window).height();
                
                /**only do if its not a mobile device */
                if(!isMobile){
                    /* If the object is completely or partially visible in the window, adjust opacity */
                    if (bottom_of_window > bottom_of_object) {
                        if (!$(this).hasClass('animated_leftSideFadeIn')) {
                            $(this).addClass('animated_leftSideFadeIn');
                        }
                    }
                }
            });
        }

        function rightSideFadeInOnScroll(domElem) {
            $(domElem).each(function (i) {
                var bottom_of_object = $(this).offset().top + $(this).outerHeight();
                var bottom_of_window = $(window).scrollTop() + $(window).height();
                
                /**only do if its not a mobile device */
                if(!isMobile){
                    /* If the object is completely or partially visible in the window, adjust opacity */
                    if (bottom_of_window > bottom_of_object) {
                        if (!$(this).hasClass('animated_rightSideFadeIn')) {
                            $(this).addClass('animated_rightSideFadeIn');
                        }
                    }
                }
            });
        }


        $(document).ready(function() {
            
            //if isMobile remove all hidden-element classes to show all divs instantly
            if(isMobile){
                document.getElementById("loginBtnWrapper").style.opacity = 1;
                document.getElementById("manageServersWrapper").style.opacity = 1;
                document.getElementById("roleManagerWrapper").style.opacity = 1;
                document.getElementById("greetingManagerWrapper").style.opacity = 1;
                document.getElementById("faceitEloWrapper").style.opacity = 1;
                document.getElementById("pointSystemWrapper").style.opacity = 1;
                document.getElementById("contactFormDivWrapper").style.opacity = 1;

                document.getElementById("loginInfoTextWrapper").style.opacity = 1;
                document.getElementById("modulesInfoTextWrapper").style.opacity = 1;
                document.getElementById("reactionRoleInfoTextWrapper").style.opacity = 1;
                document.getElementById("welcomeMessageInfoTextWrapper").style.opacity = 1;
                document.getElementById("faceitInfoTextWrapper").style.opacity = 1;
                document.getElementById("pointsInfoTextWrapper").style.opacity = 1;
            }
            
            //call the function for all divs that should be animated
            $(window).scroll( function(){
                bottomFadeInOnScroll('#loginBtnWrapper');
                leftSideFadeInOnScroll('#loginInfoTextWrapper');

                bottomFadeInOnScroll('#manageServersWrapper');
                rightSideFadeInOnScroll('#modulesInfoTextWrapper');

                bottomFadeInOnScroll('#roleManagerWrapper');
                leftSideFadeInOnScroll('#reactionRoleInfoTextWrapper');

                bottomFadeInOnScroll('#greetingManagerWrapper');
                rightSideFadeInOnScroll('#welcomeMessageInfoTextWrapper');

                bottomFadeInOnScroll('#faceitEloWrapper');
                leftSideFadeInOnScroll('#faceitInfoTextWrapper');

                bottomFadeInOnScroll('#pointSystemWrapper');
                rightSideFadeInOnScroll('#pointsInfoTextWrapper');

                bottomFadeInOnScroll('#contactFormDivWrapper');
            });
        });

    </script>

</body>




</html>