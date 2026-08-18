<?php

include "auth.php";
include "../config/database.php";



if(isset($_POST['update_status'])){



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

    $status = $_POST['status'] ?? '';



    $allowedStatuses = [
        'Pending',
        'Shipped',
        'Delivered'
    ];


    if(
        $id <= 0 ||
        !in_array($status, $allowedStatuses, true)
    ){

        die("Invalid order information.");

    }



    $stmt = $conn->prepare("
        UPDATE orders
        SET status = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "si",
        $status,
        $id
    );

    $stmt->execute();

    $stmt->close();


    header("Location: orders.php");
    exit;

}

?>