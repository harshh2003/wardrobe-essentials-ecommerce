<?php
include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

$search = $_GET['search'];

$result = $conn->query("SELECT * FROM products WHERE product_name LIKE '%$search%'");
?>

<h1>Search Results</h1>

<div class="products">

<?php
while($row = $result->fetch_assoc()){
?>

<div class="product-card">

<img src="assets/images/<?php echo $row['main_image']; ?>" width="200">

<h3><?php echo $row['product_name']; ?></h3>

<p>₹<?php echo $row['discount_price']; ?></p>

<a href="product.php?id=<?php echo $row['id']; ?>">View Product</a>

</div>

<?php
}
?>

</div>

<?php include 'includes/footer.php'; ?>