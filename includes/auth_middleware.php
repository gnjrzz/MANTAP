<?php
if (!function_exists('checkLogin')) {
    function checkLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /MANTAP/login.php");
            exit;
        }
    }
}

if (!function_exists('checkRole')) {
    function checkRole($allowed_roles)
    {
        checkLogin();
        if (!in_array($_SESSION['role_name'], $allowed_roles)) {
            // Redirect to a default dashboard if not authorized
            header("Location: /MANTAP/index.php?error=unauthorized");
            exit;
        }
    }
}
?>