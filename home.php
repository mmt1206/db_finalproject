<?php
require 'db.php';
require 'flash.php';  // 建議加入 flash 訊息功能檔

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

// 取得此使用者的歌單，這裡多抓 playlist_id
$stmt = $conn->prepare("SELECT playlist_id, playlist_name FROM playlists WHERE owner_id = ? ORDER BY playlist_name");
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>歌單首頁</title>
    <style>
        body {
            font-family: "Noto Sans TC", sans-serif;
            margin: 20px;
        }
        form.delete-form {
            display: inline;
        }
        button.delete-btn {
            color: white;
            background-color: #d9534f;
            border: none;
            padding: 3px 8px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 0.9em;
        }
        button.delete-btn:hover {
            background-color: #c9302c;
        }
        nav a {
            margin-right: 10px;
            text-decoration: none;
            color: #337ab7;
        }
        nav a:hover {
            text-decoration: underline;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 8px;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>歡迎，<?php echo htmlspecialchars($user['username']); ?>！</h1>
    <nav>
        <a href="home.php">歌單首頁</a>
        <a href="settings.php">個人設定</a>
        <a href="create_situation.php">建立推薦歌單</a>
        <a href="show_req_situation.php">顯示情境</a>
    </nav>

    <?php display_flash_message(); ?>

    <h2>你的歌單</h2>
    <ul>
    <?php if ($result->num_rows === 0): ?>
        <li>目前沒有歌單資料</li>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($row['playlist_name']); ?>

                <!-- 刪除按鈕，改用 playlist_id -->
                <form class="delete-form" method="POST" action="delete_playlist.php" onsubmit="return confirm('確定要刪除歌單「<?php echo htmlspecialchars($row['playlist_name']); ?>」嗎？');">
                    <input type="hidden" name="playlist_id" value="<?php echo (int)$row['playlist_id']; ?>">
                    <button type="submit" class="delete-btn">刪除</button>
                </form>
            </li>
        <?php endwhile; ?>
    <?php endif; ?>
    </ul>
</body>
</html>
