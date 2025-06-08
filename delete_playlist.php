<?php
session_start();
require 'db.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['playlist_id'])) {
        $_SESSION['error'] = '未指定要刪除的歌單';
        header('Location: index.php');
        exit();
    }

    $playlist_id = (int)$_POST['playlist_id'];

    // 刪除該使用者擁有且ID符合的歌單
    $stmt = $conn->prepare("DELETE FROM playlists WHERE owner_id = ? AND playlist_id = ?");
    $stmt->bind_param("ii", $user['user_id'], $playlist_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = '歌單刪除成功';
    } else {
        $_SESSION['error'] = '找不到該歌單或無法刪除';
    }

    $stmt->close();

    header('Location: index.php');
    exit();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "不支援的請求方式";
    exit();
}
