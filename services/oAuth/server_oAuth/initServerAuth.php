<?php
    //here it gets checked whether the bot is already on the server or not.
    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();


    include_once "../oAuthService.php";
    $bot_token = $_ENV["bot_token"];
    $bot_id = $_ENV["client_id"];
    $guild_id = $_GET["guildId"];

    $url = "https://discord.com/api/v9/guilds/$guild_id/members/$bot_id";
    $oAuthService = new oAuthService();
    $curlOptions = [
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
            'Authorization: Bot ' . $bot_token,
            'Content-Type: application/json',
        ]
    ];
    $response = $oAuthService->doCurlWithUrl($curlOptions, $url);

    $data = json_decode($response, true);

    //redirect to settings page or auth process
    if ($data['user']['bot']) {
        header("Location:../../../frontend/serverSettings.php?guildId=".$_GET["guildId"]);
    } else {
        header("Location:".$_ENV["discord_serverAuth_url"]);
    }

?>