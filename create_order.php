<?php
session_start();

require 'razorpay_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Cart is empty."
    ]);
    exit;
}

$total = 0;

foreach ($_SESSION['cart'] as $item){
    $total += $item['price'] * $item['qty'];
}

if(isset($_SESSION['coupon'])){
    $total -= $_SESSION['coupon']['discount'];
}

if($total < 1){
    $total = 1;
}

$order = $api->order->create([
    'receipt' => 'order_' . time(),
    'amount' => $total * 100, // Razorpay expects paise
    'currency' => 'INR'
]);

echo json_encode([
    "success" => true,
    "order_id" => $order['id'],
    "amount" => $order['amount']
]);