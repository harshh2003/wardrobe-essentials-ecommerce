<?php

include "auth.php";
include "../config/database.php";



$order_id = (int)($_GET['id'] ?? 0);

if($order_id <= 0){
    die("Invalid order ID.");
}


// Get order details safely

$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $order_id);

$stmt->execute();

$result = $stmt->get_result();


if($result->num_rows !== 1){

    $stmt->close();

    die("Order not found.");

}


$order = $result->fetch_assoc();

$stmt->close();



$stmt = $conn->prepare("
    SELECT order_items.*, products.main_image
    FROM order_items
    JOIN products
    ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
");

$stmt->bind_param("i", $order_id);

$stmt->execute();

$items = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>

<title>Order Details</title>

<style>

body{
    font-family:Arial;
    padding:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th,
table td{
    border:1px solid #ddd;
    padding:12px;
}

table th{
    background:#111;
    color:white;
}

.info{
    margin-bottom:20px;
}








.status-pending{
    background:#fff3cd;
    color:#856404;
    padding:6px 12px;
    border-radius:20px;
    font-weight:bold;
    display:inline-block;
}

.status-shipped{
    background:#cce5ff;
    color:#004085;
    padding:6px 12px;
    border-radius:20px;
    font-weight:bold;
    display:inline-block;
}

.status-delivered{
    background:#d4edda;
    color:#155724;
    padding:6px 12px;
    border-radius:20px;
    font-weight:bold;
    display:inline-block;
}




.print-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 18px;
    background:#111;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    font-weight:bold;
}

.print-btn:hover{
    background:#333;
}

</style>

</head>

<body>

<h1>Order Details</h1>

<a href="print_invoice.php?id=<?php echo $order['id']; ?>" target="_blank" class="print-btn">
    🖨️ Print Invoice
</a>

<div class="info">

<p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>

<p><strong>Customer:</strong> <?php echo $order['fullname']; ?></p>

<p><strong>Email:</strong> <?php echo $order['email']; ?></p>

<p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>

<p><strong>Address:</strong> <?php echo $order['address']; ?></p>

<p><strong>Payment:</strong>
<?php echo $order['payment_method']; ?>
</p>

<p><strong>Payment Status:</strong>
<?php echo $order['payment_status']; ?>
</p>

<?php

$statusClass = "";

if($order['status']=="Pending"){
    $statusClass = "status-pending";
}
elseif($order['status']=="Shipped"){
    $statusClass = "status-shipped";
}
else{
    $statusClass = "status-delivered";
}

?>

<p>

<strong>Order Status:</strong>

<span class="<?php echo $statusClass; ?>">

<?php echo $order['status']; ?>

</span>

</p>

<p><strong>Total:</strong>
₹<?php echo $order['total']; ?>
</p>

<p><strong>Order Date:</strong>
<?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?>
</p>

</div>

<h2>Purchased Items</h2>

<table>

<tr>

<th>Image</th>

<th>Product</th>

<th>Price</th>

<th>Quantity</th>

<th>Total</th>

</tr>

<?php while($item = $items->fetch_assoc()){ ?>

<tr>

<td>

<img
src="../assets/images/products/<?php echo $item['main_image']; ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

</td>

<td><?php echo $item['product_name']; ?></td>

<td>₹<?php echo $item['price']; ?></td>

<td><?php echo $item['quantity']; ?></td>

<td>₹<?php echo $item['price'] * $item['quantity']; ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>