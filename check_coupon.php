<?php
session_start();
include 'config/database.php';

$coupon_code = trim($_POST['coupon']);
$total = $_POST['total'];

$response = [
    "success" => false,
    "discount" => 0,
    "message" => ""
];

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

        $response["success"] = true;
$response["discount"] = $discount;

$_SESSION['coupon'] = [
    "code" => $coupon['code'],
    "discount" => $discount
];;

    }else{

        $response["message"] =
        "Minimum order should be ₹".$coupon['min_order'];

    }

}else{

    unset($_SESSION['coupon']);

$response["message"] = "Invalid Coupon";

}

echo json_encode($response);