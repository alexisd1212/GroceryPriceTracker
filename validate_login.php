<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
try {
    require_once 'connect.php';


    // get the email and password from the form submission and store them in variables 
    $email = $_GET['email'];
    $password = $_GET['password'];

    // sanitize the email and password
    // check for empty email and password
    if (empty($email) || empty($password)) {
        header("Location: login.php");
    }
    // use trim 
    $email = trim($email);
    $password = trim($password);
    // check for databse value incompatability
    if (strpos($email, "'") !== false || strpos($password, "'") !== false) {
        header("Location: login.php");
    }

    // create a query to select the user and return the user id and password
    $sql = "SELECT id, password FROM users WHERE email = '$email'";
    $statement = $pdo->prepare($sql);
    $statement->execute();

    // get the user id and password from the database
    $row = $statement->fetch();
    $userId = $row['id'];
    $dbPassword = $row['password'];

    // check if the password matches the password in the database
    if (!password_verify($password, $dbPassword)) {
        // if the password does not match, redirect to login page with error message
        header("Location: login.php?message=1");
    }

    // if the user exists
    if ($statement->rowCount() > 0) {
        header("Location: landing.php");
        // create session state and redirect to landing page
        session_start();
        $_SESSION['user'] = $userId;
        header("Location: landing.php");
    } else {
        header("Location: login.php");
    }
} catch (PDOException $e) {
    die($e->getMessage());
}
