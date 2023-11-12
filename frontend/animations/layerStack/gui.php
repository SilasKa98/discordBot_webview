
<?php
 if(!isset($_ENV["app_root"])){
  $basePath = dirname(__DIR__, 3);
  require $basePath.'/vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable($basePath);
  $dotenv->load();
}
?>

<link rel="stylesheet" href="<?php echo $_ENV["app_root"]; ?>frontend/animations/layerStack/layerStack_styles.css">
<div class="animatedLayers-container" id="animatedLayersContainer">
    <div class="animatedLayers"></div>
    <div class="animatedLayers"></div>
    <div class="animatedLayers"></div>
  </div>
