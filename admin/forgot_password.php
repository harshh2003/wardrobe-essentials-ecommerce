<?php

require "../vendor/autoload.php";

session_start();

include "../config/database.php";

include "../config/mail_config.php";

$message = "";

if (!isset($_SESSION['admin_forgot_csrf'])) {
    $_SESSION['admin_forgot_csrf'] = bin2hex(random_bytes(32));
}

$message = "";

if (isset($_POST['forgot_password'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['admin_forgot_csrf'],
            $_POST['csrf_token']
        )
    ) {

        $message = "Invalid request. Please try again.";

    } else {

        $email = trim($_POST['email']);


    $stmt = $conn->prepare(
        "SELECT id, full_name, email
         FROM users
         WHERE email = ?
         AND role = 'admin'
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows === 0) {

        $message = "No admin account found with this email.";

    } else {

        $admin = $result->fetch_assoc();

        $name = $admin['full_name'];


        $otp = random_int(100000, 999999);


        $_SESSION['admin_reset_email'] = $email;

        $_SESSION['admin_reset_otp'] = $otp;

        $_SESSION['admin_reset_otp_expiry'] =
            time() + (5 * 60);


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
                'Wardrobe Essentials Admin'
            );

            $mail->addAddress(
                $email,
                $name
            );

            $mail->isHTML(true);

            $mail->Subject =
                'Admin Password Reset OTP';


            $mail->Body = "

                <h2>Admin Password Reset</h2>

                <p>
                    Hello <strong>$name</strong>,
                </p>

                <p>
                    We received a request to reset
                    your Wardrobe Essentials admin password.
                </p>

                <p>Your OTP is:</p>

                <h1>$otp</h1>

                <p>
                    This OTP is valid for
                    <strong>5 minutes</strong>.
                </p>

                <p>
                    If you did not request this,
                    please ignore this email.
                </p>

            ";


            $mail->send();


            header("Location: reset_password.php");

            exit();


        } catch (\PHPMailer\PHPMailer\Exception $e) {

            $message =
                "Mailer Error: " . $mail->ErrorInfo;
        }
    }

    $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Forgot Password</title>

<style>

*{
    box-sizing:border-box;
    margin:0;
    padding:0;
    font-family:Arial,sans-serif;
}

body{
    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:#f4f4f4;
}

.login-box{
    width:400px;

    background:#fff;

    padding:40px;

    border-radius:16px;

    box-shadow:0 10px 30px rgba(0,0,0,.12);
}

.login-box h1{
    text-align:center;

    margin-bottom:10px;
}

.login-box p{
    text-align:center;

    color:#777;

    margin-bottom:30px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;

    margin-bottom:7px;

    font-weight:600;
}

.form-group input{
    width:100%;

    padding:13px;

    border:1px solid #ddd;

    border-radius:8px;

    font-size:15px;

    outline:none;
}

.form-group input:focus{
    border-color:#111;
}

.login-btn{
    width:100%;

    padding:14px;

    border:none;

    border-radius:8px;

    background:#111;

    color:#fff;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;
}

.login-btn:hover{
    background:#ff9800;
}

.error{
    background:#ffe5e5;

    color:#c00;

    padding:12px;

    border-radius:8px;

    margin-bottom:18px;

    text-align:center;
}

.back-login{
    text-align:center;

    margin-top:20px;
}

.back-login a{
    color:#555;

    text-decoration:none;

    font-size:14px;
}

.back-login a:hover{
    color:#ff9800;

    text-decoration:underline;
}

</style>

</head>

<body>

<div class="login-box">

    <h1>Forgot Password?</h1>

    <p>
        Enter your admin email to receive an OTP.
    </p>


    <?php if($message !== ''): ?>

        <div class="error">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <form method="POST">

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION['admin_forgot_csrf']); ?>"
    >

        <div class="form-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Admin email"
                required
            >

        </div>


        <button
            type="submit"
            name="forgot_password"
            class="login-btn">

            Send OTP

        </button>

    </form>


    <div class="back-login">

        <a href="login.php">
            ← Back to Admin Login
        </a>

    </div>

</div>

</body>

</html>