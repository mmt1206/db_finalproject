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
            background-color: #f8f9fa;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(255, 252, 252, 0.08);
            width: 100%;
            max-width: 600px;
            padding: 30px 40px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            height: 200px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
        }

        button, input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1em;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #218838;
        }

        .secondary-button {
            background-color: #6c757d;
            margin-top: 10px;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #28a745;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color:white;
            background-color: #6c757d;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: none;
        }

        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>建立貼文</h1>

        <?php display_flash_message(); ?>

        <form method="POST">
            <div class="form-group">
                <label for="content">貼文內容：</label>
                <textarea name="content" id="content" required></textarea>
            </div>
            <button type="submit" class="btn-submit">發佈</button>
        </form>
                <form action="post.php">
            <button href="post.php" class="back-link">🔙 返回貼文牆</button>
        </form>
    </div>
</body>
</html>
