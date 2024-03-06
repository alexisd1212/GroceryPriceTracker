<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user'];


// Insert product into cart 
$sql = "INSERT INTO cart (user_id, product_id) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $_POST['product_id']]);
// Close statement
$stmt = null;
// Close connection
$pdo = null;

// Redirect to cart.php
header('Location: landing.php');
exit();
