<?php
#$client_id = $_GET["client_id"];
#$discord_id = $_GET["discord_id"];
#$permissions= $_GET["permissions"];
#$scope = "bot%20identify";
#https://discord.com/api/oauth2/authorize?client_id=1069325187813752992&permissions=8&redirect_uri=http%3A%2F%2Flocalhost%2FdiscordBot_webview%2FserverSettings.php&response_type=code&scope=bot%20identify

//check if bot is already on the server of the client

//if yes --> redirect to server settings

//if no do auth


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
    'scope'=>$_ENV["scope"]
];

print_r($payload);
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

print_r($result);

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

print_r($result2);


?>