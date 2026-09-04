<?php
session_start();

include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$result = $conn->query("
SELECT wishlist.id AS wishlist_id,
       products.*
FROM wishlist
JOIN products
ON wishlist.product_id = products.id
WHERE wishlist.user_id='$user_id'
ORDER BY wishlist.id DESC
");
?>

<style>
/* =========================================
   WISHLIST - MOBILE ONLY
   DESKTOP IS COMPLETELY UNCHANGED
========================================= */

@media screen and (max-width: 768px) {

    .orders-page {
        width: 100% !important;
        max-width: 100% !important;
        padding: 100px 10px 30px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }

    .orders-page h1 {
        text-align: center !important;
        font-size: 24px !important;
        margin: 0 0 25px !important;
    }

    /* Make table fit the phone */
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
        padding: 8px 3px !important;
        font-size: 11px !important;
        word-break: break-word !important;
    }

    /* Table cells */
    .orders-page td {
        padding: 8px 3px !important;
        font-size: 12px !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        vertical-align: middle !important;
    }

    /* Column widths */
    .orders-page th:nth-child(1),
    .orders-page td:nth-child(1) {
        width: 22% !important;
    }

    .orders-page th:nth-child(2),
    .orders-page td:nth-child(2) {
        width: 25% !important;
    }

    .orders-page th:nth-child(3),
    .orders-page td:nth-child(3) {
        width: 15% !important;
    }

    .orders-page th:nth-child(4),
    .orders-page td:nth-child(4) {
        width: 38% !important;
    }

    /* Product image */
    .orders-page td img {
        width: 60px !important;
        height: 70px !important;
        max-width: 100% !important;
        object-fit: cover !important;
        border-radius: 6px !important;
    }

    /* Action buttons */
    .orders-page .move-cart-btn,
    .orders-page .remove-wishlist-btn {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 3px 0 !important;
        padding: 7px 3px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        box-sizing: border-box !important;
        white-space: normal !important;
        word-break: break-word !important;
    }
}


/* Smaller phones */
@media screen and (max-width: 480px) {

    .orders-page {
        padding-left: 6px !important;
        padding-right: 6px !important;
    }

    .orders-page h1 {
        font-size: 22px !important;
    }

    .orders-page th {
        font-size: 10px !important;
    }

    .orders-page td {
        font-size: 11px !important;
        padding: 7px 2px !important;
    }

    .orders-page td img {
        width: 55px !important;
        height: 65px !important;
    }

    .orders-page .move-cart-btn,
    .orders-page .remove-wishlist-btn {
        font-size: 9px !important;
        padding: 7px 2px !important;
    }
}
</style>

<main class="orders-page">

<h1>My Wishlist ❤️</h1>

<table border="1" cellpadding="10">

<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td>
    <img src="assets/images/products/<?php echo $row['main_image']; ?>" width="80">
</td>

<td><?php echo $row['product_name']; ?></td>

<td>₹<?php echo $row['price']; ?></td>

<td>

<a href="move_to_cart.php?id=<?php echo $row['wishlist_id']; ?>" class="move-cart-btn">
    🛒 Move to Cart
</a>

<a href="remove_wishlist.php?id=<?php echo $row['wishlist_id']; ?>" class="remove-wishlist-btn">
    ❌ Remove
</a>

</td>

</tr>

<?php } ?>

</table>

</main>

<?php include 'includes/footer.php'; ?>