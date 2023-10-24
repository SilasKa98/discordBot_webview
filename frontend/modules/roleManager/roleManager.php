<?php

session_start();

$basePath = dirname(__DIR__, 3);
require $basePath.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

if(!isset($_SESSION["logged_in"])){
    header("Location:../../index.php");
}else{
    extract($_SESSION["userData"]);
    if(isset($avatar)){
        $avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";  
    }else{
        $avatar_url = $_ENV["app_root"]."media/profileDefault_avatar.png";
    }
}

$guild_id = $_SESSION["currentGuildId"];
$bot_token = $_ENV["bot_token"];

include_once "../../../services/apiRequestService.php";
$apiRequests = new ApiRequests();
$roles = $apiRequests->getDiscordEntity($guild_id, $bot_token, "roles");
$channels = $apiRequests->getDiscordEntity($guild_id, $bot_token, "channels");
$emojis = $apiRequests->getDiscordEntity($guild_id, $bot_token, "emojis");


//check for too many requests and then send user back to serverSettings.php page
if(isset($roles["message"]) || isset($channels["message"])|| isset($emojis["message"])){
    header("Location:". $_ENV["app_root"]."frontend/serverSettings.php?error=tooManyRequests&guildId=".$guild_id);
    exit();
}

include_once "../../../services/dataHandler.php";
$dataHandler = new DataHandler();
$roleDic = $dataHandler->inputToDictionaryFilter($roles, "position", "name");
$roleIdDic = $dataHandler->inputToDictionaryFilter($roles, "position", "id");
$channelDic = $dataHandler->inputToDictionaryFilter($channels, "id", "name");

//sorting roles Dic by position (key)
ksort($roleDic);
ksort($roleIdDic);
$bergfestBotRoleKey = $dataHandler->getArrayKeyByValue($roleDic, "BergfestBot");
$slicedRoleDic = array_slice($roleDic, 0, $bergfestBotRoleKey);
$slicedRoleIdDic = array_slice($roleIdDic, 0, $bergfestBotRoleKey);

include_once "../../../services/databaseService.php";
$databaseService = new DatabaseService;

$dbSelection = $databaseService->selectData("reaction_messages", "guild_id=?", [$guild_id]);
if(empty($dbSelection)){
    $dbSelection = array();
    $dbSelection_messages  = array();
}else{
   $dbSelection_messages = $dbSelection;
   $dbSelection = $dbSelection[0]; 
}


//set reaction_messages_id here when user clicked on the discord message
//search in db for the line with the selected id
if(isset($_POST["sel_reaction_message_id"])){
    $sel_reaction_message_id = $_POST["sel_reaction_message_id"];

    $dbSelection_specific_message = $databaseService->selectData("reaction_messages", "reaction_message_id=?", [$sel_reaction_message_id]);
    if(empty($dbSelection_specific_message)){
        $dbSelection_specific_message = array();
    }else{
        $dbSelection_specific_message = $dbSelection_specific_message[0];
    }

}




/*
$dbSelection_r_messages = $databaseService->selectData("reaction_messages as m left join reaction_roles as r on (m.reaction_messages_id = r.reaction_messages_id)", "m.guild_id=?", [$guild_id]);
if(empty($dbSelection)){
    $dbSelection_r_messages = array();
}else{
   $dbSelection_r_messages = $dbSelection_r_messages[0]; 
}*/

if(isset($sel_reaction_message_id)){
    $dbSelection_r_roles = $databaseService->selectData("reaction_roles", "reaction_message_id=?", [$sel_reaction_message_id]);
    if(empty($dbSelection_r_roles)){
        $dbSelection_r_roles = array();
    }
}else{
    $dbSelection_r_roles = array();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $_ENV["app_root"];?>general.css">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Manage Roles</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    
</head>
<body id="roleManagerBody">


<?php include_once "../../navbar.php"; ?>

    <input type="hidden" id="slicedRoleIdDic_hidden" value="<?php echo implode(",",$slicedRoleIdDic); ?>">
    <input type="hidden" id="slicedRoleDic_hidden"  value="<?php echo implode(",",$slicedRoleDic); ?>">
    <input type="hidden" id="current_reaction_message_id" name="current_reaction_message_id" value="<?php if(isset($sel_reaction_message_id)){ echo $sel_reaction_message_id; }?>">

<h1 id="faceitEloCheckerHeader">Role Manager</h1>  
<button type="button" class="btn btn-outline-danger delWholeMessageBtn" onclick="deleteMessage()">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"></path>
        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"></path>
    </svg>
    Delete Message
</button>
<main id="roleManagerMain">  
    <div class="d-flex flex-column align-items-stretch flex-shrink-0 p-1 text-white bg-dark" id="sideBarWrapper" style="width: 300px;">
        <span class="fs-5 fw-semibold" style="color:white;text-align:center;padding:1%;margin-bottom: 6%;">Reaction Messages</span>
        <div class="list-group list-group-flush border-bottom scrollarea">
            <?php for($i=0;$i<count($dbSelection_messages);$i++){?>
                <form action="roleManager.php" method="post" class="formRoleMessageWrapper">
                    <input type="hidden" name="sel_reaction_message_id" value="<?php echo $dbSelection_messages[$i]["reaction_message_id"];?>">
                    <button type="submit" class="list-group-item list-group-item-action py-3 text-bg-secondary roleMsgSidebarBtn" aria-current="true">
                        <div class="d-flex w-100 align-items-center justify-content-between">
                            <strong class="mb-1"><?php echo $dbSelection_messages[$i]["message"]; ?></strong>
                            <small><?php if($dbSelection_messages[$i]["channel_id"] != ""){ echo $channelDic[$dbSelection_messages[$i]["channel_id"]];}?></small>
                        </div>                       
                    </button>
                </form>
            <?php }?>
        </div>
        <form action="roleManager.php" method="post">
            <button type="submit" class="btn btn-success addNewMsgBtn">Add new Message</button>
        </form>
    </div>
    

    <div class="container-fluid">
        <div id="eloCheckerWrapper">
            <form action="<?php echo $_ENV["app_root"];?>doTransaction.php" method="POST">
                <input type="hidden" name="method" value="reaction_role2">
                    <input type="hidden" name="sel_reaction_message_id" value="<?php if(isset($sel_reaction_message_id)){ echo $sel_reaction_message_id; }?>">
                    <div class="card text-bg-dark mb-3">

                        <div class="card-body">
                            <h5 class="card-title">Role Selection Channel</h5>
                            <select class="form-select text-bg-secondary" name="reaction_role_channel" aria-label="Update Channel" required>
                                <option selected>Open this select menu</option>
                                <?php foreach($channelDic as $channelId => $channelName){ ?>
                                        <option value="<?php echo $channelId; ?>" <?php if(isset($dbSelection_specific_message["channel_id"]) && $channelId == $dbSelection_specific_message["channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
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

                    
                    <div class="card text-bg-dark mb-3">

                        <div class="card-body" id="RoleContentWrapper">
                            <h5 class="card-title">Header Message</h5>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Personal text: </span>
                                <input type="text" class="form-control" value="<?php if(isset($dbSelection_specific_message["message"])){ echo $dbSelection_specific_message["message"]; }?>" name="mainHeaderText" placeholder="Hey @everyone Select your favorit games below!" aria-describedby="basic-addon1" required>
                            </div>    
                            
                            <?php for($x=0;$x<count($dbSelection_r_roles);$x++){?> 
                                <div class="input-group mb-3 roleDisplay">
                                    <div class="emojiSelWrapper">
                                        <input type="text" class="form-control selectedEmoji" value="<?php if(isset($dbSelection_r_roles[$x])){  echo utf8_decode($dbSelection_r_roles[$x]["emoji"]); } ?>" minlength="1" maxlength="1" name="emoji[]">
                                        <button class="openEmojiPickerBtn" type="button" onclick="handleEmojiPicker(this)">😀</button>
                                        <emoji-picker class="emojiPicker" style="display:none;"></emoji-picker>
                                    </div>
                                    <div class="descWrapper">
                                        <input type="text" class="form-control" name="roleDescription[]" value="<?php if(isset($dbSelection_r_roles[$x])){ echo $dbSelection_r_roles[$x]["description"]; } ?>" id="roleDescription" placeholder="Description" aria-describedby="basic-addon1">
                                    </div>
                                    <div class="roleSelWrapper">
                                        <select class="form-select roleSelection" name="roleSelection[]" aria-label="Default select example">
                                            <option selected>Open this select menu</option>
                                            <?php for($i=0;$i<count($slicedRoleDic);$i++){ ?>
                                                    <option value="<?php  echo $slicedRoleIdDic[$i]; ?>" <?php if(isset($dbSelection_r_roles[$x]) && $dbSelection_r_roles[$x]["role_id"] == $slicedRoleIdDic[$i]){ echo "selected"; } ?>><?php echo $slicedRoleDic[$i]; ?></option>
                                            <?php  }?>
                                        </select>
                                    </div>
                                    <div class="delWrapper">
                                        <button type="button" class="btn btn-outline-danger" onclick="delRole(<?php echo $dbSelection_r_roles[$x]['reaction_role_id']; ?>)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"></path>
                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            <?php }?>

                        </div>
                        <div><button type="button" class="btn btn-success addNewRoleLineBtn" onclick="addAddRoleHTML()">+</button></div>       
                        <div class="card-footer bg-transparent border-secondary">
                            <p class="card-text">
                                <small class="text-body-dark smallDescText">
                                    &#9432; Description
                                </small>
                            </p>
                        </div>

                    </div>
                    

                <hr>
            
                    <br>
                    
                        <button class="btn btn-success saveAllRoleBtn" type="submit" >Save</button>
                   
                    <br>
            </form>
        </div>
    </main>
</body>
</html>

<!--<script src="https://twemoji.maxcdn.com/v/13.1.0/twemoji.min.js" integrity="sha384-gPMUf7aEYa6qc3MgqTrigJqf4gzeO6v11iPCKv+AP2S4iWRWCoWyiR+Z7rWHM/hU" crossorigin="anonymous"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>-->


<script src="<?php echo $_ENV["app_root"];?>twemoji.min.js" integrity="sha384-gPMUf7aEYa6qc3MgqTrigJqf4gzeO6v11iPCKv+AP2S4iWRWCoWyiR+Z7rWHM/hU" crossorigin="anonymous"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>


<script>

    function addAddRoleHTML(){
        //add fields here .. for role selection -> get them with getelementByid or smth. and then insert them into the new elem...
        let roleContentWrapper = document.getElementById("RoleContentWrapper");

        //create general Wrapper elem.
        let roleDisplayWrapper = document.createElement("div");
        roleDisplayWrapper.classList.add("input-group");
        roleDisplayWrapper.classList.add("mb-3");
        roleDisplayWrapper.classList.add("roleDisplay");
        

        //create first Wrapper div for emojis
        let emojiSelWrapper = document.createElement("div");
        emojiSelWrapper.classList.add("emojiSelWrapper");
        roleDisplayWrapper.appendChild(emojiSelWrapper);

        //create input inside of emojiSelWrapper
        let selectedEmoji = document.createElement("input");
        selectedEmoji.classList.add("form-control");
        selectedEmoji.classList.add("selectedEmoji");
        selectedEmoji.setAttribute("type", "text");
        selectedEmoji.setAttribute("minlength", "1");
        selectedEmoji.setAttribute("maxlength", "1");
        selectedEmoji.setAttribute("name", "emoji[]");
        emojiSelWrapper.appendChild(selectedEmoji);

        //create button inside of emojiSelWrapper
        let openEmojiPickerBtn = document.createElement("button");
        openEmojiPickerBtn.classList.add("openEmojiPickerBtn");
        openEmojiPickerBtn.setAttribute("type", "button");
        openEmojiPickerBtn.setAttribute("onclick", "handleEmojiPicker(this)");
        openEmojiPickerBtn.innerHTML = "😀";
        emojiSelWrapper.appendChild(openEmojiPickerBtn);

        //create emojiPicker inside of emojiSelWrapper
        let emojiPicker = document.createElement("emoji-picker");
        emojiPicker.classList.add("emojiPicker");
        emojiPicker.setAttribute("style", "display:none;");
        emojiSelWrapper.appendChild(emojiPicker);


        //create second Wrapper div for role description
        let descWrapper = document.createElement("div");
        descWrapper.classList.add("descWrapper");
        roleDisplayWrapper.appendChild(descWrapper);

        //create input inside of descWrapper
        let roleDescription = document.createElement("input");
        roleDescription.classList.add("form-control");
        roleDescription.setAttribute("type", "text");
        roleDescription.setAttribute("name", "roleDescription[]");
        roleDescription.setAttribute("id", "roleDescription");
        roleDescription.setAttribute("placeholder", "Description");
        descWrapper.appendChild(roleDescription);


        //create third Wrapper div for role select dropdown 
        let roleSelWrapper = document.createElement("div");
        roleSelWrapper.classList.add("roleSelWrapper");
        roleDisplayWrapper.appendChild(roleSelWrapper);


        //create select inside of roleSelWrapper
        let roleSelection = document.createElement("select");
        roleSelection.classList.add("form-select");
        roleSelection.classList.add("roleSelection");
        roleSelection.setAttribute("name", "roleSelection[]");
        roleSelection.setAttribute("aria-label", "Default select example");
        roleSelWrapper.appendChild(roleSelection);



        //create default option manually
        let defaultOption = document.createElement("option");
        defaultOption.setAttribute("selected", "");
        defaultOption.innerHTML = "Open this select menu";
        roleSelection.appendChild(defaultOption);


        //create all other options dynamically, depending on the available roles
        let allAvailableRoles = document.getElementById("slicedRoleDic_hidden").value;
        let allAvailableRoleIds = document.getElementById("slicedRoleIdDic_hidden").value;
        allAvailableRoles = allAvailableRoles.split(",");
        allAvailableRoleIds = allAvailableRoleIds.split(",");
        console.log(allAvailableRoles);
        for(let i=0;i<allAvailableRoles.length;i++){
            let option = document.createElement("option");
            option.setAttribute("value", allAvailableRoleIds[i]);
            option.innerHTML = allAvailableRoles[i];
            roleSelection.appendChild(option);
        }

        //append the roleDisplayWrapper with all created contents to the roleContentWrapper
        roleContentWrapper.appendChild(roleDisplayWrapper);
    }


    function deleteMessage(){
        let current_reaction_message_id = document.getElementById("current_reaction_message_id").value;
        console.log(current_reaction_message_id);
        if(current_reaction_message_id != ""){
            $.ajax({
                type: "POST",
                url: "../../../doTransaction.php",
                data: {
                    method: "delete_reaction_message",
                    reaction_message_id: current_reaction_message_id
                },
                success: function(response, message, result) {
                    console.log(response);
                    console.log(message);
                    console.log(result);
                    //location.reload();
                }
            });                                                                                                                                                                                                                                                                                                                                             
        }
    }


    function delRole(id){
        $.ajax({
            type: "POST",
            url: "../../../doTransaction.php",
            data: {
                method: "del_reaction_role2",
                id: id
            },
            success: function(response, message, result) {
                console.log(response);
                console.log(message);
                console.log(result);
                //location.reload();
            }
        });
    }

    function handleEmojiPicker(elem){
        const picker = elem.nextElementSibling;
        if(picker.style.display == "none"){
            picker.style.display = "block";
        }else{
            picker.style.display = "none";
        }


         // Event Listener für das emoji-click-Event hinzufügen -> einfügen in input feld
        picker.addEventListener('emoji-click', (event) => {
            let emojiPickerTarget = event.target.previousElementSibling.previousElementSibling;
            const selectedEmoji = event.detail.emoji;
            emojiPickerTarget.value = selectedEmoji.unicode;
            picker.style.display = "none";
            //document.getElementById("selectedEmoji").value = selectedEmoji.unicode;
        });

        picker.addEventListener("blur", (event) => {
            picker.style.display = "none";
        });

        // Adjust twemoji styles
        const style = document.createElement('style')
        style.textContent = `.twemoji {
            width: var(--emoji-size);
            height: var(--emoji-size);
            pointer-events: none;
        }`
        picker.shadowRoot.appendChild(style)

        const observer = new MutationObserver(() => {
            for (const emoji of picker.shadowRoot.querySelectorAll('.emoji')) {
                // Avoid infinite loops of MutationObserver
                if (!emoji.querySelector('.twemoji')) {
                // Do not use default 'emoji' class name because it conflicts with emoji-picker-element's
                twemoji.parse(emoji, { className: 'twemoji' })
                //console.log(twemoji);

                }
            }
        })
        observer.observe(picker.shadowRoot, {
            subtree: true,
            childList: true
        })
    }
</script>
