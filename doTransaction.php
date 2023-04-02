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
    $updateChannel = $sanitiser->sanitiseInput($_POST["updateChannel"]);
    $roleTeam1 = $sanitiser->sanitiseInput($_POST["roleTeam1"]);
    $roleTeam2 = $sanitiser->sanitiseInput($_POST["roleTeam2"]);

    include_once "services/databaseService.php";
    $databaseService = new DatabaseService;
    $dbSelection = $databaseService->selectData("faceitelochecker", "discord_id=?", [$discord_id]);

    if(empty($dbSelection)){
        $data = array("discord_id" => $discord_id, "admin_role_id" => $adminId, "mvp_update" => $mvpTime,
                     "csgo_text_channel" => $updateChannel, "lol_text_channel" => "", "ff_1_role_id" => $roleTeam1, "ff_2_role_id" => $roleTeam2);
        $types = "issssss";
        $databaseService->insertData("faceitelochecker", $data, $types);

        $activity = array("discord_id" =>$discord_id, "author" => $_SESSION["userData"]["name"], "action" => "First time inserted the Faceit-Elo-Checker settings", "date"=> date("Y-m-d H:i:s"));
    }else{
        $data = array("discord_id" => $discord_id, "admin_role_id" => $adminId, "mvp_update" => $mvpTime,
        "csgo_text_channel" => $updateChannel, "lol_text_channel" => "", "ff_1_role_id" => $roleTeam1, "ff_2_role_id" => $roleTeam2);
        $condition = "discord_id=?";
        $params = [$discord_id];
        $types = "issssssi";
        $databaseService->updateData("faceitelochecker", $data, $condition, $params, $types);

        $activity = array("discord_id" =>$discord_id, "author" => $_SESSION["userData"]["name"], "action" => "Updated the Faceit-Elo-Checker settings", "date"=> date("Y-m-d H:i:s"));
    }


    $databaseService->insertData("activities", $activity, "isss");

    header("Location:frontend/eloChecker/eloCheckerSettings.php?insert=success");
}


?>