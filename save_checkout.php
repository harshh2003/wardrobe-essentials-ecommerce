<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit("Invalid Request");
}

$_SESSION['checkout'] = [

    "name" => $_POST['name'] ?? "",
    "email" => $_POST['email'] ?? "",
    "phone" => $_POST['phone'] ?? "",
    "address" => $_POST['address'] ?? "",
    "city" => $_POST['city'] ?? "",
    "pincode" => $_POST['pincode'] ?? ""

];

echo "success";