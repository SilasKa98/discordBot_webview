<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/discordBot_webview/index.php">
            <img src="/discordBot_webview/media/bergfestBot_logo_v2.png" alt="Logo" width="90" class="d-inline-block align-text-top">
            <p id="headerName">Bergfest Bot</p>
        </a>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <?php if(isset($_SESSION["logged_in"])){ ?>
                    <a class="nav-link" href="/discordbot_webview/dashboard.php">Dashboard</a>
                <?php }?>
                <a class="nav-link" aria-current="page" href="/discordbot_webview/frontend/commands.php">Commands</a>
                <a class="nav-link" href="#">Help</a>
                <a class="nav-link" target="_blank" href="https://discord.gg/CQVPtKvSCu">Join Our Discord</a>
            </div>
        </div>

        <?php if(isset($_SESSION["logged_in"])){ ?>
            <div class="nav-item dropdown" id="navOpenDrpDwnBtn">
                <a id="ankerWrapUserLink" href="/discordbot_webview/dashboard.php">
                    <span id="usernameLink">
                        <?php echo $name; ?>
                    </span>
                    <img class="rounded-circle" src="<?php echo $avatar_url;?>" alt="mdo" width="40" height="40">
                </a>      
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"></button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                    <li>
                        <form class="navbar-form navbar-right" method="post" action="/discordbot_webview/doTransaction.php">
                            <input type="hidden" name="method" value="logoutAccount">
                            <button class="dropdown-item" type="submit" name="logoutAccount">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php }else{?>
            <a href="/discordbot_webview/services/oAuth/init-oauth.php" id="loginWithDiscord_btn">
                <button type="button" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
                        <path d="M13.545 2.907a13.227 13.227 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.19 12.19 0 0 0-3.658 0 8.258 8.258 0 0 0-.412-.833.051.051 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.041.041 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032c.001.014.01.028.021.037a13.276 13.276 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019c.308-.42.582-.863.818-1.329a.05.05 0 0 0-.01-.059.051.051 0 0 0-.018-.011 8.875 8.875 0 0 1-1.248-.595.05.05 0 0 1-.02-.066.051.051 0 0 1 .015-.019c.084-.063.168-.129.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.052.052 0 0 1 .053.007c.08.066.164.132.248.195a.051.051 0 0 1-.004.085 8.254 8.254 0 0 1-1.249.594.05.05 0 0 0-.03.03.052.052 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.235 13.235 0 0 0 4.001-2.02.049.049 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.034.034 0 0 0-.02-.019Zm-8.198 7.307c-.789 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612Zm5.316 0c-.788 0-1.438-.724-1.438-1.612 0-.889.637-1.613 1.438-1.613.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612Z"/>
                    </svg>
                    Login with discord
                </button>
            </a>
        <?php }?>

    </div>
</nav>