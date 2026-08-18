<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$wishlist_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

$result = $conn->query("
SELECT products.*
FROM wishlist
JOIN products ON wishlist.product_id = products.id
WHERE wishlist.id='$wishlist_id'
AND wishlist.user_id='$user_id'
");

if($result->num_rows > 0){

    $product = $result->fetch_assoc();

    $id = $product['id'];

    $_SESSION['cart'][$id] = [
        "id" => $product['id'],
        "name" => $product['product_name'],
        "price" => $product['discount_price'],
        "image" => $product['main_image'],
        "qty" => 1
    ];

    $conn->query("DELETE FROM wishlist WHERE id='$wishlist_id'");
}

header("Location: cart.php");
exit;
?>