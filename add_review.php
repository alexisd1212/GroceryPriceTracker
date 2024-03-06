<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}


require_once 'connect.php';

// Get user ID from session
$user_id = $_SESSION['user'];

// Insert review into user_reviews
$sql = "INSERT INTO user_reviews (user_id, grocery_item_id, rating, comment) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $_POST['product_id'], $_POST['rating'], $_POST['comment']]);

// Close statement
$stmt = null;
// Close connection
$pdo = null;

// Redirect to product_details.php
header('Location: product_details.php?id=' . $_POST['product_id']);
exit();
