<?php
session_start();
include 'config/database.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$review_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

$result = $conn->query("
SELECT *
FROM reviews
WHERE id='$review_id'
");

if($result->num_rows == 0){
    die("Review not found.");
}

$review = $result->fetch_assoc();

if($review['user_id'] != $user_id){
    die("Access Denied.");
}

$product_id = $review['product_id'];

$conn->query("
DELETE FROM reviews
WHERE id='$review_id'
");

header("Location: product.php?id=".$product_id);
exit;
?>