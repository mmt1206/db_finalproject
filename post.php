<?php
require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

// ✅ 點讚邏輯
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post_id'])) {
    $post_id = (int)$_POST['like_post_id'];
    $like_stmt = $conn->prepare("UPDATE post SET liked_num = liked_num + 1 WHERE post_id = ?");
    $like_stmt->bind_param("i", $post_id);
    $like_stmt->execute();
    header("Location: post.php");
    exit();
}

// ✅ 取得前三篇按讚數最高的貼文
$top_stmt = $conn->prepare("
    SELECT p.post_id, p.content, p.liked_num, p.post_date, u.username
    FROM post p
    JOIN users u ON p.post_person = u.user_id
    ORDER BY p.liked_num DESC, p.post_id DESC
    LIMIT 3
");
$top_stmt->execute();
$top_result = $top_stmt->get_result();
$top_ids = [];
while ($row = $top_result->fetch_assoc()) {
    $top_posts[] = $row;
    $top_ids[] = (int)$row['post_id'];
}

// ✅ 取得其餘貼文（排除前三名）
if (!empty($top_ids)) {
    $placeholders = implode(',', array_fill(0, count($top_ids), '?'));
    $types = str_repeat('i', count($top_ids));
    $sql = "
        SELECT p.post_id, p.content, p.liked_num, p.post_date, u.username
        FROM post p
        JOIN users u ON p.post_person = u.user_id
        WHERE p.post_id NOT IN ($placeholders)
        ORDER BY p.post_id DESC
    ";
    $other_stmt = $conn->prepare($sql);
    $other_stmt->bind_param($types, ...$top_ids);
    $other_stmt->execute();
    $other_result = $other_stmt->get_result();
} else {
    // 若目前沒有貼文，建立空的結果
    $top_posts = [];
    $other_result = null;
}
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
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <h1>貼文牆</h1>

    <a href="create_post.php"><button>建立貼文</button></a>
    <nav><a href="home.php">回首頁</a></nav>

    <?php display_flash_message(); ?>

    <!-- 🔥 熱門貼文區塊 -->
    <h2>🔥 熱門貼文</h2>
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
                        <button type="submit" class="like-btn" title="按讚">👍</button>
                    </form>
                    <?php if (isManager($user)): ?>
                        <form method="POST" action="delete_post.php" onsubmit="return confirm('確定要刪除這篇貼文嗎？');" class="delete-form">
                            <input type="hidden" name="post_id" value="<?= (int)$row['post_id'] ?>">
                            <button type="submit" class="delete-btn">刪除貼文</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- 📝 其他貼文區塊 -->
    <h2>📝 所有貼文</h2>
    <?php if ($other_result === null || $other_result->num_rows === 0): ?>
        <p>沒有其他貼文。</p>
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
                        <button type="submit" class="like-btn" title="按讚">👍</button>
                    </form>
                    <?php if (isManager($user)): ?>
                        <form method="POST" action="delete_post.php" onsubmit="return confirm('確定要刪除這篇貼文嗎？');" class="delete-form">
                            <input type="hidden" name="post_id" value="<?= (int)$row['post_id'] ?>">
                            <button type="submit" class="delete-btn">刪除貼文</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</body>
</html>
