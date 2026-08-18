<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

$product_id = $_GET['id'];

$product = $conn->query("SELECT product_name, main_image FROM products WHERE id='$product_id'");
$product = $product->fetch_assoc();

$reviews = $conn->query("
SELECT reviews.*, users.full_name
FROM reviews
JOIN users ON reviews.user_id = users.id
WHERE product_id='$product_id'
ORDER BY reviews.id DESC
");
?>

<div class="all-reviews-page">

    <div class="reviews-header">
        <h1>Customer Reviews</h1>
        <div class="review-product-image">
    <img src="assets/images/products/<?php echo $product['main_image']; ?>" alt="">
</div>
        <p><?php echo $product['product_name']; ?></p>
    </div>

    <?php

    if($reviews->num_rows > 0){

        while($review = $reviews->fetch_assoc()){

    ?>

    <div class="review-box">

        <div class="review-top">

            <h3><?php echo htmlspecialchars($review['full_name']); ?></h3>

            <span class="review-date">
                <?php echo date("d M Y", strtotime($review['created_at'])); ?>
            </span>

        </div>

        <div class="review-stars">

            <?php
            for($i=1;$i<=5;$i++){
                if($i <= $review['rating']){
                    echo "⭐";
                }else{
                    echo "☆";
                }
            }
            ?>

        </div>

        <p class="review-text">
            <?php echo nl2br(htmlspecialchars($review['review'])); ?>
        </p>

    </div>

    <?php

        }

    }else{

        echo "<div class='no-review'>No reviews available.</div>";

    }

    ?>

    <div class="back-btn-wrap">
        <a href="product.php?id=<?php echo $product_id; ?>" class="back-btn">
            ← Back to Product
        </a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>