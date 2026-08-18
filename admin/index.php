<?php

include "auth.php";
include "../config/database.php";

// Total Revenue
$revenue = $conn->query("
SELECT SUM(total) AS total_revenue
FROM orders
WHERE payment_status='Paid'
OR status='Delivered'
")->fetch_assoc();

// Total Orders
$orders = $conn->query("
SELECT COUNT(*) AS total_orders
FROM orders
")->fetch_assoc();

// Total Users
$users = $conn->query("
SELECT COUNT(*) AS total_users
FROM users
")->fetch_assoc();

// Total Products
$products = $conn->query("
SELECT COUNT(*) AS total_products
FROM products
")->fetch_assoc();

// Pending Orders
$pending = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE status='Pending'
")->fetch_assoc();

// Shipped Orders
$shipped = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE status='Shipped'
")->fetch_assoc();

// Delivered Orders
$delivered = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE status='Delivered'
")->fetch_assoc();

// Total Coupons
$coupons = $conn->query("
SELECT COUNT(*) AS total
FROM coupons
")->fetch_assoc();

// Latest 5 Orders
$latestOrders = $conn->query("
SELECT id, fullname, total, status, payment_status, created_at
FROM orders
ORDER BY id DESC
LIMIT 10
");

// Monthly Sales
$sales = $conn->query("
SELECT
MONTH(created_at) AS month,
SUM(total) AS revenue
FROM orders
WHERE payment_status='Paid'
OR status='Delivered'
GROUP BY MONTH(created_at)
");

$monthlySales = array_fill(1, 12, 0);

while($row = $sales->fetch_assoc()){
    $monthlySales[$row['month']] = $row['revenue'];
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#f4f4f4;
}

.header{
    background:#111;
    color:white;
    padding:20px 40px;
}

.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:25px;
    padding:30px;
}

.card{
    background:#fff;
    border-radius:12px;
    padding:30px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,.1);
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h2{
    font-size:40px;
    margin:15px 0;
}

.card p{
    color:#666;
    font-size:18px;
}

.card:nth-child(1){
    border-top:5px solid #4CAF50;
}

.card:nth-child(2){
    border-top:5px solid #2196F3;
}

.card:nth-child(3){
    border-top:5px solid #FF9800;
}

.card:nth-child(4){
    border-top:5px solid #9C27B0;
}

.card:nth-child(5){
    border-top:5px solid #F44336;
}

.card:nth-child(6){
    border-top:5px solid #3F51B5;
}

.card:nth-child(7){
    border-top:5px solid #009688;
}

.card:nth-child(8){
    border-top:5px solid #795548;
}

.button{
    display:inline-block;
    background:#111;
    color:#fff;
    padding:14px 28px;
    border-radius:8px;
    text-decoration:none;
    margin:15px;
    transition:.3s;
}

.button:hover{
    background:#ff9800;
}

.buttons a{
    display:inline-block;
    margin:10px;
    padding:15px 30px;
    background:#111;
    color:white;
    text-decoration:none;
    border-radius:8px;
    transition:.3s;
}

.buttons a:hover{
    background:#ff9800;
}

.container{
    display:flex;
    min-height:100vh;
}

.sidebar{
    width:230px;
    background:#111;
    color:#fff;
    padding:25px 0;
}

.sidebar h2{
    text-align:center;
    margin-bottom:15px;
    font-size:25px;
    line-height:1.2;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:16px 7px;
    color:#fff;
    text-decoration:none;
    font-size:15px;
    transition:.3s;
}

.sidebar a:hover{
    background:#ff9800;
}

.sidebar a.active{
    background:#ff9800;
}

.main{
    flex:1;
}




.sales-chart{
    background:#fff;
    margin:30px;
    padding:25px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,.1);
}

.sales-chart h2{
    margin-bottom:20px;
}

.sales-chart{
    background:#fff;
    margin:30px;
    padding:30px;
    border-radius:16px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
    height:480px;
}

#salesChart{
    width:100%;
    height:100%;
}




.latest-orders{

    margin:10px;

    background:#fff;

    padding:10px;

    border-radius:40px;

    box-shadow:0 15px 15px rgba(0,0,0,0.8);

}

.latest-orders h2{

    margin-bottom:20px;

}

.latest-orders table{

    width:100%;

    border-collapse:collapse;

}

.latest-orders th{

    background:#111;

    color:#fff;

    padding:15px;

}

.latest-orders td{

    padding:15px;

    border-bottom:1px solid #eee;

    text-align:center;

}

.view-order-btn{

    background:#111;

    color:#fff;

    text-decoration:none;

    padding:8px 15px;

    border-radius:6px;

    transition:.3s;

}

.view-order-btn:hover{

    background:#ff9800;

}


.logout-btn{
    margin-top:20px;
    background:#763b37 !important;
}

.logout-btn:hover{
    background:#b91c1c !important;
}

</style>

</head>

<body>

<div class="container">

<div class="sidebar">

<h2>Wardrobe Admin</h2>

<a href="index.php">🏠 Dashboard</a>

<a href="products.php">📦 Products</a>

<a href="orders.php">🛒 Orders</a>

<a href="../index.php">🌐 Visit Website</a>

<a href="logout.php" class="logout-btn">🚪Logout</a>

</div>

<div class="main">

<div class="header">
<h1>Welcome, Admin 👋</h1>

<p>Manage your store and monitor your business.</p>
</div>

<div class="cards">

    <div class="card">
        <p>Total Products</p>
        <h2><?php echo $products['total_products']; ?></h2>
    </div>

    <div class="card">
        <p>Total Orders</p>
        <h2><?php echo $orders['total_orders']; ?></h2>
    </div>

    <div class="card">
        <p>Total Users</p>
        <h2><?php echo $users['total_users']; ?></h2>
    </div>

    <div class="card">
        <p>Total Revenue</p>
        <h2>₹<?php echo number_format($revenue['total_revenue'] ?? 0, 2); ?></h2>
    </div>

</div>

<div class="cards">

    <div class="card">
        <p>📦 Pending Orders</p>
        <h2><?php echo $pending['total']; ?></h2>
    </div>

    <div class="card">
        <p>🚚 Shipped Orders</p>
        <h2><?php echo $shipped['total']; ?></h2>
    </div>

    <div class="card">
        <p>✅ Delivered Orders</p>
        <h2><?php echo $delivered['total']; ?></h2>
    </div>

    <div class="card">
        <p>🎟️ Total Coupons</p>
        <h2><?php echo $coupons['total']; ?></h2>
    </div>

</div>

<div class="sales-chart">

    <h2>📈 Monthly Sales Analytics</h2>

    <canvas id="salesChart"></canvas>

</div>

</div>

<div class="latest-orders">

<h2>Latest Orders</h2>

<table>

<tr>

<th>Order ID</th>

<th>Customer</th>

<th>Total</th>

<th>Status</th>

<th>Payment</th>

<th>Date</th>

<th>Action</th>

</tr>

<?php while($row = $latestOrders->fetch_assoc()){ ?>

<tr>

<td>#<?php echo $row['id']; ?></td>

<td><?php echo $row['fullname']; ?></td>

<td>₹<?php echo number_format($row['total'],2); ?></td>

<td><?php echo $row['status']; ?></td>

<td><?php echo $row['payment_status']; ?></td>

<td><?php echo date("d M Y",strtotime($row['created_at'])); ?></td>

<td>

<a href="order_details.php?id=<?php echo $row['id']; ?>" class="view-order-btn">
    View
</a>

</td>

</tr>

<?php } ?>

</table>

</div>


</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('salesChart');

new Chart(ctx, {

    type: 'line',

    data: {

        labels:[
'Jan',
'Feb',
'Mar',
'Apr',
'May',
'Jun',
'Jul',
'Aug',
'Sep',
'Oct',
'Nov',
'Dec'
],

        datasets: [{
    label: 'Monthly Revenue',
    data: [
        <?php
        for($i=1;$i<=12;$i++){
            echo $monthlySales[$i];
            if($i != 12) echo ",";
        }
        ?>
    ],
    borderColor: '#4F46E5',
    backgroundColor: 'rgba(79,70,229,0.15)',
    fill: true,
    tension: 0.4,
    pointBackgroundColor: '#4F46E5',
    pointBorderColor: '#fff',
    pointRadius: 5,
    pointHoverRadius: 8,
    borderWidth: 3
}]

    }

});

</script>

</body>
</html>