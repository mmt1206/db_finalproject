<?php
require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);

    if (empty($content)) {
        set_flash_message('error', '貼文內容不可為空', 'create_post.php');
    } else {
        $stmt = $conn->prepare("
            INSERT INTO post (content, liked_num, post_date, post_person)
            VALUES (?, 0, CURDATE(), ?)
        ");
        $stmt->bind_param("si", $content, $user['user_id']);
        if ($stmt->execute()) {
            set_flash_message('success', '貼文成功建立', 'post.php');
            header("Location: post.php");
            exit();
        } else {
            set_flash_message('error', '建立貼文失敗，請稍後再試', 'create_post.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>建立貼文</title>
    <style>
        body {
            font-family: "Noto Sans TC", sans-serif;
            margin: 20px;
        }
        textarea {
            width: 100%;
            height: 100px;
            font-size: 1em;
        }
        button {
            margin-top: 10px;
            padding: 8px 16px;
        }
    </style>
</head>
<body>
    <h1>建立貼文</h1>
    <nav>
        <a href="post.php">← 回貼文牆</a>
    </nav>

    <?php display_flash_message(); ?>

    <form method="POST">
        <label for="content">貼文內容：</label><br>
        <textarea name="content" id="content" required></textarea><br>
        <button type="submit">發佈</button>
    </form>
</body>
</html>
