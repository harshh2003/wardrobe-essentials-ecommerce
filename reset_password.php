<?php

session_start();

include 'config/database.php';

$message = "";


if (
    !isset($_SESSION['reset_email']) ||
    !isset($_SESSION['reset_otp'])
) {
    header("Location: forgot_password.php");
    exit();
}


if (isset($_POST['reset_password'])) {

    $entered_otp = trim($_POST['otp']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if (time() > $_SESSION['reset_otp_expiry']) {

        $message = "OTP has expired. Please request a new OTP.";

    }

    elseif ($entered_otp != $_SESSION['reset_otp']) {

        $message = "Invalid OTP. Please enter the correct OTP.";

    }

    elseif (strlen($new_password) < 6) {

        $message = "Password must be at least 6 characters.";

    }

    elseif ($new_password !== $confirm_password) {

        $message = "Passwords do not match.";

    }

    else {

    $stmt = $conn->prepare(
        "SELECT password FROM users WHERE email = ?"
    );

    $stmt->bind_param(
        "s",
        $_SESSION['reset_email']
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        $current_password = $user['password'];


        if (password_verify($new_password, $current_password)) {

            $message = "You cannot use your previous password. Please choose a new password.";

        } else {

            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );


            $update = $conn->prepare(
                "UPDATE users
                 SET password = ?
                 WHERE email = ?"
            );

            $update->bind_param(
                "ss",
                $hashed_password,
                $_SESSION['reset_email']
            );


            if ($update->execute()) {

                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_otp']);
                unset($_SESSION['reset_otp_expiry']);


                header("Location: login.php?reset=success");
                exit();

            } else {

                $message = "Something went wrong. Please try again.";
            }

            $update->close();
        }

    } else {

        $message = "User account not found.";
    }

    $stmt->close();
}
}


include 'includes/header.php';
include 'includes/navbar.php';

?>

<main class="auth-page">

    <div class="auth-container">

        <div class="auth-header">

            <h1>Reset Password</h1>

            <p>
                Enter the OTP sent to your email
                and create a new password.
            </p>

        </div>


        <?php if ($message != ""): ?>

            <div class="auth-error">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form action="" method="POST" class="auth-form">

            <div class="form-group">

                <label for="otp">
                    OTP
                </label>

                <input
                    type="text"
                    id="otp"
                    name="otp"
                    placeholder="Enter 6-digit OTP"
                    maxlength="6"
                    inputmode="numeric"
                    required
                >

            </div>


            <div class="form-group">

                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="Enter new password"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    minlength="6"
                    required
                >

            </div>


            <button
                type="submit"
                name="reset_password"
                class="auth-button"
            >
                Reset Password
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