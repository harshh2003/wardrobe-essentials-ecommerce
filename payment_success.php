<?php
session_start();

require 'razorpay_config.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = '';

try{

    $attributes = array(

        'razorpay_order_id' => $_GET['order_id'],

        'razorpay_payment_id' => $_GET['payment_id'],

        'razorpay_signature' => $_GET['signature']

    );

    $api->utility->verifyPaymentSignature($attributes);

}
catch(SignatureVerificationError $e){

    $success = false;

    $error = $e->getMessage();

}

if(!$success){

    die("Payment Verification Failed!");

}

include 'config/database.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    die("Cart is empty.");
}

$name = $_SESSION['checkout']['name'];
$email = $_SESSION['checkout']['email'];
$phone = $_SESSION['checkout']['phone'];
$address =
$_SESSION['checkout']['address'] .
", " .
$_SESSION['checkout']['city'] .
" - " .
$_SESSION['checkout']['pincode'];

$user_id = $_SESSION['user']['id'];

$total = 0;

foreach ($_SESSION['cart'] as $item){
    $total += $item['price'] * $item['qty'];
}

if(isset($_SESSION['coupon'])){
    $total -= $_SESSION['coupon']['discount'];
}

$payment_id = $_GET['payment_id'];

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
    'Razorpay',
    'Paid',
    '$payment_id'
)";


if($conn->query($sql)){

    $order_id = $conn->insert_id;

    foreach($_SESSION['cart'] as $item){

        $conn->query("
        INSERT INTO order_items
        (
            order_id,
            product_id,
            product_name,
            price,
            quantity
        )
        VALUES
        (
            '$order_id',
            '".$item['id']."',
            '".$item['name']."',
            '".$item['price']."',
            '".$item['qty']."'
        )
        ");

    }

    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);
    unset($_SESSION['checkout']);

    header("Location: order_success.php");

}else{

    echo $conn->error;

}