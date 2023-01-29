<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$discord_url = $_ENV["discord_url"];
header("Location:".$discord_url);
exit();

?>