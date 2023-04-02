<?php

$basePath = dirname(__DIR__, 3);
require $basePath.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

include_once "../oAuthService.php";
$discord_code = $_GET["code"];
$payload = [
    'code'=>$discord_code,
    'client_id'=>$_ENV["client_id"],
    'client_secret'=>$_ENV["client_secret"],
    'grant_type'=>'authorization_code',
    'redirect_uri'=>$_ENV["serverAuth_redirect_uri"]
];


$payload_http = http_build_query($payload);
$discord_token_url = $_ENV["discord_token_url"];
$oAuthService = new oAuthService();
$curlOptions = [
    CURLOPT_URL=>$discord_token_url,
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>$payload_http,
    CURLOPT_RETURNTRANSFER=>true
];
$result = $oAuthService->doCurl($curlOptions);


$bearer_token = $oAuthService->getBearerToken($result);
$header = array("Authorization: Bearer $bearer_token", "Content-Type: application/x-www-form-urlencoded");

$discord_user_url = $_ENV["discord_user_url"];
$curlOptions2 = [
    CURLOPT_HTTPHEADER=>$header,
    CURLOPT_URL=>$discord_user_url,
    CURLOPT_POST=>false,
    CURLOPT_RETURNTRANSFER=>true
];
$result2 = $oAuthService->doCurl($curlOptions2);

$result2 = json_decode($result2, true);

header("Location:../../../frontend/serverSettings.php?guildId=".$result2["id"]);

?>