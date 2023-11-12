<?php
 if(!isset($_ENV["app_root"])){
  $basePath = dirname(__DIR__, 3);
  require $basePath.'/vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable($basePath);
  $dotenv->load();
}
?>

<link rel="stylesheet" href="<?php echo $_ENV["app_root"]; ?>frontend/animations/messageBubbles/messageBubbles_styles.css">
<div class="sprechblase-container animation-started" id="animatedBubblesContainer">
    <div class="sprechblase">
      <p>Hey {user}! Nice to see you...🎉</p>
    </div>

    <div class="sprechblase">
      <p>Hop on and enjoy your stay!✅</p>
    </div>
  </div>

