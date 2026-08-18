<?php


if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}



if (
    !isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true
) {

    header("Location: login.php");
    exit;

}



$sessionTimeout = 1800;

if (
    isset($_SESSION['admin_last_activity']) &&
    (time() - $_SESSION['admin_last_activity']) > $sessionTimeout
) {

    $_SESSION = [];

    session_destroy();

    header("Location: login.php?timeout=1");
    exit;

}



$_SESSION['admin_last_activity'] = time();



if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));

}