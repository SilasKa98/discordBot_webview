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
    $mvpRythm = $sanitiser->sanitiseInput($_POST["mvpRythm"]);
    $updateChannelFaceit = $sanitiser->sanitiseInput($_POST["updateChannelFaceit"]);
    $updateChannelLol = $sanitiser->sanitiseInput($_POST["updateChannelLol"]);
    $roleTeam1 = $sanitiser->sanitiseInput($_POST["roleTeam1"]);
    $roleTeam2 = $sanitiser->sanitiseInput($_POST["roleTeam2"]);


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
        $data = array("discord_id" => $discord_id, "admin_role_id" => $adminId, "ff_1_role_id" => $roleTeam1, "ff_2_role_id" => $roleTeam2);
        $types = "iiii";
        $databaseService->insertData("server_settings", $data, $types);
    }else{
        //update
        $data = array("admin_role_id" => $adminId, "ff_1_role_id" => $roleTeam1, "ff_2_role_id" => $roleTeam2);
        $condition = "discord_id=?";
        $params = [$discord_id];
        $types = "iiii";
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

    if(empty($dbSelection_lol)){
        //insert lol_settings
        $data3 = array("discord_id" => $discord_id, "text_channel_id" => $updateChannelLol, "mvp_update_rhythm" => $mvpRythm, "mvp_update_delta" => $mvpTime);
        $types3 = "iisi";
        $databaseService->insertData("lol_settings", $data3, $types3);
    }else{
        $data3 = array("text_channel_id" => $updateChannelLol, "mvp_update_rhythm" => $mvpRythm, "mvp_update_delta" => $mvpTime);
        $condition = "discord_id=?";
        $params = [$discord_id];
        $types3 = "isii";
        $databaseService->updateData("lol_settings", $data3, $condition, $params, $types3);
    }



    //insert into activitys
    //set mysql input values for actvity
    $activity = array("discord_id" =>$discord_id, "author" => $_SESSION["userData"]["name"], "action" => "Updated the Elo-Checker settings", "date"=> date("Y-m-d H:i:s"));
    $databaseService->insertData("activities", $activity, "isss");

    header("Location:frontend/eloChecker/eloCheckerSettings.php?insert=success");
}


?>