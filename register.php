<?php

require 'vendor/autoload.php';

session_start();

include 'config/database.php';

include "../config/mail_config.php";

$message = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $plain_password = $_POST['password'];


    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $message = "This email is already registered.";

    } else {

        $otp = random_int(100000, 999999);

        $password = password_hash(
            $plain_password,
            PASSWORD_DEFAULT
        );


        $_SESSION['signup_name'] = $name;
        $_SESSION['signup_email'] = $email;
        $_SESSION['signup_password'] = $password;

        $_SESSION['signup_otp'] = $otp;

        $_SESSION['signup_otp_expiry'] = time() + (5 * 60);


        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = $mail_username;
            $mail->Password = $mail_password;

            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;


            $mail->setFrom(
                $mail_username,
                'Wardrobe Essentials'
            );

            $mail->addAddress($email, $name);

            $mail->isHTML(true);

            $mail->Subject = 'Your OTP for Wardrobe Essentials';

            $mail->Body = "
                <h2>Email Verification</h2>

                <p>Hello <strong>$name</strong>,</p>

                <p>Your OTP for creating your account is:</p>

                <h1>$otp</h1>

                <p>This OTP is valid for <strong>5 minutes</strong>.</p>

                <p>If you did not request this, please ignore this email.</p>
            ";

            $mail->send();


            header("Location: verify_otp.php");
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

<main class="register-page">

    <div class="register-card">

        <div class="register-header">
            <span class="register-label">WARDROBE ESSENTIALS</span>

            <h1>Create Account</h1>

            <p>
                Create your account and start shopping your style.
            </p>
        </div>

        <?php if ($message != ""): ?>

            <div class="register-error">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form action="" method="POST" class="register-form">

            <div class="form-group">

                <label for="full_name">Full Name</label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    autocomplete="name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                name="register"
                class="register-btn"
            >
                Create Account
            </button>

        </form>


        <div class="register-divider">
            <span>Already have an account?</span>
        </div>


        <a href="login.php" class="login-link">
            Login to your account
        </a>

    </div>

</main>


<?php include 'includes/footer.php'; ?>