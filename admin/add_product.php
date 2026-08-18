<?php

include "auth.php";
include "../config/database.php";


if(isset($_POST['add_product'])){



    if(
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ){

        die("Invalid security token.");

    }



    $category = (int)($_POST['category'] ?? 0);

    $name = trim($_POST['name'] ?? '');

    $description = trim($_POST['description'] ?? '');

    $price = (float)($_POST['price'] ?? 0);

    $discount = (float)($_POST['discount'] ?? 0);

    $stock = (int)($_POST['stock'] ?? 0);

    $brand = trim($_POST['brand'] ?? '');



    if(
        $category <= 0 ||
        $name === '' ||
        $price < 0 ||
        $discount < 0 ||
        $stock < 0
    ){

        die("Invalid product information.");

    }



    $image = $_FILES['image']['name'] ?? '';

    $temp = $_FILES['image']['tmp_name'] ?? '';


    if($image === '' || $temp === ''){

        die("Please select a product image.");

    }



    $extension = strtolower(
        pathinfo($image, PATHINFO_EXTENSION)
    );

    $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];


    if(!in_array($extension, $allowedExtensions, true)){

        die("Invalid image format.");

    }


    $newImageName =
        uniqid('product_', true) . '.' . $extension;


    $uploadPath =
        "../assets/images/products/" . $newImageName;


    if(!move_uploaded_file($temp, $uploadPath)){

        die("Unable to upload image.");

    }



    $stmt = $conn->prepare("
        INSERT INTO products
        (
            category_id,
            product_name,
            description,
            price,
            discount_price,
            stock,
            brand,
            main_image
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");


    $stmt->bind_param(
        "issddiss",
        $category,
        $name,
        $description,
        $price,
        $discount,
        $stock,
        $brand,
        $newImageName
    );


    if($stmt->execute()){

        $stmt->close();

        echo "<script>
            alert('Product Added Successfully');
            window.location='products.php';
        </script>";

        exit;

    }


    $stmt->close();

    die("Unable to add product.");

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>

<h1>Add Product</h1>

<form method="POST" enctype="multipart/form-data">

<input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

Category ID:<br>
<input type="number" name="category" required><br><br>

Product Name:<br>
<input type="text" name="name" required><br><br>

Description:<br>
<textarea name="description"></textarea><br><br>

Price:<br>
<input type="number" step="0.01" name="price" required><br><br>

Discount Price:<br>
<input type="number" step="0.01" name="discount" required><br><br>

Stock:<br>
<input type="number" name="stock" required><br><br>

Brand:<br>
<input type="text" name="brand"><br><br>

Image:<br>
<input type="file" name="image" required><br><br>

<button type="submit" name="add_product">
Add Product
</button>

</form>

</body>
</html>