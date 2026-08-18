<?php

include "auth.php";
include "../config/database.php";



$id = (int)($_GET['id'] ?? 0);

if($id <= 0){
    die("Invalid product ID.");
}



$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows !== 1){

    $stmt->close();
    die("Product not found.");

}

$product = $result->fetch_assoc();

$stmt->close();



if(isset($_POST['update'])){



    if(
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ){

        die("Invalid security token.");

    }



    $name = trim($_POST['name'] ?? '');

    $description = trim($_POST['description'] ?? '');

    $price = (float)($_POST['price'] ?? 0);

    $stock = (int)($_POST['stock'] ?? 0);



    if(
        $name === '' ||
        $price < 0 ||
        $stock < 0
    ){

        die("Invalid product information.");

    }


    $stmt = $conn->prepare("
        UPDATE products
        SET
            product_name = ?,
            description = ?,
            price = ?,
            discount_price = ?,
            stock = ?
        WHERE id = ?
    ");


    $stmt->bind_param(
        "ssddii",
        $name,
        $description,
        $price,
        $price,
        $stock,
        $id
    );


    if($stmt->execute()){

        $stmt->close();

        echo "<script>
            alert('Product Updated Successfully');
            window.location='products.php';
        </script>";

        exit;

    }


    $stmt->close();

    die("Unable to update product.");

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>

<h1>Edit Product</h1>

<form method="POST">

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <input type="text" name="name"
    value="<?php echo $product['product_name']; ?>" required><br><br>

    <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

    <input type="number" name="price"
    value="<?php echo $product['price']; ?>" required><br><br>

    <input type="number" name="stock"
    value="<?php echo $product['stock']; ?>" required><br><br>

    <button type="submit" name="update">Update Product</button>

</form>

</body>
</html>