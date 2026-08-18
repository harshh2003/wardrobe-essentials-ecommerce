<?php

session_start();

include 'config/database.php';

if (!isset($_SESSION['user'])) {

    header("Location: login.php");

    exit;

}

$user = $_SESSION['user'];

$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE email = ?
    ORDER BY id DESC
");

$stmt->bind_param(
    "s",
    $user['email']
);

$stmt->execute();

$orders = $stmt->get_result();

include 'includes/header.php';
include 'includes/navbar.php';

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Account - Wardrobe Essentials</title>

    <style>

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            font-family:Arial,sans-serif;
            background:#f6f6f6;
            color:#111;
        }

        .account-page{
            max-width:1200px;
            margin:50px auto;
            padding:0 20px;
        }

        .account-header{
            background:#111;
            color:#fff;
            padding:35px;
            border-radius:18px;
            margin-bottom:25px;
        }

        .account-header h1{
            margin:0 0 8px;
            font-size:32px;
        }

        .account-header p{
            margin:0;
            color:#ddd;
        }

        .account-grid{
            display:grid;
            grid-template-columns:280px 1fr;
            gap:25px;
        }

        .account-menu{
            background:#fff;
            padding:20px;
            border-radius:18px;
            height:max-content;
            box-shadow:0 5px 20px rgba(0,0,0,.06);
        }

        .account-menu a{
            display:block;
            padding:14px 16px;
            margin-bottom:8px;
            border-radius:10px;
            text-decoration:none;
            color:#111;
            font-weight:600;
        }

        .account-menu a:hover{
            background:#f1f1f1;
        }

        .account-menu .logout{
            color:#d00;
        }

        .account-content{
            background:#fff;
            padding:30px;
            border-radius:18px;
            box-shadow:0 5px 20px rgba(0,0,0,.06);
        }

        .profile-box{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            margin-bottom:35px;
        }

        .profile-item{
            background:#f7f7f7;
            padding:18px;
            border-radius:12px;
        }

        .profile-item small{
            display:block;
            color:#777;
            margin-bottom:6px;
        }

        .profile-item strong{
            font-size:16px;
        }

        .orders-title{
            margin-bottom:18px;
        }

        .order-card{
            border:1px solid #e5e5e5;
            border-radius:14px;
            padding:20px;
            margin-bottom:15px;
        }

        .order-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:12px;
        }

        .order-id{
            font-weight:bold;
        }

        .status{
            padding:6px 12px;
            border-radius:20px;
            font-size:13px;
            font-weight:bold;
        }

        .pending{
            background:#fff3cd;
            color:#856404;
        }

        .shipped{
            background:#cce5ff;
            color:#004085;
        }

        .delivered{
            background:#d4edda;
            color:#155724;
        }

        .order-details{
            color:#555;
            line-height:1.7;
        }

        .view-order{
            display:inline-block;
            margin-top:10px;
            padding:9px 15px;
            background:#111;
            color:#fff;
            text-decoration:none;
            border-radius:8px;
            font-size:14px;
        }

        .view-order:hover{
            background:#333;
        }

        .no-orders{
            background:#f7f7f7;
            padding:25px;
            border-radius:12px;
            text-align:center;
            color:#777;
        }


        @media(max-width:768px){

            .account-page{
                margin:25px auto;
            }

            .account-grid{
                grid-template-columns:1fr;
            }

            .profile-box{
                grid-template-columns:1fr;
            }

            .account-header{
                padding:25px;
            }

            .account-header h1{
                font-size:26px;
            }

            .account-content{
                padding:20px;
            }

        }

    </style>

</head>


<body>


<?php include 'includes/navbar.php'; ?>


<main class="account-page">


    <div class="account-header">

        <h1>
            My Account 👋
        </h1>

        <p>
            Welcome back,
            <?php echo htmlspecialchars($user['full_name']); ?>!
        </p>

    </div>


    <div class="account-grid">

        <div class="account-menu">

            <a href="account.php">
                👤 My Profile
            </a>

            <a href="my_orders.php">
                📦 My Orders
            </a>

            <a href="wishlist.php">
                ❤️ Wishlist
            </a>

            <a href="cart.php">
                🛒 My Cart
            </a>

            <a href="logout.php" class="logout">
                🚪 Logout
            </a>

        </div>

        <div class="account-content">


            <h2>
                Profile Information
            </h2>


            <div class="profile-box">


                <div class="profile-item">

                    <small>
                        Full Name
                    </small>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $user['full_name']
                        );
                        ?>
                    </strong>

                </div>


                <div class="profile-item">

                    <small>
                        Email Address
                    </small>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $user['email']
                        );
                        ?>
                    </strong>

                </div>


            </div>


            <h2 class="orders-title">
                Recent Orders
            </h2>


            <?php if($orders->num_rows > 0): ?>


                <?php while($order = $orders->fetch_assoc()): ?>


                    <?php

                    $statusClass =
                        strtolower($order['status']);

                    ?>


                    <div class="order-card">


                        <div class="order-top">

                            <span class="order-id">

                                Order #<?php
                                echo $order['id'];
                                ?>

                            </span>


                            <span class="status <?php
                                echo htmlspecialchars(
                                    $statusClass
                                );
                            ?>">

                                <?php
                                echo htmlspecialchars(
                                    $order['status']
                                );
                                ?>

                            </span>

                        </div>


                        <div class="order-details">

                            <strong>
                                Total:
                            </strong>

                            ₹<?php
                            echo number_format(
                                $order['total'],
                                2
                            );
                            ?>

                            <br>

                            <strong>
                                Date:
                            </strong>

                            <?php
                            echo date(
                                "d M Y",
                                strtotime(
                                    $order['created_at']
                                )
                            );
                            ?>

                        </div>


                        <a
                            href="order_details.php?id=<?php echo $order['id']; ?>"
                            class="view-order">

                            View Order

                        </a>


                    </div>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="no-orders">

                    You haven't placed any orders yet. 🛍️

                </div>


            <?php endif; ?>


        </div>


    </div>


</main>


<?php include 'includes/footer.php'; ?>


</body>

</html>