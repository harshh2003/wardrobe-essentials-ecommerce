<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$conn->query("DELETE FROM wishlist WHERE id='$id'");

header("Location: wishlist.php");
exit;
?>