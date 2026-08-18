<?php
session_start();
if(!isset($_SESSION['user'])){
    echo "<script>
            alert('Please login first.');
            window.location='login.php';
          </script>";
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "<h2>Your cart is empty.</h2>";
    exit;
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}
?>

<main class="checkout-page">

    <h1>Checkout</h1>

    <form
action="place_order.php"
method="POST"
id="checkoutForm">

    <label>Full Name</label>
    <input type="text" name="name" required>

    <label>Email Address</label>
    <input type="email" name="email" required>

    <label>Phone Number</label>
    <input type="text" name="phone" required>

    <label>Delivery Address</label>
    <textarea name="address" rows="4" required></textarea>

    <label>City</label>
    <input type="text" name="city" required>

    <label>Pincode</label>
    <input type="text" name="pincode" required>

    <h3>Payment Method</h3>

<div class="payment-method">

    <label class="payment-option">
        <input type="radio" name="payment_method" value="COD" checked>
        <span>🚚 Cash on Delivery (COD)</span>
    </label>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="Razorpay">
        <span>💳 Pay Online (Razorpay)</span>
    </label>

</div>

    <h2>Total: ₹<?php echo $total; ?></h2>

    <hr>

<h3>Apply Coupon</h3>

<input
type="text"
name="coupon_code"
id="coupon_code"
placeholder="Enter Coupon Code"
value="<?php echo isset($_SESSION['coupon']) ? $_SESSION['coupon']['code'] : ''; ?>">

<?php if(isset($_SESSION['coupon'])){ ?>

<p class="coupon-success">
    ✅ Coupon <strong><?php echo $_SESSION['coupon']['code']; ?></strong> applied successfully!
</p>

<?php } ?>

<button
type="button"
class="apply-coupon-btn"
onclick="applyCoupon()">

Apply Coupon

</button>

<p style="font-size:14px;color:gray;">
Try: <strong>WELCOME10</strong> or <strong>SAVE500</strong>
</p>

<?php

?>

<div class="order-summary">

<h3>Order Summary</h3>

<div class="summary-row">
    <span>Subtotal</span>
    <span>₹<?php echo number_format($total, 2); ?></span>
</div>

<div class="summary-row">
    <span>Discount</span>

    <span id="discountAmount">
        ₹<?php
        $discount = isset($_SESSION['coupon'])
            ? $_SESSION['coupon']['discount']
            : 0;

        echo number_format($discount, 2);
        ?>
    </span>
</div>

<hr>

<div class="summary-row total-row">
    <span>Total</span>

    <span id="finalTotal">
        ₹<?php echo number_format($total - $discount, 2); ?>
    </span>
</div>

</div>

</div>

    <button
type="submit"
id="placeOrderBtn"
class="checkout-btn">

Place Order

</button>

</form>

</main>

<script>

function applyCoupon(){

    let coupon =
    document.getElementById("coupon_code").value;

    let total =
    <?php echo $total; ?>;

    fetch("check_coupon.php",{

        method:"POST",

        headers:{
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        "coupon="+coupon+"&total="+total

    })

    .then(res=>res.json())

    .then(data=>{

        if(data.success){

            document.getElementById("discountAmount")
            .innerHTML="₹"+data.discount;

            document.getElementById("finalTotal")
            .innerHTML="₹"+(total-data.discount);

            location.reload();
        }else{

            alert(data.message);

        }

    });

}



    document.getElementById("placeOrderBtn").addEventListener("click", function(e){

    let paymentMethod =
    document.querySelector('input[name="payment_method"]:checked').value;

    if(paymentMethod == "COD"){
    return;
}

e.preventDefault();

let form = document.getElementById("checkoutForm");

let formData = new FormData(form);

console.log([...formData.entries()]);

fetch("save_checkout.php",{

    method:"POST",

    body:formData

})

.then(res=>res.text())

.then(result=>{

    if(result != "success"){

        alert("Unable to save checkout details.");

        return;

    }

    return fetch("create_order.php");

})

.then(response=>response.json())

.then(data => {

    if(!data.success){
        alert(data.message);
        return;
    }

    var options = {

        key: "rzp_test_TMoOQxJOGvDlmi",

        amount: data.amount,

        currency: "INR",

        name: "Wardrobe Essentials",

        description: "Order Payment",

        order_id: data.order_id,

        handler: function (response) {

            window.location =
            "payment_success.php?payment_id=" +
            response.razorpay_payment_id +
            "&order_id=" +
            response.razorpay_order_id +
            "&signature=" +
            response.razorpay_signature;

        },

        theme: {
            color: "#000000"
        }

    };

    var rzp = new Razorpay(options);

    rzp.open();

});

});

</script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<?php include 'includes/footer.php'; ?>