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