<?php

include "auth.php";
include "../config/database.php";



$order_id = (int)($_GET['id'] ?? 0);

if($order_id <= 0){
    die("Invalid order ID.");
}



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



.invoice-header{

    display:flex;

    justify-content:space-between;

    align-items:flex-start;

    margin-bottom:30px;

}

.company h1{

    margin:0;

    color:#111;

}

.company p{

    margin:4px 0;

    color:#555;

}

.invoice-info{

    text-align:right;

}

.invoice-info h2{

    margin:0;

    font-size:30px;

}

hr{

    margin:25px 0;

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


.billing-box{
    display:flex;
    justify-content:space-between;
    background:#f8f9fa;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

.billing-box div{
    width:48%;
}

.payment-box{
    background:#eef6ff;
    padding:20px;
    border-left:5px solid #007bff;
    border-radius:10px;
    margin-bottom:25px;
}

.invoice-footer{

text-align:center;

margin-top:40px;

color:#666;

font-size:14px;

}

</style>

</head>

<body>

<div class="invoice-header">

    <div class="company">

        <h1>Wardrobe Essentials</h1>

        <p>Fashion for Everyone</p>

        <p>Gagra, kutrakhand</p>

        <p>Email: support@wardrobeessentials.com</p>

        <p>Phone: +91 9876543210</p>

    </div>

    <div class="invoice-info">

        <h2>INVOICE</h2>

        <p><strong>Invoice No:</strong>
        INV-<?php echo str_pad($order['id'],5,"0",STR_PAD_LEFT); ?>
        </p>

        <p><strong>Order ID:</strong>
        #<?php echo $order['id']; ?>
        </p>

        <p><strong>Date:</strong>
        <?php echo date("d M Y",strtotime($order['created_at'])); ?>
        </p>

        <hr>

<div class="invoice-footer">

<p>

Thank you for shopping with
<strong>Wardrobe Essentials</strong>.

</p>

<p>

This is a computer-generated invoice.
No signature is required.

</p>

</div>

    </div>

</div>

<hr>

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

<div class="billing-box">

    <div>

        <h3>Customer Details</h3>

        <p><strong><?php echo $order['fullname']; ?></strong></p>

        <p><?php echo $order['email']; ?></p>

        <p><?php echo $order['phone']; ?></p>

    </div>

    <div>

        <h3>Delivery Address</h3>

        <p><?php echo $order['address']; ?></p>

    </div>

</div>

<div class="payment-box">

    <h3>Payment Information</h3>

    <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>

    <p><strong>Payment Method:</strong>
    <?php echo $order['payment_method']; ?>
    </p>

    <p><strong>Payment Status:</strong>
    <?php echo $order['payment_status']; ?>
    </p>

    <p><strong>Total:</strong>
₹<?php echo $order['total']; ?>
</p>

<p><strong>Order Date:</strong>
<?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?>
</p>

</p>

</div>

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

<script>
window.onload = function(){
    window.print();
}
</script>

</body>
</html>