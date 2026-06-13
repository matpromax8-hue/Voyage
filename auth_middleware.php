<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0]);
    session_start();
}

define('SESSION_TIMEOUT', 300);

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function checkSessionTimeout(): void {
    if (!isLoggedIn()) {
        return;
    }
    $lastActivity = $_SESSION['last_activity'] ?? 0;
    if (time() - $lastActivity > SESSION_TIMEOUT) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    checkSessionTimeout();
    echo '<script src="auto-logout.js"></script>';
}

function getLoggedInUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function getLoggedInUserEmail(): ?string {
    return $_SESSION['user_email'] ?? null;
}
