<?php
require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

// ✅ 點讚邏輯（保留 tab）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post_id'])) {
    $post_id = (int)$_POST['like_post_id'];
    $like_stmt = $conn->prepare("UPDATE post SET liked_num = liked_num + 1 WHERE post_id = ?");
    $like_stmt->bind_param("i", $post_id);
    $like_stmt->execute();

    $tab = $_POST['current_tab'] ?? 'hot';
    header("Location: post.php?tab=" . urlencode($tab));
    exit();
}

$active_tab = $_GET['tab'] ?? 'hot';

// ✅ 取得熱門貼文
$top_stmt = $conn->prepare("
    SELECT p.post_id, p.content, p.liked_num, p.post_date, u.username
    FROM post p
    JOIN users u ON p.post_person = u.user_id
    ORDER BY p.liked_num DESC, p.post_id DESC
    LIMIT 3
");
$top_stmt->execute();
$top_result = $top_stmt->get_result();
$top_posts = [];
while ($row = $top_result->fetch_assoc()) {
    $top_posts[] = $row;
}

// ✅ 取得所有貼文
$all_stmt = $conn->prepare("
    SELECT p.post_id, p.content, p.liked_num, p.post_date, u.username
    FROM post p
    JOIN users u ON p.post_person = u.user_id
    ORDER BY p.post_id DESC
");
$all_stmt->execute();
$other_result = $all_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>貼文牆</title>
    <style>
        body {
            font-family: "Noto Sans TC", sans-serif;
            margin: 20px;
        }
        .tab-bar button {
            padding: 10px 20px;
            font-size: 1.2em;
            border: none;
            background-color: #eee;
            cursor: pointer;
            margin-right: 5px;
        }
        .tab-bar .active {
            background-color:  #28a745;
            color: black;
        }
        .post {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .post-header {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .post-date {
            color: #777;
            font-size: 0.9em;
        }
        form.like-form, form.delete-form {
            display: inline;
        }
        button.like-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1em;
            color: #007bff;
        }
        button.like-btn:hover {
            color: #0056b3;
        }
        button.delete-btn {
            color: white;
            background-color: #d9534f;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        button.delete-btn:hover {
            background-color: #c9302c;
        }
        h2 {
            margin-top: 30px;
        }
    </style>

        <form action="home.php">
        <button type="submit"style="
                position: absolute; right: 20px; top: 10px;
                font-size: 1.15em; /* Slightly smaller than links but still larger */
                padding: 8px 15px;
                background-color:rgb(178, 183, 178); /* A green color for the button */
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;"
                >返回主頁</button>
    </form>
</head>
<body>
    <h1>貼文牆</h1>

    <a href="create_post.php"><button>建立貼文</button></a>

    <?php display_flash_message(); ?>

    <!-- 🔁 分頁切換按鈕 -->
    <div class="tab-bar">
        <button onclick="switchTab('hot')" class="<?= $active_tab === 'hot' ? 'active' : '' ?>">🔥 熱門貼文</button>
        <button onclick="switchTab('all')" class="<?= $active_tab === 'all' ? 'active' : '' ?>">📝 所有貼文</button>
    </div>

    <!-- 🔥 熱門貼文區塊 -->
    <div id="hot-posts" style="<?= $active_tab === 'hot' ? '' : 'display:none;' ?>">
        <?php if (empty($top_posts)): ?>
            <p>目前沒有熱門貼文。</p>
        <?php else: ?>
            <?php foreach ($top_posts as $row): ?>
                <div class="post">
                    <div class="post-header">
                        <?= htmlspecialchars($row['username']) ?>
                        <span class="post-date">(<?= htmlspecialchars($row['post_date']) ?>)</span>
                    </div>
                    <div class="post-content">
                        <?= nl2br(htmlspecialchars($row['content'])) ?>
                    </div>
                    <div class="post-likes">
                        按讚數: <?= (int)$row['liked_num'] ?>
                        <form method="POST" class="like-form">
                            <input type="hidden" name="like_post_id" value="<?= (int)$row['post_id'] ?>">
                            <input type="hidden" name="current_tab" value="hot">
                            <button type="submit" class="like-btn" title="按讚">👍</button>
                        </form>
                        <?php if (isManager($user)): ?>
                            <form method="POST" action="delete_post.php" onsubmit="return confirm('確定要刪除這篇貼文嗎？');" class="delete-form">
                                <input type="hidden" name="post_id" value="<?= (int)$row['post_id'] ?>">
                                <input type="hidden" name="current_tab" value="hot">
                                <button type="submit" class="delete-btn">刪除貼文</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- 📝 所有貼文區塊 -->
    <div id="all-posts" style="<?= $active_tab === 'all' ? '' : 'display:none;' ?>">
        <?php if ($other_result === null || $other_result->num_rows === 0): ?>
            <p>沒有貼文。</p>
        <?php else: ?>
            <?php while ($row = $other_result->fetch_assoc()): ?>
                <div class="post">
                    <div class="post-header">
                        <?= htmlspecialchars($row['username']) ?>
                        <span class="post-date">(<?= htmlspecialchars($row['post_date']) ?>)</span>
                    </div>
                    <div class="post-content">
                        <?= nl2br(htmlspecialchars($row['content'])) ?>
                    </div>
                    <div class="post-likes">
                        按讚數: <?= (int)$row['liked_num'] ?>
                        <form method="POST" class="like-form">
                            <input type="hidden" name="like_post_id" value="<?= (int)$row['post_id'] ?>">
                            <input type="hidden" name="current_tab" value="all">
                            <button type="submit" class="like-btn" title="按讚">👍</button>
                        </form>
                        <?php if (isManager($user)): ?>
                            <form method="POST" action="delete_post.php" onsubmit="return confirm('確定要刪除這篇貼文嗎？');" class="delete-form">
                                <input type="hidden" name="post_id" value="<?= (int)$row['post_id'] ?>">
                                <input type="hidden" name="current_tab" value="all">
                                <button type="submit" class="delete-btn">刪除貼文</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <script>
    function switchTab(tab) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.location.href = url.toString();
    }
    </script>
</body>
</html>
