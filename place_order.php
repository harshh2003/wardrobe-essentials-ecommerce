<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    die("Cart is empty.");
}

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$user_id = $_SESSION['user']['id'];
$payment_method = $_POST['payment_method'];

if($payment_method == "COD"){
    $payment_status = "Pending";
}else{
    $payment_status = "Paid";
}

$payment_id = NULL;
$coupon_code = trim($_POST['coupon_code']);

$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}

$discount = 0;

if($coupon_code != ''){

    $coupon = $conn->query("
    SELECT *
    FROM coupons
    WHERE code='$coupon_code'
    AND status='active'
    AND expiry_date >= CURDATE()
    ");

    if($coupon->num_rows > 0){

        $coupon = $coupon->fetch_assoc();

        if($total >= $coupon['min_order']){

            if($coupon['discount_type'] == "percentage"){

                $discount = ($total * $coupon['discount_value']) / 100;

            }else{

                $discount = $coupon['discount_value'];

            }

        }else{

            echo "<script>
            alert('Minimum order should be ₹".$coupon['min_order']."');
            history.back();
            </script>";
            exit;
        }

    }else{

        echo "<script>
        alert('Invalid or Expired Coupon');
        history.back();
        </script>";
        exit;
    }

}

$total = $total - $discount;

if($total < 0){
    $total = 0;
}

$sql = "INSERT INTO orders
(
    user_id,
    fullname,
    email,
    phone,
    address,
    total,
    payment_method,
    payment_status,
    payment_id
)
VALUES
(
    '$user_id',
    '$name',
    '$email',
    '$phone',
    '$address',
    '$total',
    '$payment_method',
    '$payment_status',
    '$payment_id'
)";

if ($conn->query($sql)) {

    $order_id = $conn->insert_id;

    foreach ($_SESSION['cart'] as $item) {

        $product_id = $item['id'];
        $product_name = $item['name'];
        $price = $item['price'];
        $quantity = $item['qty'];

        $conn->query("
            INSERT INTO order_items
            (order_id, product_id, product_name, price, quantity)
            VALUES
            ('$order_id', '$product_id', '$product_name', '$price', '$quantity')
        ");
    }

    unset($_SESSION['cart']);
unset($_SESSION['coupon']);

    echo "<script>
        alert('Order Placed Successfully!');
        window.location='index.php';
    </script>";

} else {
    echo "Error: " . $conn->error;
}
?>