<?php
#$client_id = $_GET["client_id"];
#$discord_id = $_GET["discord_id"];
#$permissions= $_GET["permissions"];
#$scope = "bot%20identify";
#https://discord.com/api/oauth2/authorize?client_id=1069325187813752992&permissions=8&redirect_uri=http%3A%2F%2Flocalhost%2FdiscordBot_webview%2FserverSettings.php&response_type=code&scope=bot%20identify

//check if bot is already on the server of the client

//if yes --> redirect to server settings

//if no do auth


require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include_once "oAuthService.php";
$discord_code = $_GET["code"];
$payload = [
    'code'=>$discord_code,
    'client_id'=>$_ENV["client_id"],
    'client_secret'=>$_ENV["client_secret"],
    'grant_type'=>'authorization_code',
    'redirect_uri'=>$_ENV["join_redirect_url"],
    'scope'=>$_ENV["join_scope"]
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

echo "<pre>";
var_dump($result);
echo "</pre>";


?>