<?php
session_start();

include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$order_id = $_GET['id'];

$order = $conn->query("
SELECT * FROM orders
WHERE id='$order_id'
AND user_id='".$_SESSION['user']['id']."'
")->fetch_assoc();

if(!$order){
    die("Order not found.");
}

$items = $conn->query("
SELECT order_items.*, products.main_image
FROM order_items
LEFT JOIN products
ON order_items.product_id = products.id
WHERE order_items.order_id='$order_id'
");
?>

<main class="orders-page">

<h1>Order #<?php echo $order_id; ?></h1>

<p><strong>Status:</strong> <?php echo $order['status']; ?></p>

<table border="1" cellpadding="10">

<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
</tr>

<?php while($item = $items->fetch_assoc()){ ?>

<tr>

<td>
    <img src="assets/images/products/<?php echo $item['main_image']; ?>"
         width="80">
</td>

<td><?php echo $item['product_name']; ?></td>

<td>₹<?php echo $item['price']; ?></td>

<td><?php echo $item['quantity']; ?></td>

<td>₹<?php echo $item['price'] * $item['quantity']; ?></td>

</tr>

<?php } ?>

</table>

<br>

<h3>Total : ₹<?php echo $order['total']; ?></h3>

<p><strong>Status :</strong> <?php echo $order['status']; ?></p>

<p><strong>Order Date :</strong> <?php echo $order['created_at']; ?></p>

</main>

<?php include 'includes/footer.php'; ?>