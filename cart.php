<?php

session_start();

if(isset($_GET['action']) && isset($_GET['id'])){

    $id = $_GET['id'];

    if(isset($_SESSION['cart'][$id])){

        if($_GET['action'] == "increase"){
            $_SESSION['cart'][$id]['qty']++;
        }

        if($_GET['action'] == "decrease"){

            $_SESSION['cart'][$id]['qty']--;

            if($_SESSION['cart'][$id]['qty'] < 1){
                unset($_SESSION['cart'][$id]);
            }
        }
        if($_GET['action'] == "remove"){
    unset($_SESSION['cart'][$id]);
}
    }

    header("Location: cart.php");
    exit;
}

if(isset($_POST['add_to_cart'])){

    $id = $_POST['id'];

    $item = [
        "id" => $_POST['id'],
        "name" => $_POST['name'],
        "price" => $_POST['price'],
        "image" => $_POST['image'],
        "qty" => $_POST['qty']
    ];

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][$id] = $item;

    header("Location: cart.php");
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="padding:120px 60px;">

<h1>Shopping Cart</h1>

<?php

if(empty($_SESSION['cart'])){

    echo "<h3>Your cart is empty.</h3>";

}else{

    $total = 0;

    foreach($_SESSION['cart'] as $item){

        $subtotal = $item['price'] * $item['qty'];

        $total += $subtotal;
?>

<div style="display:flex;align-items:center;gap:20px;margin:30px 0;padding:20px;border:1px solid #ddd;border-radius:10px;">

<img src="assets/images/products/<?php echo $item['image']; ?>" width="120">

<div class="cart-details">

<h2><?php echo $item['name']; ?></h2>

<p>Price : ₹<?php echo $item['price']; ?></p>

<div class="cart-qty">

<a class="qty-btn" href="cart.php?action=decrease&id=<?php echo $item['id']; ?>">−</a>

<span><?php echo $item['qty']; ?></span>

<a class="qty-btn" href="cart.php?action=increase&id=<?php echo $item['id']; ?>">+</a>

</div>

<p>Subtotal : ₹<?php echo $subtotal; ?></p>

<a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="remove-btn">
🗑 Remove
</a>

</div>

</div>

<?php
    }

    echo "<h2>Total : ₹".$total."</h2>";

echo '
<br><br>

<a href="checkout.php" class="checkout-btn">
    Proceed to Checkout
</a>
';
}

?>

</main>

<?php include 'includes/footer.php'; ?>