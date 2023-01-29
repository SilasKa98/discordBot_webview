<?php

if(!isset($_GET["code"])){
    echo "something went wrong, no code found";
    exit();
}
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
    'redirect_uri'=>$_ENV["redirect_uri"],
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


$discord_guild_url = $_ENV["discord_guild_url"];
$curlOptions3 = [
    CURLOPT_HTTPHEADER=>$header,
    CURLOPT_URL=>$discord_guild_url,
    CURLOPT_POST=>false,
    CURLOPT_RETURNTRANSFER=>true
];
$result3 = $oAuthService->doCurl($curlOptions3);
$result3 = json_decode($result3, true);


//get all information about the Servers which the user is connected to
$serverNames = [];
$serverIcons = [];
$guildIds = [];
foreach($result3 as $key => $value){
    #print_r($value);
    #print "<br><br>";
    array_push($serverNames,$value["name"]);
    array_push($serverIcons,$value["icon"]);
    array_push($guildIds,$value["id"]);
}


//create Sessions for the informations which need to be accessed later
session_start();

$_SESSION["logged_in"] = true;
$_SESSION["userData"] = [
    "name"=>$result2["username"],
    "discord_id"=>$result2["id"],
    "avatar"=>$result2["avatar"],
];

$_SESSION["userServerData"] = [
    "serverNames"=>$serverNames,
    "serverIcons"=>$serverIcons,
    "guildIds"=>$guildIds
];


header("Location:dashboard.php")

?>