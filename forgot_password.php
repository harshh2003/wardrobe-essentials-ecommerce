<?php

require 'vendor/autoload.php';

session_start();

include 'config/database.php';

include "../config/mail_config.php";

$message = "";
$success = "";

if (isset($_POST['forgot_password'])) {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare(
        "SELECT id, full_name FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {

        $message = "No account found with this email.";

    } else {

        $user = $result->fetch_assoc();

        $name = $user['full_name'];

        $otp = random_int(100000, 999999);

        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;

        $_SESSION['reset_otp_expiry'] = time() + (5 * 60);


        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = $mail_username;

            $mail->Password = $mail_password;

            $mail->SMTPSecure =
                \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = 587;


            $mail->setFrom(
                $mail_username,
                'Wardrobe Essentials'
            );

            $mail->addAddress($email, $name);

            $mail->isHTML(true);

            $mail->Subject =
                'Password Reset OTP - Wardrobe Essentials';

            $mail->Body = "

                <h2>Password Reset</h2>

                <p>Hello <strong>$name</strong>,</p>

                <p>
                    We received a request to reset your
                    Wardrobe Essentials password.
                </p>

                <p>Your OTP is:</p>

                <h1>$otp</h1>

                <p>
                    This OTP is valid for
                    <strong>5 minutes</strong>.
                </p>

                <p>
                    If you did not request a password reset,
                    please ignore this email.
                </p>

            ";

            $mail->send();

            header("Location: reset_password.php");
            exit();

        } catch (\PHPMailer\PHPMailer\Exception $e) {

            $message = "Mailer Error: " . $mail->ErrorInfo;
        }
    }

    $stmt->close();
}


include 'includes/header.php';
include 'includes/navbar.php';

?>

<main class="auth-page">

    <div class="auth-container">

        <div class="auth-header">

            <h1>Forgot Password?</h1>

            <p>
                Enter your email address and we'll
                send you an OTP to reset your password.
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


            <button
                type="submit"
                name="forgot_password"
                class="auth-button"
            >
                Send OTP
            </button>

        </form>


        <div class="auth-footer">

            <p>
                Remember your password?
                <a href="login.php">Back to Login</a>
            </p>

        </div>

    </div>

</main>


<?php include 'includes/footer.php'; ?>