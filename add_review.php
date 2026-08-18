<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$product_id = $_POST['product_id'];
$user_id = $_SESSION['user']['id'];
$rating = $_POST['rating'];
$review = $_POST['review'];

$check = $conn->query("
SELECT id
FROM reviews
WHERE product_id='$product_id'
AND user_id='$user_id'
");

if($check->num_rows > 0){

    echo "<script>
        alert('You have already reviewed this product.');
        window.location='product.php?id=$product_id';
    </script>";

    exit;
}

$purchased = $conn->query("
SELECT order_items.id
FROM order_items
JOIN orders ON order_items.order_id = orders.id
WHERE orders.user_id='$user_id'
AND order_items.product_id='$product_id'
LIMIT 1
");

if($purchased->num_rows == 0){

    echo "<script>
        alert('You must purchase this product before reviewing it.');
        window.location='product.php?id=$product_id';
    </script>";

    exit;
}

$conn->query("
INSERT INTO reviews(product_id, user_id, rating, review)
VALUES('$product_id','$user_id','$rating','$review')
");

header("Location: product.php?id=".$product_id);
exit;
?>