<?php

include "auth.php";
include "../config/database.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f6f8;
            color: #222;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 32px;
        }

        .add-btn {
            background: #22c55e;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .add-btn:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        .table-card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #111;
            color: white;
            padding: 15px;
            text-align: left;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:hover {
            background: #f9fafb;
        }

        .product-img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-name {
            font-weight: bold;
        }

        .price {
            font-weight: bold;
        }

        .actions {
            white-space: nowrap;
        }

        .edit-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            margin-right: 5px;
        }

        .delete-btn {
            display: inline-block;
            background: #ef4444;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .edit-btn:hover {
            background: #1d4ed8;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .top-bar {
    display: flex;
    align-items: center;
    gap: 15px;
}

.top-bar input {
    width: 250px;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    font-size: 14px;
}

.top-bar input:focus {
    border-color: #2563eb;
}

.subtitle {
    margin-top: 6px;
    color: #777;
    font-size: 14px;
}

.back-btn {
    display: inline-block;
    padding: 12px 18px;
    background: #111;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}

.back-btn:hover {
    background: #333;
    transform: translateY(-2px);
}


    </style>
</head>
<body>

<div class="page-header">

    <div>
        <h1>Manage Products</h1>
        <p class="subtitle">Add, edit and manage your store products</p>
    </div>

    <div class="top-bar">

    <a href="index.php" class="back-btn">
        ← Dashboard
    </a>

    <input
        type="text"
        id="searchInput"
        placeholder="🔍 Search Products...">

    <a href="add_product.php" class="add-btn">
        + Add Product
    </a>

</div>

</div>

<table border="1" cellpadding="10" cellspacing="0">

<thead> <!-- -->

<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Name</th>
    <th>Price</th>
    <th>Category</th>
    <th>Actions</th>
</tr>

</thead>

<tbody>

<?php

$result = $conn->query("
SELECT
products.*,
categories.category_name
FROM products
LEFT JOIN categories
ON products.category_id = categories.id
ORDER BY products.id DESC
");

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>
<img
    class="product-img"
    src="../assets/images/products/<?php echo $row['main_image']; ?>"
>
</td>

<td class="product-name">
    <?php echo $row['product_name']; ?>
</td>

<td class="price">
    ₹<?php echo number_format($row['discount_price'], 2); ?>
</td>

<td><?php echo $row['category_name']; ?></td>

<td class="actions">

    <a
        href="edit_product.php?id=<?php echo $row['id']; ?>"
        class="edit-btn">
        ✏️ Edit
    </a>

    <form
    action="delete_product.php"
    method="POST"
    style="display:inline;"
    onsubmit="return confirm('Delete this product?');">

    <input
        type="hidden"
        name="id"
        value="<?php echo $row['id']; ?>">

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <button
        type="submit"
        class="delete-btn">
        Delete
    </button>

</form>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

<script>

const search =
document.getElementById("searchInput");

search.addEventListener("keyup",function(){

let value =
this.value.toLowerCase();

let rows =
document.querySelectorAll("tbody tr");

rows.forEach(function(row){

let text =
row.innerText.toLowerCase();

row.style.display =
text.includes(value)
? ""
: "none";

});

});

</script>.

</body>
</html>