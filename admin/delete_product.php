<?php

include "auth.php";
include "../config/database.php";



if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: products.php");
    exit;
}



if(
    !isset($_POST['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
){

    die("Invalid security token.");

}



$id = (int)($_POST['id'] ?? 0);



if($id <= 0){
    die("Invalid product ID.");
}



$stmt = $conn->prepare("
    DELETE FROM products
    WHERE id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->close();



header("Location: products.php");
exit;

?>