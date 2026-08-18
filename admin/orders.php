<?php

include "auth.php";
include '../config/database.php';


$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';



$status = isset($_GET['status'])
    ? $_GET['status']
    : 'all';



$sort = isset($_GET['sort'])
    ? $_GET['sort']
    : 'newest';



$where = [];



if($search != ''){

    $searchSafe = $conn->real_escape_string($search);

    $where[] = "
        (
            id LIKE '%$searchSafe%'
            OR fullname LIKE '%$searchSafe%'
            OR email LIKE '%$searchSafe%'
            OR phone LIKE '%$searchSafe%'
        )
    ";
}



if(
    $status == 'Pending' ||
    $status == 'Shipped' ||
    $status == 'Delivered'
){

    $statusSafe = $conn->real_escape_string($status);

    $where[] = "status='$statusSafe'";
}



$whereSQL = '';

if(count($where) > 0){

    $whereSQL = 'WHERE ' . implode(' AND ', $where);

}



$orderSQL = "ORDER BY id DESC";


if($sort == 'oldest'){

    $orderSQL = "ORDER BY id ASC";

}
elseif($sort == 'high'){

    $orderSQL = "ORDER BY total DESC";

}
elseif($sort == 'low'){

    $orderSQL = "ORDER BY total ASC";

}



$ordersPerPage = 10;


$countQuery = "
    SELECT COUNT(*) AS total
    FROM orders
    $whereSQL
";


$totalResult = $conn->query($countQuery);

$totalOrders = $totalResult->fetch_assoc()['total'];


$totalPages = max(
    1,
    ceil($totalOrders / $ordersPerPage)
);


$currentPage = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;


if($currentPage > $totalPages){

    $currentPage = $totalPages;

}


$offset = ($currentPage - 1) * $ordersPerPage;


$result = $conn->query("
    SELECT *
    FROM orders
    $whereSQL
    $orderSQL
    LIMIT $ordersPerPage
    OFFSET $offset
");

$allCount = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
")->fetch_assoc()['total'];

$pendingCount = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status='Pending'
")->fetch_assoc()['total'];

$shippedCount = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status='Shipped'
")->fetch_assoc()['total'];

$deliveredCount = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status='Delivered'
")->fetch_assoc()['total'];

// Order Statistics

$totalOrders = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
")->fetch_assoc()['total'];

$pendingOrders = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status='Pending'
")->fetch_assoc()['total'];

$shippedOrders = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status='Shipped'
")->fetch_assoc()['total'];

$deliveredOrders = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status='Delivered'
")->fetch_assoc()['total'];

$totalRevenue = $conn->query("
    SELECT SUM(total) AS total
    FROM orders
    WHERE payment_status='Paid'
")->fetch_assoc()['total'];

$totalRevenue = $totalRevenue ?? 0;
?>



<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f5f6f8;
    color:#222;
    padding:30px;
}

/* TOP HEADER */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.page-header h1{
    font-size:32px;
    margin-bottom:6px;
}

.page-header p{
    color:#777;
    font-size:14px;
}

/* DASHBOARD BUTTON */

.back-btn{
    display:inline-block;
    background:#111;
    color:#fff;
    text-decoration:none;
    padding:12px 18px;
    border-radius:8px;
    font-weight:bold;
    transition:.3s;
}

.back-btn:hover{
    background:#333;
    transform:translateY(-2px);
}

/* SEARCH */

.search-box{
    margin-bottom:20px;
}

.search-box input{
    width:300px;
    padding:12px 15px;
    border:1px solid #ddd;
    border-radius:8px;
    outline:none;
    font-size:14px;
}

.search-box input:focus{
    border-color:#2563eb;
}

/* TABLE CARD */

.table-card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#111;
    color:#fff;
    padding:15px;
    text-align:left;
    white-space:nowrap;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
    vertical-align:middle;
}

tbody tr{
    transition:.2s;
}

tbody tr:hover{
    background:#f9fafb;
}

/* TOTAL */

.total{
    font-weight:bold;
}

/* STATUS */

.status-form{
    display:flex;
    align-items:center;
    gap:8px;
}

.status-select{
    padding:8px 10px;
    border:1px solid #ddd;
    border-radius:6px;
    background:#fff;
    cursor:pointer;
}

.update-btn{
    border:none;
    background:#2563eb;
    color:#fff;
    padding:8px 12px;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
    transition:.3s;
}

.update-btn:hover{
    background:#1d4ed8;
}

/* VIEW DETAILS */

.view-btn{
    display:inline-block;
    margin-top:8px;
    padding:8px 14px;
    background:#111;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    font-size:14px;
    transition:.3s;
}

.view-btn:hover{
    background:#333;
}

/* MOBILE */

@media(max-width:768px){

    body{
        padding:15px;
    }

    .page-header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .page-header h1{
        font-size:26px;
    }

    .search-box input{
        width:100%;
    }

    .table-card{
        padding:10px;
    }

    th,
    td{
        padding:10px;
        font-size:13px;
    }

}



/* =========================
   ORDERS TABLE FIX
========================= */

.table-card {
    width: 100%;
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    margin-top: 25px;
    overflow-x: auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.table-card table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1100px;
}

.table-card thead {
    background: #111;
    color: #fff;
}

.table-card th {
    padding: 16px 14px;
    text-align: left;
    font-size: 14px;
    white-space: nowrap;
}

.table-card td {
    padding: 16px 14px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    vertical-align: middle;
}

.table-card tbody tr:hover {
    background: #f8f9fa;
}

.table-card .total {
    font-weight: 700;
    white-space: nowrap;
}

.status-form {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.status-select {
    padding: 9px 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
    background: #fff;
    font-size: 14px;
}

.update-btn {
    border: none;
    background: #2563eb;
    color: #fff;
    padding: 9px 14px;
    border-radius: 7px;
    cursor: pointer;
    font-weight: 600;
}

.update-btn:hover {
    background: #1d4ed8;
}

.view-btn {
    display: inline-block;
    background: #111;
    color: #fff;
    text-decoration: none;
    padding: 9px 14px;
    border-radius: 7px;
    font-weight: 600;
    white-space: nowrap;
}

.view-btn:hover {
    background: #333;
}

.order-products {
    min-width: 180px;
}

.order-product {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.order-product img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.order-product span {
    font-size: 14px;
    font-weight: 500;
}

.order-filters {
    display: flex;
    gap: 10px;
    margin: 20px 0;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 18px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
    text-decoration: none;
}

.filter-btn:hover {
    background: #111;
    color: white;
}

.filter-btn.active {
    background: #111;
    color: white;
}


.count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    margin-left: 5px;
    border-radius: 50px;
    background: #eee;
    color: #333;
    font-size: 12px;
    font-weight: bold;
}

.filter-btn.active .count {
    background: #fff;
    color: #111;
}






.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 11px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 8px;
}

/* Pending */
.status-pending {
    background: #fff3cd;
    color: #856404;
}

/* Shipped */
.status-shipped {
    background: #dbeafe;
    color: #1d4ed8;
}

/* Delivered */
.status-delivered {
    background: #dcfce7;
    color: #15803d;
}

/* ORDER STATS */

.order-stats{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:18px;
    margin:25px 0;
}

.stat-card{
    background:#fff;
    padding:22px;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.07);
    border:1px solid #eee;
    transition:.3s;
}

.stat-card:hover{
    transform:translateY(-4px);
    box-shadow:0 10px 25px rgba(0,0,0,.1);
}

.stat-card .icon{
    font-size:25px;
    margin-bottom:12px;
}

.stat-card p{
    color:#777;
    font-size:13px;
    margin-bottom:7px;
}

.stat-card h2{
    font-size:26px;
    margin:0;
}

/* Different top borders */

.stat-card.total{
    border-top:4px solid #111;
}

.stat-card.pending{
    border-top:4px solid #f59e0b;
}

.stat-card.shipped{
    border-top:4px solid #2563eb;
}

.stat-card.delivered{
    border-top:4px solid #16a34a;
}

.stat-card.revenue{
    border-top:4px solid #9333ea;
}


/* MOBILE */

@media(max-width:1000px){

    .order-stats{
        grid-template-columns:repeat(2,1fr);
    }

}

@media(max-width:600px){

    .order-stats{
        grid-template-columns:1fr;
    }

}


.sort-box{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:20px;
}

.sort-box label{
    font-size:14px;
    font-weight:600;
    color:#444;
}

.sort-box select{
    padding:10px 14px;
    border:1px solid #ddd;
    border-radius:8px;
    background:#fff;
    font-size:14px;
    cursor:pointer;
    outline:none;
}

.sort-box select:focus{
    border-color:#2563eb;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin: 25px 0;
    flex-wrap: wrap;
}

.page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fff;
    color: #111;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: 0.2s;
}

.page-btn:hover {
    background: #111;
    color: #fff;
    border-color: #111;
}

.page-btn.active {
    background: #111;
    color: #fff;
    border-color: #111;
}



.search-btn{
    border:none;
    background:#111;
    color:#fff;
    padding:14px 24px;
    border-radius:10px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    margin-left:8px;
    transition:0.3s;
}

.search-btn:hover{
    background:#ff9800;
    transform:translateY(-1px);
}



.search-form{
    display:flex;
    align-items:center;
    gap:8px;
}

.search-form input{
    margin:0;
}


</style>

</head>
<body>

<div class="page-header">

    <div>
        <h1>Customer Orders</h1>
        <p>Manage and track all customer orders</p>
    </div>

    <a href="index.php" class="back-btn">
        ← Back to Dashboard
    </a>

</div>

<div class="order-stats">

    <div class="stat-card total">

        <div class="icon">📦</div>

        <p>Total Orders</p>

        <h2>
            <?php echo $totalOrders; ?>
        </h2>

    </div>


    <div class="stat-card pending">

        <div class="icon">⏳</div>

        <p>Pending Orders</p>

        <h2>
            <?php echo $pendingOrders; ?>
        </h2>

    </div>


    <div class="stat-card shipped">

        <div class="icon">🚚</div>

        <p>Shipped Orders</p>

        <h2>
            <?php echo $shippedOrders; ?>
        </h2>

    </div>


    <div class="stat-card delivered">

        <div class="icon">✅</div>

        <p>Delivered Orders</p>

        <h2>
            <?php echo $deliveredOrders; ?>
        </h2>

    </div>


    <div class="stat-card revenue">

        <div class="icon">💰</div>

        <p>Paid Revenue</p>

        <h2>
            ₹<?php echo number_format($totalRevenue, 2); ?>
        </h2>

    </div>

</div>

<div class="search-box">

    <form method="GET" class="search-form">

        <input
            type="text"
            name="search"
            id="orderSearch"
            value="<?php echo htmlspecialchars($search); ?>"
            placeholder="🔍 Search orders...">

        <?php if($status != 'all'): ?>
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
        <?php endif; ?>

        <?php if($sort != 'newest'): ?>
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
        <?php endif; ?>

        <button type="submit" class="search-btn">Search</button>

    </form>

</div>

<div class="sort-box">

    <label for="orderSort">Sort by:</label>

    <select id="orderSort">

        <option value="newest"
        <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>
            Newest First
        </option>

        <option value="oldest"
        <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>
            Oldest First
        </option>

        <option value="high"
        <?php echo ($sort == 'high') ? 'selected' : ''; ?>>
            Highest Amount
        </option>

        <option value="low"
        <?php echo ($sort == 'low') ? 'selected' : ''; ?>>
            Lowest Amount
        </option>

    </select>

</div>

<div class="table-card">

<div class="order-filters">

    <?php
    $filterParams = [];

    if($search != ''){
        $filterParams['search'] = $search;
    }

    if($sort != 'newest'){
        $filterParams['sort'] = $sort;
    }

    $filterParams['status'] = 'all';
    ?>

    <a
        href="?<?php echo http_build_query($filterParams); ?>"
        class="filter-btn <?php echo ($status == 'all') ? 'active' : ''; ?>"
    >
        All Orders
        <span class="count"><?php echo $allCount; ?></span>
    </a>


    <?php
    $filterParams['status'] = 'Pending';
    ?>

    <a
        href="?<?php echo http_build_query($filterParams); ?>"
        class="filter-btn <?php echo ($status == 'Pending') ? 'active' : ''; ?>"
    >
        Pending
        <span class="count"><?php echo $pendingCount; ?></span>
    </a>


    <?php
    $filterParams['status'] = 'Shipped';
    ?>

    <a
        href="?<?php echo http_build_query($filterParams); ?>"
        class="filter-btn <?php echo ($status == 'Shipped') ? 'active' : ''; ?>"
    >
        Shipped
        <span class="count"><?php echo $shippedCount; ?></span>
    </a>


    <?php
    $filterParams['status'] = 'Delivered';
    ?>

    <a
        href="?<?php echo http_build_query($filterParams); ?>"
        class="filter-btn <?php echo ($status == 'Delivered') ? 'active' : ''; ?>"
    >
        Delivered
        <span class="count"><?php echo $deliveredCount; ?></span>
    </a>

</div>

    <table>

    <thead>

    <tr>
        <th>ID</th>
        <th>Products</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

    </thead>

    <tbody>

    <?php while($order = $result->fetch_assoc()) { ?>

<tr
    data-date="<?php echo strtotime($order['created_at']); ?>"
    data-total="<?php echo $order['total']; ?>"
>

    <td><?php echo $order['id']; ?></td>

<td class="order-products">

<?php

$order_id = $order['id'];

$items = $conn->query("
    SELECT products.main_image, products.product_name
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = $order_id
");

while($item = $items->fetch_assoc()){

?>

<div class="order-product">

    <img
        src="../assets/images/products/<?php echo $item['main_image']; ?>"
        alt="<?php echo htmlspecialchars($item['product_name']); ?>"
    >

    <span>
        <?php echo htmlspecialchars($item['product_name']); ?>
    </span>

</div>

<?php } ?>

</td>

<td><?php echo $order['fullname']; ?></td>

    <td><?php echo $order['email']; ?></td>

    <td><?php echo $order['phone']; ?></td>

    <td><?php echo $order['address']; ?></td>

    <td class="total">
        ₹<?php echo number_format($order['total'], 2); ?>
    </td>

    <td>

    <?php

    if($order['status'] == "Pending"){
        $statusClass = "status-pending";
        $statusIcon = "⏳";
    }
    elseif($order['status'] == "Shipped"){
        $statusClass = "status-shipped";
        $statusIcon = "🚚";
    }
    else{
        $statusClass = "status-delivered";
        $statusIcon = "✓";
    }

    ?>

    <span class="status-badge <?php echo $statusClass; ?>">
        <?php echo $statusIcon; ?>
        <?php echo $order['status']; ?>
    </span>

    <form action="update_status.php" method="POST" class="status-form">

        <input
            type="hidden"
            name="id"
            value="<?php echo $order['id']; ?>">




            <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">





        <select name="status" class="status-select">

            <option value="Pending"
                <?php if($order['status']=="Pending") echo "selected"; ?>>
                Pending
            </option>

            <option value="Shipped"
                <?php if($order['status']=="Shipped") echo "selected"; ?>>
                Shipped
            </option>

            <option value="Delivered"
                <?php if($order['status']=="Delivered") echo "selected"; ?>>
                Delivered
            </option>

        </select>

        <button
            type="submit"
            name="update_status"
            class="update-btn">
            Update
        </button>

    </form>

</td>

    <td>
        <?php echo $order['created_at']; ?>
    </td>

    <td>

        <a
            href="order_details.php?id=<?php echo $order['id']; ?>"
            class="view-btn">
            View Details
        </a>

    </td>

</tr>

    <?php } ?>

    </tbody>

    </table>

    </div>

    <div class="pagination">

<?php

$queryParams = [];

if($search != ''){
    $queryParams['search'] = $search;
}

if($status != 'all'){
    $queryParams['status'] = $status;
}

if($sort != 'newest'){
    $queryParams['sort'] = $sort;
}

?>

<?php if($currentPage > 1): ?>

    <?php
    $queryParams['page'] = $currentPage - 1;
    ?>

    <a
        href="?<?php echo http_build_query($queryParams); ?>"
        class="page-btn">
        ← Previous
    </a>

<?php endif; ?>


<?php for($i = 1; $i <= $totalPages; $i++): ?>

    <?php
    $queryParams['page'] = $i;
    ?>

    <a
        href="?<?php echo http_build_query($queryParams); ?>"
        class="page-btn <?php echo ($i == $currentPage) ? 'active' : ''; ?>">

        <?php echo $i; ?>

    </a>

<?php endfor; ?>


<?php if($currentPage < $totalPages): ?>

    <?php
    $queryParams['page'] = $currentPage + 1;
    ?>

    <a
        href="?<?php echo http_build_query($queryParams); ?>"
        class="page-btn">
        Next →
    </a>

<?php endif; ?>

</div>


<script>

document.getElementById("orderSort").addEventListener("change", function(){

    const params = new URLSearchParams(window.location.search);

    params.set("sort", this.value);

    params.set("page", "1");

    window.location.href = "?" + params.toString();

});

</script>



</body>
</html>