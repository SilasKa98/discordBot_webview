<?php
  if(!isset($_ENV["app_root"])){
    $basePath = dirname(__DIR__, 3);
    require $basePath.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();
  }
  
?>

<link rel="stylesheet" href="<?php echo $_ENV["app_root"]; ?>frontend/animations/levelSystem/levelSystem_styles.css">
<div class="level-container" id="animatedLevelsContainer">
    <div class="level">
      <div id="highLvl">
        <img src="<?php echo $_ENV["app_root"]; ?>frontend/animations/levelSystem/gauge-high.svg" class="levelImg">
        <p class="blurEffekt">Elite Member</p>
      </div>
    </div>
    <div class="level">
      <div id="midLvl">
        <img src="<?php echo $_ENV["app_root"]; ?>frontend/animations/levelSystem/gauge.svg" class="levelImg">
        <p class="blurEffekt">Top Member</p>
      </div>
    </div>
    <div class="level">
      <div id="lowLvl">
        <img src="<?php echo $_ENV["app_root"]; ?>frontend/animations/levelSystem/gauge-high.svg" class="levelImg">
        <p class="blurEffekt">Beginner</p>
      </div>
    </div>
  </div>