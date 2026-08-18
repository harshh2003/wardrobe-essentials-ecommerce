<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$review_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

$result = $conn->query("
SELECT *
FROM reviews
WHERE id='$review_id'
");

if($result->num_rows == 0){
    die("Review not found.");
}

$review = $result->fetch_assoc();

if($review['user_id'] != $user_id){
    die("Access Denied.");
}

if(isset($_POST['update_review'])){

    $rating = $_POST['rating'];
    $review_text = $_POST['review'];

    $conn->query("
    UPDATE reviews
    SET rating='$rating',
        review='$review_text'
    WHERE id='$review_id'
    ");

    header("Location: product.php?id=".$review['product_id']);
    exit;
}
?>

<main class="checkout-page">

<h1>Edit Review</h1>

<form method="POST">

<label>Rating</label>

<select name="rating">

<?php for($i=5;$i>=1;$i--){ ?>

<option value="<?php echo $i; ?>"
<?php if($review['rating']==$i) echo "selected"; ?>>

<?php
for($j=1;$j<=$i;$j++) echo "⭐";
?>

</option>

<?php } ?>

</select>

<br><br>

<label>Review</label>

<textarea
name="review"
rows="6"
required><?php echo htmlspecialchars($review['review']); ?></textarea>

<br><br>

<button type="submit" name="update_review" class="review-btn">
Update Review
</button>

</form>

</main>

<?php include 'includes/footer.php'; ?>