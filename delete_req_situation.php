<?php
session_start();
require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // 確認情境是屬於目前使用者的
    $stmt = $conn->prepare("SELECT * FROM req_situation WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $id, $user['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        set_flash_message('error', '找不到該情境或您沒有權限刪除。', 'show_req_situation.php');
        header("Location: show_req_situation.php");
        exit();
    }

    // 刪除資料
    $stmt = $conn->prepare("DELETE FROM req_situation WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        set_flash_message('success', '成功刪除情境。', 'show_req_situation.php');
    } else {
        set_flash_message('error', '刪除失敗，請稍後再試。', 'show_req_situation.php');
    }

    header("Location: show_req_situation.php");
    exit();
} else {
    set_flash_message('error', '請求無效。', 'show_req_situation.php');
    header("Location: show_req_situation.php");
    exit();
}
