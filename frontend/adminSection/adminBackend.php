<?php

if(isset($_POST["method"]) && $_POST["method"] == "startBot"){
    $command = escapeshellcmd('/opt/BergfestBot/launcher.py');
    $output = shell_exec($command);
    header("Location:adminDashboard.php?info=botStarted");
}

if(isset($_POST["method"]) && $_POST["method"] == "stopBot"){
    $command = escapeshellcmd('/opt/BergfestBot/launcher.py -stop');
    $output = shell_exec($command);
    header("Location:adminDashboard.php?info=botStopped");
}


?>