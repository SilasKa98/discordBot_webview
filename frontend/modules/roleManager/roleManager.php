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
}else{
   $dbSelection = $dbSelection[0]; 
}

/*
$dbSelection_r_messages = $databaseService->selectData("reaction_messages as m left join reaction_roles as r on (m.reaction_messages_id = r.reaction_messages_id)", "m.guild_id=?", [$guild_id]);
if(empty($dbSelection)){
    $dbSelection_r_messages = array();
}else{
   $dbSelection_r_messages = $dbSelection_r_messages[0]; 
}*/


$dbSelection_r_roles = $databaseService->selectData("reaction_roles", "reaction_messages_id=?", [$dbSelection["reaction_messages_id"]]);
if(empty($dbSelection_r_roles)){
    $dbSelection_r_roles = array();
}

print_r($dbSelection_r_roles);


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
<body>

<?php include_once "../../navbar.php"; ?>
    <div class="container-fluid">
        <div id="eloCheckerWrapper">
            <h1 id="faceitEloCheckerHeader">Role Manager</h1>
            <form action="<?php echo $_ENV["app_root"];?>doTransaction.php" method="POST">
                <input type="hidden" name="method" value="reaction_role">
            
                    <div class="card text-bg-dark mb-3">

                        <div class="card-body">
                            <h5 class="card-title">Role Selection Channel</h5>
                            <select class="form-select text-bg-secondary" name="reaction_role_channel" aria-label="Update Channel">
                                <option selected>Open this select menu</option>
                                <?php foreach($channelDic as $channelId => $channelName){ ?>
                                        <option value="<?php echo $channelId; ?>" <?php if($channelId == $dbSelection["channel_id"]){ echo "selected";} ?>><?php echo $channelName;?></option>
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
                                <input type="text" class="form-control" value="<?php echo $dbSelection["message"]; ?>" name="mainHeaderText" placeholder="Hey @everyone Select your favorit games below!" aria-label="Username" aria-describedby="basic-addon1">
                            </div>    
                            
                            <?php for($x=0;$x<count($dbSelection_r_roles);$x++){?> 
                            <div class="input-group mb-3 roleDisplay">
                                <div class="emojiSelWrapper">
                                    <input type="text" class="form-control selectedEmoji" value="<?php if(isset($dbSelection_r_roles[$x])){  echo $dbSelection_r_roles[$x]["emoji"]; } ?>" minlength="1" maxlength="1" name="emoji[]">
                                    <button class="openEmojiPickerBtn" type="button" onclick="handleEmojiPicker(this)">😀</button>
                                    <emoji-picker class="emojiPicker" style="display:none;"></emoji-picker>
                                </div>
                                <div class="descWrapper">
                                    <input type="text" class="form-control" name="roleDescription[]" value="<?php if(isset($dbSelection_r_roles[$x])){ echo $dbSelection_r_roles[$x]["description"]; } ?>" id="roleDescription" placeholder="Description" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                                <div class="roleSelWrapper">
                                    <select class="form-select" name="roleSelection[]" aria-label="Default select example">
                                        <option selected>Open this select menu</option>
                                        <?php for($i=0;$i<count($slicedRoleDic);$i++){ ?>
                                                <option value="<?php  echo $slicedRoleIdDic[$i]; ?>" <?php if(isset($dbSelection_r_roles[$x]) && $dbSelection_r_roles[$x]["role_id"] == $slicedRoleIdDic[$i]){ echo "selected"; } ?>><?php echo $slicedRoleDic[$i]; ?></option>
                                        <?php  }?>
                                    </select>
                                </div>
                            </div>
                            <?php }?>

                        </div>
                        <div><button type="button" onclick="addAddRoleHTML()">+</button></div>       
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
                    
                        <button class="btn btn-success" type="submit" style="width: 98%; margin-left: 1%;">Save</button>
                   
                    <br>
            </form>
        </div>

</body>
</html>

<!--<script src="https://twemoji.maxcdn.com/v/13.1.0/twemoji.min.js" integrity="sha384-gPMUf7aEYa6qc3MgqTrigJqf4gzeO6v11iPCKv+AP2S4iWRWCoWyiR+Z7rWHM/hU" crossorigin="anonymous"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>-->


<script src="<?php echo $_ENV["app_root"];?>twemoji.min.js" integrity="sha384-gPMUf7aEYa6qc3MgqTrigJqf4gzeO6v11iPCKv+AP2S4iWRWCoWyiR+Z7rWHM/hU" crossorigin="anonymous"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>


<script>

    function addAddRoleHTML(){
        //add fields here .. for role selection -> get them with getelementByid or smth. and then insert them into the new elem...

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
