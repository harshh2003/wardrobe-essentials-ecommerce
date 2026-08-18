<?php
session_start();

include 'config/database.php';

$message = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // Login successful
            $_SESSION['user'] = $user;

            header("Location: index.php");
            exit();

        } else {

            $message = "Incorrect password. Please try again.";

        }

    } else {

        $message = "No account found with this email.";

    }

    $stmt->close();
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="auth-page">

    <div class="auth-container">

        <div class="auth-header">

            <h1>Welcome Back</h1>

            <p>
                Login to your Wardrobe Essentials account
            </p>

        </div>


        <?php if ($message != ""): ?>

            <div class="auth-error">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form action="" method="POST" class="auth-form">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <div class="forgot-password">
    <a href="forgot_password.php">Forgot Password?</a>
</div>


            <button
                type="submit"
                name="login"
                class="auth-button"
            >
                Login
            </button>

        </form>


        <div class="auth-footer">

            <p>
                Don't have an account?
                <a href="register.php">Create Account</a>
            </p>

        </div>

    </div>

</main>


<?php include 'includes/footer.php'; ?>