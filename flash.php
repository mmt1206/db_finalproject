<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function set_flash_message(string $type, string $message, string $page_name) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
        'page' => $page_name
    ];
}

function display_flash_message() {
    if (!isset($_SESSION['flash_message'])) {
        return;
    }

    $flash = $_SESSION['flash_message'];
    $current_page = basename($_SERVER['PHP_SELF']);

    if ($flash['page'] === $current_page) {
        $class = $flash['type'] === 'success' ? 'message success' : 'message error';
        echo "<p class='$class'>" . htmlspecialchars($flash['message']) . "</p>";
        unset($_SESSION['flash_message']);
    }
}
