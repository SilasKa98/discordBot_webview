<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include_once "services/databaseService.php";
include_once "services/sanitiseInputService.php";
include_once "services/oAuth/oAuthService.php";
$databaseService = new DatabaseService;
$sanitiser = new SanitiseInputService;
$oAuthService = new oAuthService;

if(isset($_POST["method"]) && $_POST["method"] == "logoutAccount"){
    session_start();
    session_destroy();

    //delete cookies
    
    setcookie("logged_in", "", time()-3600);
    setcookie("userData", "", time()-3600);
    setcookie("serverNames", "", time()-3600);
    setcookie("serverIcons", "", time()-3600);
    setcookie("guildIds", "", time()-3600);
    setcookie("guildOwnerStatus", "", time()-3600);
    setcookie("guildPermissions", "", time()-3600);

    header("Location: index.php?logout=success");
    exit();
}


if(isset($_POST["method"]) && $_POST["method"] == "changeGeneralSettings"){
    session_start();

    $guild_id = $_SESSION["currentGuildId"];
    $adminId = $sanitiser->sanitiseInput($_POST["adminId"]);

    $dbSelection = $databaseService->selectData("guilds", "guild_id=?", [$guild_id]);

    //insert or update in server_settings
    if(empty($dbSelection)){
        //insert server settings
        $data = array("guild_id" => $guild_id, "admin_role_id" => $adminId);
        $types = "ii";
        $databaseService->insertData("guilds", $data, $types);
    }else{
        //update
        $data = array("admin_role_id" => $adminId);
        $condition = "guild_id=?";
        $params = [$guild_id];
        $types = "ii";
        $databaseService->updateData("guilds", $data, $condition, $params, $types);
    }

    $activityMessage = "Updated the general settings: changed Admin ID to ".$adminId;
    //insert into activitys
    $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
    $databaseService->insertData("activities", $activity, "isss");

    header("Location:frontend/serverSettings.php?guildId={$guild_id}&insert=success");
}


if(isset($_POST["method"]) && $_POST["method"] == "changeModulStatus"){
    session_start();
    $guild_id = $_SESSION["currentGuildId"];

    $modulName = $sanitiser->sanitiseInput($_POST["moduleName"]);
    $moduleStatus = $sanitiser->sanitiseInput($_POST["moduleStatus"]);
    $allowedModules = explode(",",$_ENV["active_modules"]);

    // needs to be checked again. Not working properly WIP
    if(!in_array($modulName,$allowedModules)){
        print "illegal Paramas submitted!";
        exit();
    }

    $dbSelection = $databaseService->selectData("guilds", "guild_id=?", [$guild_id]);
    //update
    $data = array($modulName => $moduleStatus);
    $condition = "guild_id=?";
    $params = [$guild_id];
    $types = "ii";
    $databaseService->updateData("guilds", $data, $condition, $params, $types);


    $activityMessage = "";
    if($moduleStatus == 1){
        $activityMessage = "enabled the module ".$modulName;
    }else{
        $activityMessage = "disabled the module ".$modulName;
    }

    //insert into activitys
    $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
    $databaseService->insertData("activities", $activity, "isss");
}


//Send data to endpoint
if(isset($_POST["method"]) && $_POST["method"] == "reaction_role2"){
    session_start();
    $guild_id = $_SESSION["currentGuildId"];
    $getGuildId = $sanitiser->sanitiseInput($_POST["getGuildId"]);

    $sanitiser->guildIdValidator($getGuildId, $guild_id);

    $allPostData = [];
    $channel_id = $sanitiser->sanitiseInput($_POST["reaction_role_channel"]);
    $message = $sanitiser->sanitiseInput($_POST["mainHeaderText"]);
    $sel_reaction_message_id = $sanitiser->sanitiseInput($_POST["sel_reaction_message_id"]);
    $roleDescriptions = $_POST["roleDescription"];
    $roleSelections_id = $_POST["roleSelection"];
    $emojis = $_POST["emoji"];

    $roleContent = [];
    for($i=0;$i<count($roleDescriptions);$i++){
       $tempRoleArray = [
                            "description" => $roleDescriptions[$i],
                            "role_id" => $roleSelections_id[$i],
                            "emojis" => $emojis[$i]
                        ];
        array_push($roleContent,$tempRoleArray);
    }

    $allPostData = [
        "reaction_message_id"=>$sel_reaction_message_id,
        "guild_id"=>$guild_id,
        "channel_id"=>$channel_id,
        "message"=>$message,
        "roles"=> $roleContent

    ];

    //unset it if its empty (new entry)
    if($allPostData["reaction_message_id"] == ""){
        unset($allPostData["reaction_message_id"]);
    }


    $jsonData = json_encode($allPostData);

    $url = $_ENV["endpoint_id"].":".$_ENV["endpoint_port"]."/set_reaction_role_message";
    print "<br>".$url."<br>";
    $bot_token = $_ENV["bot_token"];
    $options = [
        CURLOPT_URL=>$url,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_CUSTOMREQUEST => "POST", 
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER=>[
                                "Authorization: Bot {$bot_token}",
                                "Content-Type: application/json"
                            ]
    ];

    $oAuthService->doCurl($options);


    $activityMessage = "changed the reaction role settings";
    //insert into activitys
    $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
    $databaseService->insertData("activities", $activity, "isss");
    
    header("Location:frontend/modules/roleManager/roleManager.php?insert=success");
}



if(isset($_POST["method"]) && $_POST["method"] == "del_reaction_role2"){
    $id = $sanitiser->sanitiseInput($_POST["id"]);
    $getGuildId = $sanitiser->sanitiseInput($_POST["getGuildId"]);
    session_start();
    $guild_id = $_SESSION["currentGuildId"];

    $sanitiser->guildIdValidator($getGuildId, $guild_id);


    $dbSelection = $databaseService->selectData("reaction_messages as m left join reaction_roles as r on (m.reaction_message_id = r.reaction_message_id)", "reaction_role_id=?", [$id]);
    
    if($dbSelection[0]["guild_id"] == $guild_id){
        
        $allPostData = [
            "reaction_role_id" => $id
        ];
    
        $jsonData = json_encode($allPostData);

        $url = $_ENV["endpoint_id"].":".$_ENV["endpoint_port"]."/remove_reaction_role";
        $bot_token = $_ENV["bot_token"];
        $options = [
            CURLOPT_URL=>$url,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_CUSTOMREQUEST => "POST", 
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER=>[
                                    "Authorization: Bot {$bot_token}",
                                    "Content-Type: application/json"
                                ]
        ];
    
        $oAuthService->doCurl($options);

        $activityMessage = "deleted a reaction role";
        //insert into activitys
        $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
        $databaseService->insertData("activities", $activity, "isss");

    }else{
        print "Error: Illegal Id submitted! (ID does not belong to your guild)";
        exit();
    }
}



if(isset($_POST["method"]) && $_POST["method"] == "delete_reaction_message"){
    $getGuildId = $sanitiser->sanitiseInput($_POST["getGuildId"]);
    $reaction_message_id = $sanitiser->sanitiseInput($_POST["reaction_message_id"]);
    session_start();
    $guild_id = $_SESSION["currentGuildId"];

    $sanitiser->guildIdValidator($getGuildId, $guild_id);


    $dbSelection = $databaseService->selectData("reaction_messages", "reaction_message_id=?", [$reaction_message_id]);

    if($dbSelection[0]["guild_id"] == $guild_id){

        $allPostData = [
            "reaction_message_id" => $reaction_message_id
        ];
    
        $jsonData = json_encode($allPostData);

        $url = $_ENV["endpoint_id"].":".$_ENV["endpoint_port"]."/delete_reaction_role_message";
        $bot_token = $_ENV["bot_token"];
        $options = [
            CURLOPT_URL=>$url,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_CUSTOMREQUEST => "POST", 
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER=>[
                                    "Authorization: Bot {$bot_token}",
                                    "Content-Type: application/json"
                                ]
        ];
    
        $oAuthService->doCurl($options);

        $activityMessage = "deleted a reaction role message";
        //insert into activitys
        $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
        $databaseService->insertData("activities", $activity, "isss");
    }
}



if(isset($_POST["method"]) && $_POST["method"] == "greeting_message"){
    $getUrlGuildId = $sanitiser->sanitiseInput($_POST["getUrlGuildId"]);
    session_start();
    $guild_id = $_SESSION["currentGuildId"];

    $sanitiser->guildIdValidator($getUrlGuildId, $guild_id);
    
    $checkboxPrivate = $sanitiser->sanitiseInput($_POST["checkboxPrivate"]);
    $checkboxGuild = $sanitiser->sanitiseInput($_POST["checkboxGuild"]);
    $privateTitel = $sanitiser->sanitiseInput($_POST["privateTitel"]);
    $privateMessage = $sanitiser->sanitiseInput($_POST["privateMessage"]);
    $privateColor = $sanitiser->sanitiseInput($_POST["privateColor"]);
    $guildChannel_id = $sanitiser->sanitiseInput($_POST["guildChannel_id"]);
    $guildMessage = $sanitiser->sanitiseInput($_POST["guildMessage"]);

    //convert color from hex to rgb 
    $convertedPrivateColor = hexdec(substr($privateColor, 1));
    

    //unset values if checkbox is handed over with value 0 
    if($checkboxPrivate == 0){
        $privateTitel = "";
        $privateMessage = "";
        $privateColor = 0;
    }

    if($checkboxGuild == 0){
        $guildChannel_id = 0;
        $guildMessage = "";
    }


    //db update
    $data = array("greeting_channel_id" => $guildChannel_id, "private_greeting_message" => $privateMessage, "guild_greeting_message" => $guildMessage, "private_greeting_color" => $convertedPrivateColor, "private_greeting_title" => $privateTitel);
    $condition = "guild_id=?";
    $params = [$guild_id];
    $types = "issisi";
    $databaseService->updateData("guilds", $data, $condition, $params, $types);

    $activityMessage = "updated the greeting messages";
    //insert into activitys
    $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
    $databaseService->insertData("activities", $activity, "isss");
}

if(isset($_POST["method"]) && $_POST["method"] == "payment_per_minute"){

    $getUrlGuildId = $sanitiser->sanitiseInput($_POST["getGuildId"]);
    
    $ppm = $sanitiser->sanitiseInput($_POST["ppm"]);
    session_start();
    $guild_id = $_SESSION["currentGuildId"];

    $sanitiser->guildIdValidator($getUrlGuildId, $guild_id);


    $dbSelection = $databaseService->selectData("guilds", "guild_id=?", [$guild_id]);

    if($dbSelection[0]["guild_id"] == $guild_id){
        //db update
        $data = array("payment_per_minute" => $ppm);
        $condition = "guild_id=?";
        $params = [$guild_id];
        $types = "ii";
        $databaseService->updateData("guilds", $data, $condition, $params, $types);


        $activityMessage = "updated the Point System settings.";
        //insert into activitys
        $activity = array("guild_id" =>$guild_id, "author" => $_SESSION["userData"]["name"], "action" => $activityMessage, "date"=> date("Y-m-d H:i:s"));
        $databaseService->insertData("activities", $activity, "isss");
    }
}

if(isset($_POST["method"]) && $_POST["method"] == "sendContactForm"){
    include_once "services/mailService.php";
    $mailService = new MailService;

    $mailAdress = $sanitiser->sanitiseInput($_POST["mailAdress"]);
    $name = $sanitiser->sanitiseInput($_POST["name"]);
    $message = $sanitiser->sanitiseInput($_POST["message"]);

    if(strlen($name) == 0 || strlen($message) == 0 || strlen($mailAdress) == 0){
        print "Please fill in all required fields!";
        exit();
    }elseif(!filter_var($mailAdress, FILTER_VALIDATE_EMAIL)){
        print "Please enter a valid mail address!";
        exit();
    }else{

        print "Thank you for your message, we will get back to you as soon as possible.";
        $mailService->sendContactMail($mailAdress, $name, $message); 
    }

    
}



if(isset($_POST["method"]) && $_POST["method"] == "acceptCookies"){
    session_start();
    /*
    $username = $_SESSION["userData"]["name"];
    $userId = $_SESSION["userData"]["discord_id"];
    $avatar = $_SESSION["userData"]["avatar"];
    $logged_in = $_SESSION["logged_in"];
    setcookie('name', $username, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('discord_id', $userId, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('avatar', $avatar, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('logged_in', $logged_in, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    */
    /*
    $userData = serialize($_SESSION["userData"]);
    $logged_in = $_SESSION["logged_in"];
    $serverNames = serialize($_SESSION["userServerData"]["serverNames"]);
    $serverIcons = serialize($_SESSION["userServerData"]["serverIcons"]);
    $guildIds = serialize($_SESSION["userServerData"]["guildIds"]);
    $guildOwnerStatus = serialize($_SESSION["userServerData"]["guildOwnerStatus"]);
    $guildPermissions = serialize($_SESSION["userServerData"]["guildPermissions"]);
    
    setcookie('logged_in', $logged_in, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('userData', $userData, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('serverNames', $serverNames, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('serverIcons', $serverIcons, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('guildIds', $guildIds, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('guildOwnerStatus', $guildOwnerStatus, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    setcookie('guildPermissions', $guildPermissions, time() + 3600 * 24 * 30); // Gültig für 30 Tage
    */
}

/*
if(isset($_POST["method"]) && $_POST["method"] == "reaction_role"){

    //TODO
    //sanitse and check if roleSelections_id contains 2 same values if so, error and back.


    $channel_id = $sanitiser->sanitiseInput($_POST["reaction_role_channel"]);
    $message_id = 12314142;
    $message = $sanitiser->sanitiseInput($_POST["mainHeaderText"]);

    $roleDescriptions = $_POST["roleDescription"];
    $roleSelections_id = $_POST["roleSelection"];
    $emojis = $_POST["emoji"];


    session_start();
    $guild_id = $_SESSION["currentGuildId"];




    //insert or update the reaction_messages table
    $dbSelection = $databaseService->selectData("reaction_messages", "guild_id=?", [$guild_id]);

    if(empty($dbSelection)){
        //insert reaction_message
        $data = array("guild_id" => $guild_id, "channel_id" => $channel_id, "message_id" => $message_id, "message" => $message);
        $types = "iiis";
        $databaseService->insertData("reaction_messages", $data, $types);
    }else{
        //update
        $data = array("channel_id" => $channel_id, "message_id" => $message_id, "message" => $message );
        $condition = "guild_id=?";
        $params = [$guild_id];
        $types = "iisi";
        $databaseService->updateData("reaction_messages", $data, $condition, $params, $types);

    }

    //get the current reaction_messages_id for the current guild
    $reaction_messages_id = $databaseService->selectData("reaction_messages", "guild_id=?", [$guild_id]);
    $reaction_messages_id = $reaction_messages_id[0]["reaction_message_id"];

    print_r($reaction_messages_id);
    //del all from reaction_roles table what belongs to reaction_message_id of this guild (to understand check the relation to reaction_messages table)
    $databaseService->deleteData("reaction_roles", $reaction_messages_id, "reaction_message_id");
    //insert into reaction_roles table
    for($i=0;$i<count($roleDescriptions);$i++){
        $emojis[$i] = utf8_encode($emojis[$i]);
        $data = array("reaction_message_id" => $reaction_messages_id, "role_id" => $roleSelections_id[$i], "emoji" => $emojis[$i], "description" => $roleDescriptions[$i]);
        $types = "iiss";
        $databaseService->insertData("reaction_roles", $data, $types);
    }

    header("Location:frontend/modules/roleManager/roleManager.php?insert=success");
}

if(isset($_POST["method"]) && $_POST["method"] == "del_reaction_role"){
    $id = $sanitiser->sanitiseInput($_POST["id"]);
    session_start();
    $guild_id = $_SESSION["currentGuildId"];


    $dbSelection = $databaseService->selectData("reaction_messages as m left join reaction_roles as r on (m.reaction_message_id = r.reaction_message_id)", "reaction_role_id=?", [$id]);
    
    print_r($dbSelection);
    if($dbSelection[0]["guild_id"] == $guild_id){
        $databaseService->deleteData("reaction_roles", $id, "reaction_role_id");
        print "Successfully deleted the selected role";
    }else{
        print "Error: Illegal Id submitted! (ID does not belong to your guild)";
        exit();
    }

}
*/



//NOT IN USE ANY LONGER
/*
if(isset($_POST["method"]) && $_POST["method"] == "faceitEloChecker"){
    session_start();
    include_once "services/sanitiseInputService.php";
    $sanitiser = new SanitiseInputService;

    $discord_id = $_SESSION["currentGuildId"];
    $adminId = $sanitiser->sanitiseInput($_POST["adminId"]);
    $mvpTime = $sanitiser->sanitiseInput($_POST["mvpTime"]);
    $mvpRythm = $sanitiser->sanitiseInput($_POST["mvpRythm"]);
    $updateChannelFaceit = $sanitiser->sanitiseInput($_POST["updateChannelFaceit"]);


    //edit later
    $mvpTime = strtotime($mvpTime);

    include_once "services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("server_settings", "discord_id=?", [$discord_id]);
    $dbSelection_csgo = $databaseService->selectData("csgo_elo_settings", "discord_id=?", [$discord_id]);
    $dbSelection_lol= $databaseService->selectData("lol_settings", "discord_id=?", [$discord_id]);

    //insert or update in server_settings
    if(empty($dbSelection)){
        //insert server settings
        $data = array("discord_id" => $discord_id, "admin_role_id" => $adminId);
        $types = "ii";
        $databaseService->insertData("server_settings", $data, $types);
    }else{
        //update
        $data = array("admin_role_id" => $adminId);
        $condition = "discord_id=?";
        $params = [$discord_id];
        $types = "ii";
        $databaseService->updateData("server_settings", $data, $condition, $params, $types);
    }

    if(empty($dbSelection_csgo)){
        //insert csgo_elo_settings
        $data2 = array("discord_id" => $discord_id, "text_channel_id" => $updateChannelFaceit, "mvp_update_rhythm" => $mvpRythm, "mvp_update_delta" => $mvpTime);
        $types2 = "iisi";
        $databaseService->insertData("csgo_elo_settings", $data2, $types2);
    }else{
        $data2 = array("text_channel_id" => $updateChannelFaceit, "mvp_update_rhythm" => $mvpRythm, "mvp_update_delta" => $mvpTime);
        $condition = "discord_id=?";
        $params = [$discord_id];
        $types2 = "isii";
        $databaseService->updateData("csgo_elo_settings", $data2, $condition, $params, $types2);
    }


    //insert into activitys
    //set mysql input values for actvity
    $activity = array("discord_id" =>$discord_id, "author" => $_SESSION["userData"]["name"], "action" => "Updated the Elo-Checker settings", "date"=> date("Y-m-d H:i:s"));
    $databaseService->insertData("activities", $activity, "isss");

    header("Location:frontend/modules/faceitElo/eloCheckerSettings.php?insert=success");
}
*/


?>