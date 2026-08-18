<?php
session_start();

include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$result = $conn->query("SELECT * FROM orders WHERE user_id='$user_id' ORDER BY id DESC");
?>

<main class="orders-page">

<h1>My Orders</h1>

<table border="1" cellpadding="12" cellspacing="0">

<tr>
    <th>Order ID</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
<th>Action</th>
</tr>

<?php while($order = $result->fetch_assoc()){ ?>

<tr>

<td>#<?php echo $order['id']; ?></td>

<td>₹<?php echo $order['total']; ?></td>

<td><?php echo $order['status']; ?></td>

<td><?php echo $order['created_at']; ?></td>

<td>
    <a href="order_details.php?id=<?php echo $order['id']; ?>">
        View Details
    </a>
</td>

</tr>

<?php } ?>

</table>

</main>

<?php include 'includes/footer.php'; ?>