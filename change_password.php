<!-- php file to handle password change -->
<?php
// connect to database
require_once 'connect.php';

// get user id from session
session_start();
$user_id = $_SESSION['user'];

// get old password from form
$old_password = $_POST['old-password'];

// get new password from form
$new_password = $_POST['new-password'];

// get confirm password from form
$confirm_password = $_POST['confirm-password'];

// get password from database
$sql = "SELECT password FROM users WHERE id = $user_id";
$statement = $pdo->prepare($sql);
$statement->execute();
$password = $statement->fetchColumn();

// check if old password matches password in database
if (password_verify($old_password, $password)) {
    // check if new password and confirm password match
    if ($new_password == $confirm_password) {
        // hash new password
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // update password in database
        $sql2 = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
        $statement2 = $pdo->prepare($sql2);
        $statement2->execute();

        // redirect to preferences page
        header("Location: preferences.php");
    } else {
        // redirect to preferences page with error message
        header("Location: preferences.php?message=1");
    }
} else {
    // redirect to preferences page with error message
    header("Location: preferences.php?message=2");
}
