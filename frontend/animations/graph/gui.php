<?php
 if(!isset($_ENV["app_root"])){
  $basePath = dirname(__DIR__, 3);
  require $basePath.'/vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable($basePath);
  $dotenv->load();
}
?>

<link rel="stylesheet" href="<?php echo $_ENV["app_root"]; ?>frontend/animations/graph/graph_styles.css">
<div class="graph-container" id="animatedGraphContainer">
    <div class="line"></div>
</div>