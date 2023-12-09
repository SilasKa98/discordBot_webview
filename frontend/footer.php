<?php
  $basePath = dirname(__DIR__, 1);
  require $basePath.'/vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable($basePath);
  $dotenv->load();
?>
<div class="container">
  <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
    <p class="col-md-4 mb-0 text-muted" style="color:white !important;">© <?php echo date("Y"); ?> Bergfest Bot </p>

    <img src="<?php echo $_ENV["app_root"];?>media/bergfestBot_logo_v2.png" alt="our logo" width="60">

    <ul class="nav col-md-4 justify-content-end">
      <li class="nav-item"><a href="<?php echo $_ENV["app_root"];?>frontend/impressum.php" target="_blank" class="nav-link px-2 text-muted" style="color:white !important;">Impressum</a></li>
      <li class="nav-item"><a href="<?php echo $_ENV["app_root"];?>frontend/datenschutzerklaerung.php" target="_blank" class="nav-link px-2 text-muted" style="color:white !important;">Data privacy</a></li>
      <li class="nav-item"><a href="#" target="_blank" class="nav-link px-2 text-muted" style="color:white !important;">Help</a></li>
    </ul>
  </footer>
</div>