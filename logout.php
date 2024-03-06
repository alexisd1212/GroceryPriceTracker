<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
unset($_SESSION);
session_destroy();
session_write_close();
header('Location: landing.php');
die;
?>