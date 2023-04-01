<?php
$basePath = dirname(__DIR__, 2);
require $basePath.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

$discord_url = $_ENV["discord_url"];
header("Location:".$discord_url);
exit();

?>