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

<style>
/* =========================================
   MY ORDERS - MOBILE ONLY
   DESKTOP IS NOT AFFECTED
========================================= */

@media screen and (max-width: 768px) {

    .orders-page {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 100px 8px 30px !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }

    .orders-page h1 {
        text-align: center !important;
        font-size: 24px !important;
        margin: 0 0 25px !important;
    }

    /* Make the table fit inside mobile screen */
    .orders-page table {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        margin: 0 !important;
    }

    /* Table headings */
    .orders-page th {
        padding: 8px 2px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        word-break: break-word !important;
    }

    /* Table cells */
    .orders-page td {
        padding: 9px 2px !important;
        font-size: 11px !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        vertical-align: middle !important;
    }

    /* Column widths */

    /* Order ID */
    .orders-page th:nth-child(1),
    .orders-page td:nth-child(1) {
        width: 15% !important;
    }

    /* Total */
    .orders-page th:nth-child(2),
    .orders-page td:nth-child(2) {
        width: 17% !important;
    }

    /* Status */
    .orders-page th:nth-child(3),
    .orders-page td:nth-child(3) {
        width: 17% !important;
    }

    /* Date */
    .orders-page th:nth-child(4),
    .orders-page td:nth-child(4) {
        width: 28% !important;
    }

    /* Action */
    .orders-page th:nth-child(5),
    .orders-page td:nth-child(5) {
        width: 23% !important;
    }

    /* View Details button/link */
    .orders-page td a {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        padding: 7px 2px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        text-align: center !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
    }
}


/* =========================================
   SMALL PHONES
========================================= */

@media screen and (max-width: 480px) {

    .orders-page {
        padding: 95px 5px 25px !important;
    }

    .orders-page h1 {
        font-size: 22px !important;
        margin-bottom: 20px !important;
    }

    .orders-page th {
        padding: 7px 1px !important;
        font-size: 9px !important;
    }

    .orders-page td {
        padding: 8px 1px !important;
        font-size: 10px !important;
    }

    .orders-page td a {
        padding: 7px 1px !important;
        font-size: 9px !important;
    }
}
</style>

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