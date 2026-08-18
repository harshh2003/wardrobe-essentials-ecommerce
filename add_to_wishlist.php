<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = $_GET['id'];

$check = $conn->query("SELECT * FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");

if ($check->num_rows == 0) {

    $conn->query("
        INSERT INTO wishlist(user_id, product_id)
        VALUES('$user_id','$product_id')
    ");

}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>