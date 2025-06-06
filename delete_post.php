<?php
require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user || !isManager($user)) {
    set_flash_message('error', '只有管理員可以刪除貼文', 'post.php');
    header('Location: post.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $current_tab = $_POST['current_tab'] ?? 'hot';  // 取得目前 tab

    $stmt = $conn->prepare("DELETE FROM post WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        set_flash_message('success', '貼文已刪除', 'post.php');
    } else {
        set_flash_message('error', '刪除失敗，請稍後再試', 'post.php');
    }

    // ✅ 根據原 tab 導回對應頁籤
    header("Location: post.php?tab=" . urlencode($current_tab));
    exit();
}
?>
