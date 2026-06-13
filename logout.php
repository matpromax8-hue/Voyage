<?php
require_once 'auth_middleware.php';
$_SESSION = [];
session_destroy();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
$redirect = isset($_GET['expired']) ? 'login.php?expired=1' : 'login.php';
header('Location: ' . $redirect);
exit;
