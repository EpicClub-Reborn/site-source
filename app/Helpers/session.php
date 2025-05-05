<?php
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function redirectIfLoggedIn(string $redirectTo = '/dashboard'): void {
    if (isLoggedIn()) {
        header("Location: $redirectTo");
        exit;
    }
}

function loginUser(int $userId, string $username): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
}
?>