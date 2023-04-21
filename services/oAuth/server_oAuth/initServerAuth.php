<?php
    //here it gets checked whether the bot is already on the server or not.
    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();


    include_once "../../apiRequestService.php";
    $apiRequest = new ApiRequests();
    $bot_id = $_ENV["client_id"];
    $guild_id = $_GET["guildId"];
    $data = $apiRequest->checkUserOnServer($guild_id, $bot_id);

    //redirect to settings page or auth process
    if ($data['user']['bot']) {
        header("Location:" .$_ENV["app_root"]."frontend/serverSettings.php?guildId=".$_GET["guildId"]);
    } else {
        header("Location:".$_ENV["discord_serverAuth_url"]);
    }

?>