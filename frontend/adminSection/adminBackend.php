<?php

if(isset($_POST["method"]) && $_POST["method"] == "startBot"){
    $command = escapeshellcmd('/opt/BergfestBot/main.py');
    $output = shell_exec($command);
}


?>