<?php
include 'config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 6;

$offset = ($page - 1) * $limit;

$category = isset($_GET['category']) ? $_GET['category'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$brand = isset($_GET['brand']) ? $_GET['brand'] : '';

$sql = "SELECT * FROM products WHERE 1";

if($category != ''){
    $sql .= " AND category_id='$category'";
}

if($price == "1000"){
    $sql .= " AND discount_price <= 1000";
}
elseif($price == "2000"){
    $sql .= " AND discount_price BETWEEN 1001 AND 2000";
}
elseif($price == "5000"){
    $sql .= " AND discount_price > 2000";
}

if($brand != ''){
    $sql .= " AND brand='$brand'";
}

if($sort == "low"){
    $sql .= " ORDER BY discount_price ASC";
}
elseif($sort == "high"){
    $sql .= " ORDER BY discount_price DESC";
}

$sql .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$countSql = "SELECT COUNT(*) AS total FROM products WHERE 1";

if($category != ''){
    $countSql .= " AND category_id='$category'";
}

if($price == "1000"){
    $countSql .= " AND discount_price <= 1000";
}
elseif($price == "2000"){
    $countSql .= " AND discount_price BETWEEN 1001 AND 2000";
}
elseif($price == "5000"){
    $countSql .= " AND discount_price > 2000";
}

if($brand != ''){
    $countSql .= " AND brand='$brand'";
}

$countQuery = $conn->query($countSql);

$totalProducts = $countQuery->fetch_assoc()['total'];

$totalPages = ceil($totalProducts / $limit);

$categories = $conn->query("SELECT * FROM categories");
$brands = $conn->query("
SELECT DISTINCT brand
FROM products
ORDER BY brand
");
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/navbar.php'; ?>

<main>

    <section class="hero">
        <div class="hero-content">
            <h1>Wardrobe Essentials</h1>
            <p>Everyday Style. Timeless Essentials.</p>

            <a href="#" class="shop-btn">
                Shop Now
            </a>
        </div>
    </section>

    <section class="filter-section">

<form method="GET" id="filterForm">

<div class="filter-group">

<label><strong>Category</strong></label>

<select name="category" onchange="document.getElementById('filterForm').submit();">

<option value="">All Categories</option>

<?php while($cat = $categories->fetch_assoc()){ ?>

<option
value="<?php echo $cat['id']; ?>"

<?php
if($category == $cat['id']){
    echo "selected";
}
?>

>

<?php echo $cat['category_name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="filter-group">

<label><strong>Price:</strong></label>

<select name="price" onchange="document.getElementById('filterForm').submit();">

    <option value="">All Prices</option>

    <option value="1000" <?php if($price=="1000") echo "selected"; ?>>
        Below ₹1000
    </option>

    <option value="2000" <?php if($price=="2000") echo "selected"; ?>>
        ₹1001 - ₹2000
    </option>

    <option value="5000" <?php if($price=="5000") echo "selected"; ?>>
        Above ₹2000
    </option>

</select>

</div>

<div class="filter-group">

<label><strong>Sort:</strong></label>

<select name="sort" onchange="document.getElementById('filterForm').submit();">

    <option value="">Default</option>

    <option value="low" <?php if($sort=="low") echo "selected"; ?>>
        Price: Low → High
    </option>

    <option value="high" <?php if($sort=="high") echo "selected"; ?>>
        Price: High → Low
    </option>

</select>

</div>

<div class="filter-group">

<label><strong>Brand:</strong></label>

<select name="brand" onchange="document.getElementById('filterForm').submit();">

    <option value="">All Brands</option>

    <?php while($b = $brands->fetch_assoc()){ ?>

        <option
            value="<?php echo $b['brand']; ?>"
            <?php if($brand == $b['brand']) echo "selected"; ?>>

            <?php echo $b['brand']; ?>

        </option>

    <?php } ?>

</select>

</div>

<a href="index.php" class="clear-filter-btn">
    Clear Filters
</a>

</form>

</section>
    
    <section class="featured-products">
    <h2>Featured Products</h2>

    <div class="products-grid">

        <?php while($product = $result->fetch_assoc()) { ?>

        <a href="product.php?id=<?php echo $product['id']; ?>" class="product-link">

        <div class="product-card">
    
<span class="sale-badge">SALE</span>

            <img src="assets/images/products/<?php echo $product['main_image']; ?>" alt="">

            <h3><?php echo $product['product_name']; ?></h3>

            <p>₹<?php echo $product['discount_price']; ?></p>

            <button>Add to Cart</button>

            <button class="wishlist-btn" onclick="addToWishlist(<?php echo $product['id']; ?>); return false;">
    ❤️ Add to Wishlist
</button>
        </div>

</a>

        <?php } ?>

    </div>

    <?php
$query = $_GET;
?>

<?php
$query = $_GET;

if($totalPages > 1){
?>

<div class="pagination">

    <?php if($page > 1){ ?>

        <?php
        $query['page'] = $page - 1;
        ?>

        <a href="?<?php echo http_build_query($query); ?>">
            ← Previous
        </a>

    <?php } ?>

    <?php for($i=1; $i<=$totalPages; $i++){ ?>

        <?php
        $query['page'] = $i;
        ?>

        <a href="?<?php echo http_build_query($query); ?>"
           class="<?php if($page == $i) echo 'active-page'; ?>">

            <?php echo $i; ?>

        </a>

    <?php } ?>

    <?php if($page < $totalPages){ ?>

        <?php
        $query['page'] = $page + 1;
        ?>

        <a href="?<?php echo http_build_query($query); ?>">
            Next →
        </a>

    <?php } ?>

</div>

<?php } ?>

</section>

</main>

<?php include 'includes/footer.php'; ?>