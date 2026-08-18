<?php
session_start();
include 'config/database.php';

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $result->fetch_assoc();


if(!isset($_SESSION['recently_viewed'])){
    $_SESSION['recently_viewed'] = [];
}

$product_id = $product['id'];

$_SESSION['recently_viewed'] = array_diff(
    $_SESSION['recently_viewed'],
    [$product_id]
);

array_unshift($_SESSION['recently_viewed'], $product_id);

$_SESSION['recently_viewed'] = array_slice(
    $_SESSION['recently_viewed'],
    0,
    5
);


$ratingData = $conn->query("
SELECT
AVG(rating) AS average_rating,
COUNT(*) AS total_reviews
FROM reviews
WHERE product_id='".$product['id']."'
")->fetch_assoc();

$average_rating = round($ratingData['average_rating'],1);
$total_reviews = $ratingData['total_reviews'];


$canReview = false;
$hasReviewed = false;

if(isset($_SESSION['user'])){

    $user_id = $_SESSION['user']['id'];
    $product_id = $product['id'];

    $checkPurchase = $conn->query("
        SELECT order_items.id
        FROM order_items
        JOIN orders ON order_items.order_id = orders.id
        WHERE orders.user_id='$user_id'
        AND order_items.product_id='$product_id'
        LIMIT 1
    ");

    $canReview = $checkPurchase->num_rows > 0;

    $alreadyReviewed = $conn->query("
        SELECT id
        FROM reviews
        WHERE product_id='$product_id'
        AND user_id='$user_id'
    ");

    $hasReviewed = $alreadyReviewed->num_rows > 0;
}





include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="product-page">

    <div class="product-container">

        <div class="product-image">
            <img src="assets/images/products/<?php echo $product['main_image']; ?>" alt="">
        </div>

        <div class="product-details">

            <h1><?php echo $product['product_name']; ?></h1>

            <h2>₹<?php echo $product['discount_price']; ?></h2>

            <div class="product-rating">

<?php

for($i=1;$i<=5;$i++){

    if($i <= round($average_rating)){
        echo "⭐";
    }else{
        echo "☆";
    }

}

?>

<span>

<a href="#reviews-section" class="reviews-link">

<?php

if($total_reviews>0){

    echo "(".$average_rating." | ".$total_reviews." Reviews)";

}else{

    echo "(No Reviews Yet)";

}

?>

</a>

</span>

</div>

            <p><?php echo $product['description']; ?></p>

            <h3>Select Size</h3>

<div class="sizes">
    <button class="size-btn active">S</button>
<button class="size-btn">M</button>
<button class="size-btn">L</button>
<button class="size-btn">XL</button>
</div>

<h3>Quantity</h3>

<div class="quantity-box">
    <button type="button" onclick="changeQty(-1)">−</button>

    <input type="number" id="qty" name="quantity" value="1" min="1">

    <button type="button" onclick="changeQty(1)">+</button>
</div>

            <form action="cart.php" method="POST">

    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

    <input type="hidden" name="name" value="<?php echo $product['product_name']; ?>">

    <input type="hidden" name="price" value="<?php echo $product['discount_price']; ?>">

    <input type="hidden" name="image" value="<?php echo $product['main_image']; ?>">

    <input type="hidden" name="qty" id="cartQty" value="1">

    <button type="submit" name="add_to_cart" class="cart-btn">
        Add to Cart
    </button>

</form>

<button class="buy-btn">Buy Now</button>

<a href="add_to_wishlist.php?id=<?php echo $product['id']; ?>" class="wishlist-btn">
    ❤️ Add to Wishlist
</a>







        </div>

        <div class="review-section">

        <div class="write-reviews">

<h2>Write a Review</h2>

<?php if(isset($_SESSION['user'])){ ?>

    <?php if($canReview){ ?>

        <?php if(!$hasReviewed){ ?>

<form action="add_review.php" method="POST">

    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

    <label>Rating</label><br>

    <select name="rating" required>
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
    </select>

    <br><br>

    <textarea
        name="review"
        rows="5"
        placeholder="Write your review..."
        required></textarea>

    <br><br>

    <button type="submit" class="review-btn">
    Submit Review
</button>

</form>

<?php } else { ?>

<p style="color:green;font-weight:bold;">
✅ You have already reviewed this product.
</p>

<?php } ?>

<?php } else { ?>

<p style="color:red;font-weight:bold;">
Purchase this product first to write a review.
</p>

<?php } ?>

<?php } else { ?>

<p class="review-login">
Please <a href="login.php">Login</a> to write a review.
</p>

<?php } ?>

</div>

<div class="customer-review-list">

<h2 class="review-title">Customer Reviews</h2>

<?php

$reviews = $conn->query("
SELECT reviews.*, users.full_name
FROM reviews
JOIN users ON reviews.user_id = users.id
WHERE product_id = '".$product['id']."'
ORDER BY reviews.id DESC
LIMIT 2
");

if (!$reviews) {
    die("SQL Error: " . $conn->error);
}

if($reviews->num_rows > 0){

    while($review = $reviews->fetch_assoc()){

?>

<div class="review-card">

<h3><?php echo $review['full_name']; ?></h3>

<p class="review-stars">

<?php
for($i=1;$i<=5;$i++){

    if($i <= $review['rating']){
        echo "⭐";
    }else{
        echo "☆";
    }

}
?>

</p>

<p><?php echo $review['review']; ?></p>

<small><?php echo $review['created_at']; ?></small>

<?php
if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $review['user_id']){
?>

<div class="review-actions">

    <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="edit-review-btn">
        ✏️ Edit
    </a>

    <a href="delete_review.php?id=<?php echo $review['id']; ?>"
       class="delete-review-btn"
       onclick="return confirm('Delete this review?');">
        🗑 Delete
    </a>

</div>

<?php } ?>

</div>

<?php

    }

}else{

    echo "<p>No reviews yet.</p>";

}

?>

<a href="all_reviews.php?id=<?php echo $product['id']; ?>" class="see-more-btn">
    See More Reviews →
</a>

</div>



    </div>

    </div>



    <section class="related-products">

    <h2>You May Also Like</h2>

    <div class="products-grid">

        <?php

        $related = $conn->query("SELECT * FROM products WHERE id != $id LIMIT 3");

        while($item = $related->fetch_assoc()){

        ?>

        <a href="product.php?id=<?php echo $item['id']; ?>" class="product-link">

            <div class="product-card">

                <span class="sale-badge">SALE</span>

                <img src="assets/images/products/<?php echo $item['main_image']; ?>">

                <h3><?php echo $item['product_name']; ?></h3>

                <p>₹<?php echo $item['discount_price']; ?></p>

            </div>

        </a>

        <?php } ?>

    </div>

</section>

</main>

<script>
function changeQty(value){

    let qty = document.getElementById("qty");

    let hiddenQty = document.getElementById("cartQty");

    let current = parseInt(qty.value);

    current += value;

    if(current < 1){
        current = 1;
    }

    qty.value = current;
    hiddenQty.value = current;

}
</script>



<?php

if(isset($_SESSION['recently_viewed']) && count($_SESSION['recently_viewed']) > 1){

$ids = array_filter(
    $_SESSION['recently_viewed'],
    function($id) use ($product){
        return $id != $product['id'];
    }
);

if(count($ids) > 0){

$idList = implode(",", $ids);

$recent = $conn->query("
SELECT *
FROM products
WHERE id IN ($idList)
ORDER BY FIELD(id,$idList)
");

?>

<section class="related-products">

<h2>Recently Viewed</h2>

<div class="products-grid">

<?php while($item = $recent->fetch_assoc()){ ?>

<a href="product.php?id=<?php echo $item['id']; ?>" class="product-link">

<div class="product-card">

<img src="assets/images/products/<?php echo $item['main_image']; ?>">

<h3><?php echo $item['product_name']; ?></h3>

<p>₹<?php echo $item['discount_price']; ?></p>

</div>

</a>

<?php } ?>

</div>

</section>

<?php

}

}

?>

<?php include 'includes/footer.php'; ?>