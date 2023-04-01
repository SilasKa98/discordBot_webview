<?php
    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();

    header("LOCATION:".$_ENV["discord_serverAuth_url"]);
?>