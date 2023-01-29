<?php
session_start();

if(!$_SESSION["logged_in"]){
    header("Location:index.php");
    exit();
}

extract($_SESSION["userData"]);
$avatar_url = "https://cdn.discordapp.com/avatars/".$discord_id."/".$avatar.".jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Dashboard placeholder</p>
    <p>welcome <?php echo $name; ?></p>
    <img src="<?php echo $avatar_url; ?>">
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>