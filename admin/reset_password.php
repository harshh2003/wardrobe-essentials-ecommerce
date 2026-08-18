<?php

session_start();

include "../config/database.php";

$message = "";

if (!isset($_SESSION['admin_reset_csrf'])) {
    $_SESSION['admin_reset_csrf'] = bin2hex(random_bytes(32));
}


if (
    !isset($_SESSION['admin_reset_email']) ||
    !isset($_SESSION['admin_reset_otp']) ||
    !isset($_SESSION['admin_reset_otp_expiry'])
) {

    header("Location: forgot_password.php");
    exit();
}


if (isset($_POST['reset_password'])) {

    // Check CSRF token
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['admin_reset_csrf'],
            $_POST['csrf_token']
        )
    ) {

        $message = "Invalid request. Please try again.";

    } else {

        $entered_otp = trim($_POST['otp']);

$new_password = $_POST['new_password'];

$confirm_password = $_POST['confirm_password'];


$maxOtpAttempts = 5;

$otpAttempts = $_SESSION['admin_reset_otp_attempts'] ?? 0;


if ($otpAttempts >= $maxOtpAttempts) {

    $message =
        "Too many incorrect OTP attempts. Please request a new OTP.";

} else {


    if (
        time() >
        $_SESSION['admin_reset_otp_expiry']
    ) {

        $message =
            "OTP has expired. Please request a new OTP.";

    }


    elseif (
    $entered_otp !=
    $_SESSION['admin_reset_otp']
) {

    $_SESSION['admin_reset_otp_attempts']++;

    $remainingAttempts =
        $maxOtpAttempts -
        $_SESSION['admin_reset_otp_attempts'];


    if ($remainingAttempts <= 0) {

        unset($_SESSION['admin_reset_otp']);
        unset($_SESSION['admin_reset_otp_expiry']);

        unset(
    $_SESSION['admin_reset_otp_attempts']
);

unset(
    $_SESSION['admin_reset_csrf']
);

        $message =
            "Too many incorrect OTP attempts. Please request a new OTP.";

    } else {

        $message =
            "Invalid OTP. "
            . $remainingAttempts
            . " attempt(s) remaining.";

    }

}

    elseif (strlen($new_password) < 6) {

        $message =
            "Password must be at least 6 characters.";

    }


    elseif ($new_password !== $confirm_password) {

        $message =
            "Passwords do not match.";

    }


    else {

        $stmt = $conn->prepare(
            "SELECT password
             FROM users
             WHERE email = ?
             AND role = 'admin'
             LIMIT 1"
        );

        $stmt->bind_param(
            "s",
            $_SESSION['admin_reset_email']
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            $current_password =
                $admin['password'];


            if (
                password_verify(
                    $new_password,
                    $current_password
                )
            ) {

                $message =
                    "You cannot use your previous password. Please choose a new password.";

            }


            else {

                $hashed_password =
                    password_hash(
                        $new_password,
                        PASSWORD_DEFAULT
                    );


                $update = $conn->prepare(
                    "UPDATE users
                     SET password = ?
                     WHERE email = ?
                     AND role = 'admin'"
                );

                $update->bind_param(
                    "ss",
                    $hashed_password,
                    $_SESSION['admin_reset_email']
                );


                if ($update->execute()) {

                    unset(
                        $_SESSION['admin_reset_email']
                    );

                    unset(
                        $_SESSION['admin_reset_otp']
                    );

                    unset(
                        $_SESSION['admin_reset_otp_expiry']
                    );


                    header(
                        "Location: login.php?reset=success"
                    );

                    exit();

                } else {

                    $message =
                        "Something went wrong. Please try again.";
                }


                $update->close();
            }

        } else {

            $message =
                "Admin account not found.";
        }


        $stmt->close();
      }
}
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Reset Admin Password</title>

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

.reset-box{
    width:400px;

    background:#fff;

    padding:40px;

    border-radius:16px;

    box-shadow:0 10px 30px rgba(0,0,0,.12);
}

.reset-box h1{
    text-align:center;

    margin-bottom:10px;
}

.reset-box > p{
    text-align:center;

    color:#777;

    margin-bottom:30px;
}

.form-group{
    margin-bottom:18px;
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

.reset-btn{
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

.reset-btn:hover{
    background:#ff9800;
}

.error{
    background:#ffe5e5;

    color:#c00;

    padding:12px;

    border-radius:8px;

    margin-bottom:18px;

    text-align:center;

    font-size:14px;
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

back-login a:hover{
  color:#ff9800;

    text-decoration:underline;
}

</style>

</head>

<body>

<div class="reset-box">

    <h1>Reset Password</h1>

    <p>
        Enter the OTP sent to your admin email
        and create a new password.
    </p>


    <?php if($message !== ''): ?>

        <div class="error">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <form method="POST">

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION['admin_reset_csrf']); ?>"
    >


        <div class="form-group">

            <label>OTP</label>

            <input
               type="text"
                name="otp"
                placeholder="Enter 6-digit OTP"
                maxlength="6"
                inputmode="numeric"
                required
            >

        </div>


        <div class="form-group">

        <label>New Password</label>

            <input
                type="password"
                name="new_password"
                placeholder="Enter new password"
                minlength="6"
                required
            >

        </div>

        <div class="form-group">

            <label>Confirm New Password</label>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                minlength="6"
                required
            >

        </div>


        <button
            type="submit"
            name="reset_password"
            class="reset-btn">

            Reset Password

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