<nav class="navbar">

    <div class="logo">
        <a href="index.php">Wardrobe Essentials</a>
    </div>

    <!-- Mobile Hamburger -->
    <button type="button" class="menu-toggle" id="menuToggle">
        ☰
    </button>

    <ul class="nav-links" id="navLinks">

        <li><a href="index.php">Home</a></li>
        <li><a href="#">Men</a></li>
        <li><a href="#">Women</a></li>
        <li><a href="#">Accessories</a></li>
        <li><a href="cart.php">Cart</a></li>

        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
        ?>
            <li>
                <a href="account.php">
                    Hi, <?php echo $_SESSION['user']['full_name']; ?>
                </a>
            </li>

            <li><a href="wishlist.php">Wishlist ❤️</a></li>
            <li><a href="my_orders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>

        <?php } else { ?>

            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>

        <?php } ?>

    </ul>

    <form action="search.php" method="GET">
        <input type="text" name="search" placeholder="Search products...">
        <button type="submit">Search</button>
    </form>

</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");

    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", function () {
            navLinks.classList.toggle("active");
        });
    }

});
</script>