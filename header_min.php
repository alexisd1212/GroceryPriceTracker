<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);
    session_start();
    if (isset($_SESSION['user'])) {
      echo '
        <div class="header-wrapper" id="logged">
        <a href="landing.php"><h1 id="logo">PANTRY</h1></a>
        <div id="spacer">


        </div>
        <a href="preferences.php" id="preferences">
        <div class="button">
        <h2>Preferences</h2>
        </div>
        </a>
        </div>
        ';
    } else {
      echo '
        <div class="header-wrapper" id="guest">
        <a href="landing.php"><h1 id="logo">PANTRY</h1></a>
        <div id="spacer">

        </div>
        <a href="create_account.php" id="createAccount"> <h2>Create Account</h2></a>
        <a href="login.php" id="login">
        <div class="button">
        <h2>Log In</h2>
        </div>
        </a>
        </div>
        ';
    }
    ?>