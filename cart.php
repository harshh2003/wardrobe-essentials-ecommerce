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

<style>

/* =========================================
   CART MOBILE UI
   DESKTOP IS COMPLETELY UNCHANGED
========================================= */

@media screen and (max-width: 768px) {

    /* Main cart area */
    main[style*="padding:120px 60px"] {
        padding: 95px 14px 35px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }


    /* Shopping Cart heading */
    main[style*="padding:120px 60px"] > h1 {
        text-align: center !important;
        font-size: 30px !important;
        margin: 0 0 28px !important;
        line-height: 1.2 !important;
    }


    /* =================================
       CART PRODUCT CARD
    ================================= */

    main[style*="padding:120px 60px"] > div[style*="display:flex"] {

        display: flex !important;

        flex-direction: column !important;

        align-items: stretch !important;

        width: 100% !important;

        max-width: 430px !important;

        margin: 0 auto 22px !important;

        padding: 16px !important;

        gap: 0 !important;

        box-sizing: border-box !important;

        border: 1px solid #e5e5e5 !important;

        border-radius: 18px !important;

        background: #fff !important;

        box-shadow: 0 5px 18px rgba(0,0,0,0.07) !important;

    }


    /* =================================
       PRODUCT IMAGE
    ================================= */

    main[style*="padding:120px 60px"] > div[style*="display:flex"] > img {

        width: 100% !important;

        max-width: 100% !important;

        height: 250px !important;

        object-fit: cover !important;

        display: block !important;

        margin: 0 0 18px !important;

        border-radius: 14px !important;

    }


    /* =================================
       PRODUCT DETAILS
    ================================= */

    .cart-details {

        width: 100% !important;

        display: flex !important;

        flex-direction: column !important;

        align-items: stretch !important;

        gap: 5px !important;

        box-sizing: border-box !important;

    }


    /* Product name */

    .cart-details h2 {

        font-size: 21px !important;

        line-height: 1.25 !important;

        margin: 0 0 7px !important;

        text-align: left !important;

    }


    /* Price + subtotal */

    .cart-details p {

        font-size: 15px !important;

        line-height: 1.4 !important;

        margin: 3px 0 !important;

    }


    /* =================================
       QUANTITY
    ================================= */

    .cart-qty {

        display: flex !important;

        align-items: center !important;

        justify-content: flex-start !important;

        gap: 14px !important;

        margin: 13px 0 !important;

    }


    .qty-btn {

        width: 42px !important;

        height: 42px !important;

        min-width: 42px !important;

        display: flex !important;

        align-items: center !important;

        justify-content: center !important;

        border-radius: 10px !important;

        font-size: 22px !important;

    }


    .cart-qty span {

        min-width: 28px !important;

        text-align: center !important;

        font-size: 19px !important;

        font-weight: 600 !important;

    }


    /* =================================
       REMOVE BUTTON
    ================================= */

    .remove-btn {

        width: 100% !important;

        max-width: 100% !important;

        display: block !important;

        box-sizing: border-box !important;

        text-align: center !important;

        margin: 12px 0 0 !important;

        padding: 12px 15px !important;

        border-radius: 10px !important;

        font-size: 15px !important;

        font-weight: 600 !important;

    }


    /* =================================
       TOTAL
    ================================= */

    main[style*="padding:120px 60px"] > h2 {

        width: 100% !important;

        max-width: 430px !important;

        margin: 28px auto 20px !important;

        font-size: 24px !important;

        line-height: 1.3 !important;

    }


    /* =================================
       CHECKOUT
    ================================= */

    .checkout-btn {

        display: block !important;

        width: 100% !important;

        max-width: 430px !important;

        margin: 0 auto !important;

        padding: 14px 18px !important;

        box-sizing: border-box !important;

        text-align: center !important;

        border-radius: 11px !important;

        font-size: 16px !important;

        font-weight: 600 !important;

    }

}


/* =========================================
   SMALL PHONES
========================================= */

@media screen and (max-width: 480px) {

    main[style*="padding:120px 60px"] {

        padding: 90px 12px 30px !important;

    }


    main[style*="padding:120px 60px"] > h1 {

        font-size: 27px !important;

        margin-bottom: 24px !important;

    }


    main[style*="padding:120px 60px"] > div[style*="display:flex"] {

        max-width: 100% !important;

        padding: 13px !important;

        border-radius: 16px !important;

    }


    main[style*="padding:120px 60px"] > div[style*="display:flex"] > img {

        height: 220px !important;

        border-radius: 12px !important;

        margin-bottom: 15px !important;

    }


    .cart-details h2 {

        font-size: 20px !important;

    }


    .cart-details p {

        font-size: 14px !important;

    }


    .qty-btn {

        width: 40px !important;

        height: 40px !important;

        min-width: 40px !important;

    }


    .remove-btn {

        font-size: 14px !important;

        padding: 11px !important;

    }


    main[style*="padding:120px 60px"] > h2 {

        font-size: 22px !important;

    }


    .checkout-btn {

        font-size: 15px !important;

        padding: 13px 15px !important;

    }

}

</style>

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