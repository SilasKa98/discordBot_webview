<?php

if(isset($_POST["method"]) && $_POST["method"] == "startBot"){
    $command = escapeshellcmd('python3 /opt/BergfestBot/launcher.py');
    $output = shell_exec($command);
    echo $output;
    #header("Location:adminDashboard.php?info=botStarted");
}

if(isset($_POST["method"]) && $_POST["method"] == "stopBot"){
    #$command = escapeshellcmd('python3 /opt/BergfestBot/launcher.py -stop');
    #$output = shell_exec("/opt/BergfestBot/launcher.py -stop");
    #echo $output;
    #header("Location:adminDashboard.php?info=botStopped");
    echo "test";
    $output = shell_exec("python3 /opt/BergfestBot/test.py");
    echo $output;
}


?>