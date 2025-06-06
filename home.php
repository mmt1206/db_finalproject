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
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit"style="
                position: absolute; right: 20px; top: 10px;
                font-size: 1.15em; /* Slightly smaller than links but still larger */
                padding: 8px 15px;
                background-color: #4CAF50; /* A green color for the button */
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            ">登出</button>
        </form>
</head>
<body>
    <h1>歡迎，<?php echo htmlspecialchars($user['username']); ?>！</h1>
    <nav>
        <nav style="
        display: flex; 
        justify-content:  space-evenly;; 
        align-items: center; /* Aligns items vertically in the center */
        background-color: #80EF80;
        padding: 15px 10px; /* Added some padding for better spacing */
        border-radius: 8px; /* Added rounded corners */
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Added a subtle shadow */
    ">
        <a href="home.php"style="
            font-size: 1.25em; /* Increased font size */
            margin-right: 15px; /* Added margin between links */
            color: #333; /* Darker text color for better readability on yellow */
            text-decoration: none; /* Remove underline from links */
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        ">歌單首頁</a>
        <a href="settings.php"style="
            font-size: 1.25em;
            margin-right: 15px;
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        ">個人設定</a>
        <a href="create_situation.php"style="
            font-size: 1.25em;
            margin-right: 15px;
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        ">建立推薦歌單</a>
        <a href="show_req_situation.php"style="
            font-size: 1.25em;
            margin-right: 15px;
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        ">顯示情境</a>
        <a href="post.php"style="
            font-size: 1.25em;
            margin-right: 15px;
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        ">貼文牆</a>
        <?php if (isManager($user)): ?>
            <a href="user_list.php"style="
            font-size: 1.25em;
            margin-right: 15px;
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        ">使用者管理</a>
        <?php endif; ?>
    </nav>

    <?php display_flash_message(); ?>

    <h2>你的歌單</h2>
    <ul>
    <?php if ($result->num_rows === 0): ?>
        <li>目前沒有歌單資料</li>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <a href="playlist_view.php?id=<?= (int)$row['playlist_id'] ?>">
                    <?= htmlspecialchars($row['playlist_name']) ?>
                </a>

                <form class="delete-form" method="POST" action="delete_playlist.php" onsubmit="return confirm('確定要刪除歌單「<?= htmlspecialchars($row['playlist_name']) ?>」嗎？');">
                    <input type="hidden" name="playlist_id" value="<?= (int)$row['playlist_id'] ?>">
                    <button type="submit" class="delete-btn">刪除</button>
                </form>
            </li>
        <?php endwhile; ?>
    <?php endif; ?>
    </ul>
</body>
</html>
