<?php

if(isset($_POST["method"]) && $_POST["method"] == "logoutAccount"){
    session_start();
    session_destroy();
    header("Location: index.php?logout=success");
    exit();
}

if(isset($_POST["method"]) && $_POST["method"] == "faceitEloChecker"){
    session_start();
    include_once "services/sanitiseInputService.php";
    $sanitiser = new SanitiseInputService;

    $discord_id = $_SESSION["currentGuildId"];
    $adminId = $sanitiser->sanitiseInput($_POST["adminId"]);
    $mvpTime = $sanitiser->sanitiseInput($_POST["mvpTime"]);
    $updateChannelFaceit = $sanitiser->sanitiseInput($_POST["updateChannelFaceit"]);
    $updateChannelLol = $sanitiser->sanitiseInput($_POST["updateChannelLol"]);
    $roleTeam1 = $sanitiser->sanitiseInput($_POST["roleTeam1"]);
    $roleTeam2 = $sanitiser->sanitiseInput($_POST["roleTeam2"]);


    echo $discord_id."<br";
    echo $adminId."<br";
    echo $mvpTime."<br";
    echo $updateChannelFaceit."<br";
    echo $updateChannelLol."<br";
    echo $roleTeam1."<br";
    echo $roleTeam2."<br";


    include_once "services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("server_settings", "discord_id=?", [$discord_id]);

    if(empty($dbSelection)){
        $data = array("discord_id" => $discord_id, "admin_role_id" => $adminId, "mvp_update" => $mvpTime,
                     "csgo_text_channel_id" => $updateChannelFaceit, "lol_text_channel_id" => $updateChannelLol, "ff_1_role_id" => $roleTeam1, "ff_2_role_id" => $roleTeam2);
        $types = "iisiiii";
        $databaseService->insertData("server_settings", $data, $types);

        $activity = array("discord_id" =>$discord_id, "author" => $_SESSION["userData"]["name"], "action" => "First time inserted the Elo-Checker settings", "date"=> date("Y-m-d H:i:s"));
    }else{
        $data = array("discord_id" => $discord_id, "admin_role_id" => $adminId, "mvp_update" => $mvpTime,
        "csgo_text_channel_id" => $updateChannelFaceit, "lol_text_channel_id" => $updateChannelLol, "ff_1_role_id" => $roleTeam1, "ff_2_role_id" => $roleTeam2);
        $condition = "discord_id=?";
        $params = [$discord_id];
        $types = "iisiiiii";
        $databaseService->updateData("server_settings", $data, $condition, $params, $types);

        $activity = array("discord_id" =>$discord_id, "author" => $_SESSION["userData"]["name"], "action" => "Updated the Elo-Checker settings", "date"=> date("Y-m-d H:i:s"));
    }


    $databaseService->insertData("activities", $activity, "isss");

    header("Location:frontend/eloChecker/eloCheckerSettings.php?insert=success");
}


?>