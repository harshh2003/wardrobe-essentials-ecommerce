<?php
session_start();

include 'config/database.php';

if (!isset($_SESSION['signup_otp'])) {
    header("Location: register.php");
    exit();
}

$message = "";

if (isset($_POST['verify_otp'])) {

    $entered_otp = trim($_POST['otp']);

    if (time() > $_SESSION['signup_otp_expiry']) {

        $message = "OTP has expired. Please register again.";

    } elseif ($entered_otp == $_SESSION['signup_otp']) {

        $name = $_SESSION['signup_name'];
        $email = $_SESSION['signup_email'];
        $password = $_SESSION['signup_password'];

        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {

$_SESSION['user'] = [
    'id' => $conn->insert_id,
    'full_name' => $name,
    'email' => $email
];

unset($_SESSION['signup_otp']);
unset($_SESSION['signup_otp_expiry']);
unset($_SESSION['signup_name']);
unset($_SESSION['signup_email']);
unset($_SESSION['signup_password']);

header("Location: index.php");
exit();

        } else {

            $message = "Something went wrong. Please try again.";
        }

        $stmt->close();

    } else {

        $message = "Invalid OTP. Please enter the correct OTP.";
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="auth-page">

    <h1>Verify Your Email</h1>

    <p>
        We have sent a 6-digit OTP to your email address.
    </p>

    <?php if ($message != ""): ?>
        <p style="color:red;">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form action="" method="POST">

        <input
            type="text"
            name="otp"
            placeholder="Enter 6-digit OTP"
            maxlength="6"
            required
        >

        <br><br>

        <button type="submit" name="verify_otp">
            Verify OTP
        </button>

    </form>

</main>

<?php include 'includes/footer.php'; ?>