<?php

session_start();

include "../config/database.php";

$error = "";

if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true){
    header("Location: index.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    $maxAttempts = 5;
    $lockoutTime = 15 * 60; // 15 minutes



    $attempts = $_SESSION['admin_login_attempts'] ?? 0;
    $lockedUntil = $_SESSION['admin_locked_until'] ?? 0;



    if($lockedUntil > time()){

        $remaining = ceil(
            ($lockedUntil - time()) / 60
        );

        $error =
            "Too many failed attempts. "
            . "Try again in {$remaining} minute(s).";

    }

    else{


        if($lockedUntil > 0 && $lockedUntil <= time()){

            $_SESSION['admin_login_attempts'] = 0;
            $_SESSION['admin_locked_until'] = 0;

            $attempts = 0;

        }


        if($email === '' || $password === ''){

            $error = "Please enter email and password.";

        }

        else{

            $stmt = $conn->prepare("
                SELECT id, email, password, role
                FROM users
                WHERE email = ?
                AND role = 'admin'
                LIMIT 1
            ");

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();


            if($result->num_rows === 1){

                $user = $result->fetch_assoc();

            }else{

                $user = null;

            }



            if(
                $user &&
                password_verify($password, $user['password'])
            ){


                session_regenerate_id(true);

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];


                unset($_SESSION['admin_login_attempts']);
                unset($_SESSION['admin_locked_until']);

                header("Location: index.php");
                exit;

            }

            else{


                $_SESSION['admin_login_attempts'] =
                    $attempts + 1;


                if(
                    $_SESSION['admin_login_attempts']
                    >= $maxAttempts
                ){

                    $_SESSION['admin_locked_until'] =
                        time() + $lockoutTime;

                    $_SESSION['admin_login_attempts'] = 0;

                    $error =
                        "Too many failed attempts. "
                        . "Try again in 15 minutes.";

                }

                else{

                    $remainingAttempts =
                        $maxAttempts -
                        $_SESSION['admin_login_attempts'];

                    $error =
                        "Invalid email or password. "
                        . $remainingAttempts
                        . " attempt(s) remaining.";

                }

            }


            $stmt->close();

        }

    }

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Login</title>

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
    margin-bottom:8px;
}

.login-box p{
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

.forgot-password{
    text-align:center;
    margin-top:18px;
}

.forgot-password a{
    color:#555;
    font-size:15px;
    text-decoration:none;
    font-weight: 600;
}

.forgot-password a:hover{
    color:#ff9800;
    text-decoration:underline;
}

</style>

</head>

<body>

<div class="login-box">

    <h1>Wardrobe Admin</h1>

    <p>Admin Login</p>

    <?php if($error !== ''): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="form-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Admin email"
                required>

        </div>

        <div class="form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Admin password"
                required>

        </div>

        <button
            type="submit"
            class="login-btn">
            Login
        </button>

        <div class="forgot-password">
    <a href="forgot_password.php">Forgot Password?</a>
</div>

    </form>

</div>

</body>

</html>