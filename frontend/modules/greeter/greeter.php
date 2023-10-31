<?php
    session_start();

    if(!isset($_SESSION["logged_in"])){
        header("Location:../../index.php");
        exit();
    }else{
        extract($_SESSION["userData"]);
        if(isset($avatar)){
            $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
        }else{
            $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
        }
    }

    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    $guild_id = $_SESSION["currentGuildId"];
    $bot_token = $_ENV["bot_token"];

    include_once "../../../services/apiRequestService.php";
    $apiRequests = new ApiRequests();
    $channels = $apiRequests->getDiscordEntity($guild_id, $bot_token, "channels");

    include_once "../../../services/dataHandler.php";
    $dataHandler = new DataHandler();
    $channelDic = $dataHandler->inputToDictionaryFilter($channels, "id", "name");


    include_once "../../../services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("guilds", "guild_id=?", [$guild_id]);
    if(empty($dbSelection)){
        $dbSelection = array();
    }else{
        $dbSelection = $dbSelection[0];
    }
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="<?php echo $_ENV["app_root"];?>frontend/modules/greeter/greeterStyles.css" rel="stylesheet">
        <link href="<?php echo $_ENV["app_root"];?>general.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <title>Greeting Message</title>
    </head>
    <body onload="enablePrivateMessage();enableGuildMessage();initialEmbedPreviewLoad();">
        <div class="allGreeterWrapper">
            <h1 id="greeter_mainHeader">Greeting Message</h1>
            <div class="card-body" id="bodyCardWrapper">
                <div class="card text-bg-dark" id="innerCardLeft">
                    <div class="card-body">

                        <div>
                            <h1 class="mainHeader">Private Message</h1>

                            <div class="moduleSwitch">
                                <label class="switch">
                                    <input id="checkboxPrivate" type="checkbox" name="changeStatus" onchange="enablePrivateMessage()" <?php if($dbSelection["private_greeting_title"] != "" ||$dbSelection["private_greeting_message"] != ""){ echo "checked";}?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="card text-bg-secondary" id="leftInnerContentBody">
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Titel</span>
                                    <input type="text" id="privateTitel" onkeyup="genEmbedPreview(this)" class="form-control" value="<?php echo $dbSelection["private_greeting_title"];?>" aria-label="Titel" aria-describedby="basic-addon1" disabled>
                                </div>

                                <label for="exampleColorInput" class="form-label" id="colorPicker"><p id="colorPickerText">Choose a color</p>
                                    <input type="color" onchange="genEmbedPreview(this)" class="form-control form-control-color" value="<?php echo $dbSelection["private_greeting_color"];?>" id="privateColor"  title="Choose your color" disabled>
                                </label>
   
                                <div class="input-group" style="min-height: 50%;">
                                    <span class="input-group-text">Message</span>
                                    <textarea class="form-control" maxlength="1000" onkeyup="genEmbedPreview(this)" id="privateMessage" aria-label="Message" disabled><?php echo $dbSelection["private_greeting_message"];?></textarea>
                                </div>

                                <p id="embedPreviewBotLine" style="display:none;"><img id="botLineImg" src="<?php echo $_ENV["app_root"]."media/bergfestBot_logo_v2.png"; ?>">BergfestBot<span id="botBadge">BOT</span><span id="botLineTime"><?php echo date("Y-m-d H:i:s");?></span></p>
                                <div class="card" id="embedPreview" style="display: none;">
                                    <div class="card-body" id="embedPreviewBody">
                                        <p id="embedPreviewTitel"></p>
                                        <p id="embedPreviewMessage"></p>
                                    </div>
                                </div>                     
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card  text-bg-dark" id="innerCardRight">
                    <div class="card-body">

                        <div>
                            <h1 class="mainHeader">Guild Message</h1>

                            <div class="moduleSwitch">
                                <label class="switch">
                                    <input id="checkboxGuild" type="checkbox" name="changeStatus" onchange="enableGuildMessage();" <?php if($dbSelection["guild_greeting_message"] != ""){ echo "checked";}?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="card text-bg-secondary" id="rightInnerContentBody">
                            <div class="card-body">

                                <select class="form-select" id="guildChannel_id" aria-label="Channel_id" style="margin-bottom: 2%;" disabled>
                                    <option selected>Select the channel for the message</option>
                                    <?php foreach($channelDic as $channelId => $channelName){ ?>
                                        <option value="<?php echo $channelId; ?>" <?php if(isset($dbSelection["greeting_channel_id"]) && $channelId == $dbSelection["greeting_channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
                                    <?php }?>
                                </select>

                                <div class="input-group" style="min-height: 76%;">
                                    <span class="input-group-text">Message</span>
                                    <textarea id="guildMessage" maxlength="250" class="form-control" aria-label="Message" disabled><?php echo $dbSelection["guild_greeting_message"];?></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card mb-3 text-bg-dark" id="variableInformationWrapper">
                <div class="card-header border-light">Discord variable informations</div>
                <div class="card-body text-secondary">
                    <h5 class="card-title">Available Variables</h5>
                    <p class="card-text">
                        <span class="discordVariable">{#channel} </span><span class="discordVariableText">Link a channel into your text. Just replace "channel" with your channel name. eg. {#welcome}</span><br>
                        <span class="discordVariable">{server} </span><span class="discordVariableText">Shows the server name in the text.</span><br>
                        <span class="discordVariable">{channel} </span><span class="discordVariableText">Shows a channel name in the text.</span><br>
                        <span class="discordVariable">{user} </span><span class="discordVariableText">Shows the user which called the command. Eg. Hello I am {user}</span><br>
                        <span class="discordVariable">{username} </span><span class="discordVariableText">Shows a username in the text. Eg. Hello my username is {username}</span><br>
                        <span class="discordVariable">{avatar} </span><span class="discordVariableText">Shows the avatar in the text. Eg. Hey, check out my new avatar {avatar}</span><br>
                        <span class="discordVariable">{&role} </span><span class="discordVariableText">Shows a role in the text. Just replace "role" with a any role your server has. Eg. We now have the role {&Members}.</span><br>
                        <span class="discordVariable">{everyone} </span><span class="discordVariableText">Mention @everyone in the text.</span><br>
                        <span class="discordVariable">{here} </span><span class="discordVariableText">Mention @here in the text.</span><br>
                    </p>
                </div>
            </div>

            <button type="button" class="btn btn-success greeter_saveAllBtn" onclick="saveAll()">Save</button>
        </div>
    </body>
</html>
<script>


    function initialEmbedPreviewLoad(){
        let privateTitel = document.getElementById("privateTitel").value;
        let privateMessage = document.getElementById("privateMessage").value;
        let privateColor = document.getElementById("privateColor").value;

        let embedPreview = document.getElementById("embedPreview");
        let embedPreviewBotLine = document.getElementById("embedPreviewBotLine");
        let embedPreviewTitel = document.getElementById("embedPreviewTitel");
        let embedPreviewMessage = document.getElementById("embedPreviewMessage");

        embedPreviewTitel.innerHTML = privateTitel;
        embedPreviewMessage.innerHTML = privateMessage;
        if(embedPreviewTitel.innerHTML != "" || embedPreviewMessage.innerHTML != ""){
            embedPreview.style.display = "block";
            embedPreviewBotLine.style.display = "block";
        }
        embedPreview.style.borderLeft = "4px solid "+privateColor;
    }
    

    function genEmbedPreview(elem){
        let embedPreview = document.getElementById("embedPreview");
        let embedPreviewBody = document.getElementById("embedPreviewBody");
        let embedPreviewBotLine = document.getElementById("embedPreviewBotLine");

        if(embedPreview.style.display == "none"){
            embedPreview.style.display = "block";
            embedPreviewBotLine.style.display = "block";
        }
        
        if(document.getElementById("embedPreviewTitel").innerHTML == "" && document.getElementById("embedPreviewMessage").innerHTML == ""){
            embedPreview.style.display = "none"; 
            embedPreviewBotLine.style.display = "none";
        }
        

        //change titel in preview
        if(elem.id == "privateTitel"){
            if(document.contains(document.getElementById("embedPreviewTitel"))){
                document.getElementById("embedPreviewTitel").remove();
            }
            let newTitel = document.createElement("p");
            newTitel.setAttribute("id","embedPreviewTitel");
            newTitel.innerHTML = elem.value;
            embedPreviewBody.prepend(newTitel);
        }

        if(elem.id == "privateColor"){
            if(document.getElementById("embedPreviewTitel") != "" || document.getElementById("embedPreviewMessage") != ""){
                 embedPreview.style.borderLeft = "4px solid "+ elem.value;
            }
        }

        //change message in preview
        if(elem.id == "privateMessage"){
            if(document.contains(document.getElementById("embedPreviewMessage"))){
                document.getElementById("embedPreviewMessage").remove();
            }
            let newMessage = document.createElement("p");
            newMessage.setAttribute("id","embedPreviewMessage");
            newMessage.innerHTML = elem.value;
            embedPreviewBody.appendChild(newMessage);
        }
    }

    function enablePrivateMessage(){
        let leftInnerContentBody = document.getElementById("leftInnerContentBody");
        let privateCheckbox = document.getElementById("checkboxPrivate");
        let privateTitel = document.getElementById("privateTitel");
        let privateColor = document.getElementById("privateColor");
        let privateMessage = document.getElementById("privateMessage");


        if(privateCheckbox.checked == true ){
            leftInnerContentBody.style.opacity = 1;
            privateTitel.disabled = false;
            privateColor.disabled = false;
            privateMessage.disabled = false;

            privateTitel.style.cursor = "inherit";
            privateColor.style.cursor = "inherit";
            privateMessage.style.cursor = "inherit";
        }else{
            leftInnerContentBody.style.opacity = 0.5;
            privateTitel.disabled = true;
            privateColor.disabled = true;
            privateMessage.disabled = true;

            privateTitel.style.cursor = "not-allowed";
            privateColor.style.cursor = "not-allowed";
            privateMessage.style.cursor = "not-allowed";
        }
    }


    function enableGuildMessage(){
        let rightInnerContentBody = document.getElementById("rightInnerContentBody");
        let checkboxGuild = document.getElementById("checkboxGuild");
        let guildChannel_id = document.getElementById("guildChannel_id");
        let guildMessage = document.getElementById("guildMessage");


        if(checkboxGuild.checked == true ){
            rightInnerContentBody.style.opacity = 1;
            guildChannel_id.disabled = false;
            guildMessage.disabled = false;

            guildChannel_id.style.cursor = "inherit";
            guildMessage.style.cursor = "inherit";
        }else{
            rightInnerContentBody.style.opacity = 0.5;
            guildChannel_id.disabled = true;
            guildMessage.disabled = true;

            guildChannel_id.style.cursor = "not-allowed";
            guildMessage.style.cursor = "not-allowed";
        }
    }


    function saveAll(){
        let checkboxPrivate = document.getElementById("checkboxPrivate").checked;
        let checkboxGuild = document.getElementById("checkboxGuild").checked;

        if(checkboxPrivate == true){
            checkboxPrivate = 1;
        }else{
            checkboxPrivate = 0;
        }

        if(checkboxGuild == true){
            checkboxGuild = 1;
        }else{
            checkboxGuild = 0;
        }

        let privateTitel = document.getElementById("privateTitel").value;
        let privateMessage = document.getElementById("privateMessage").value;
        let privateColor = document.getElementById("privateColor").value;

        let guildChannel_id = document.getElementById("guildChannel_id").value;
        let guildMessage = document.getElementById("guildMessage").value;

        $.ajax({
                type: "POST",
                url: "../../../doTransaction.php",
                data: {
                    method: "greeting_message",
                    checkboxPrivate: checkboxPrivate,
                    checkboxGuild: checkboxGuild,
                    privateTitel: privateTitel,
                    privateMessage: privateMessage,
                    privateColor: privateColor,
                    guildChannel_id: guildChannel_id,
                    guildMessage: guildMessage
                },
                success: function(response, message, result) {
                    console.log(response);
                    console.log(message);
                    console.log(result);
                }
            });   
        
    }
</script>