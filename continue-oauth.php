<?php

if(!isset($_GET["code"])){
    echo "something went wrong, no code found";
    exit();
}
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$discord_code = $_GET["code"];


$payload = [
    'code'=>$discord_code,
    'client_id'=>$_ENV["client_id"],
    'client_secret'=>$_ENV["client_secret"],
    'grant_type'=>'authorization_code',
    'redirect_uri'=>$_ENV["redirect_uri"],
    'scope'=>$_ENV["scope"],
];

print_r($payload);

$payload_http = http_build_query($payload);
$discord_token_url = $_ENV["discord_token_url"];


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $discord_token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_http);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


//only in dev --> remove for production
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);
if(!$result){
    echo curl_error($ch);
    exit();
}
echo $result;
$result = json_decode($result,true);
$bearer_token = $result["access_token"];

$discord_user_url = $_ENV["discord_user_url"];
$header = array("Authorization: Bearer $bearer_token", "Content-Type: application/x-www-form-urlencoded");

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch2, CURLOPT_URL, $discord_user_url);
curl_setopt($ch2, CURLOPT_POST, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

//only in dev --> remove for production
curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);

$result2 = curl_exec($ch2);

$result2 = json_decode($result2, true);

session_start();

$_SESSION["logged_in"] = true;
$_SESSION["userData"] = [
    "name"=>$result2["username"],
    "discord_id"=>$result2["id"],
    "avatar"=>$result2["avatar"],
];

header("Location:dashboard.php")

?>