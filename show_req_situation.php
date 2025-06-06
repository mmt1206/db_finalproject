<?php
require 'db.php';
require 'flash.php'; // 加入 flash 功能

// 取得當前使用者
$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

// 取得該使用者所有情境，依建立時間倒序
$stmt = $conn->prepare("SELECT * FROM req_situation WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>我的情境列表</title>
    <style>
        body { font-family: "Noto Sans TC", sans-serif; margin: 20px; }
        ul { list-style-type: none; padding-left: 0; }
        li { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        form.delete-form { display: inline; }
        button.delete-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9em;
        }
        button.delete-btn:hover { background-color: #c9302c; }
        .message { margin: 15px 0; font-weight: bold; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>情境列表 - <?= htmlspecialchars($user['username']) ?></h1>

    <?php display_flash_message(); ?>

    <?php if ($result->num_rows === 0): ?>
        <p>目前沒有情境資料。</p>
    <?php else: ?>
        <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <strong>任務名稱：</strong> <?= htmlspecialchars($row['task']) ?><br>
                <strong>時長：</strong> <?= is_null($row['duration']) ? '不限' : htmlspecialchars($row['duration']) . ' 秒' ?><br>
                <strong>Explicit：</strong> 
                <?= is_null($row['explicit']) ? '不限' : ($row['explicit'] == 1 ? '是' : '否') ?><br>

                <strong>Danceability：</strong>
                <?= is_null($row['danceability_min']) ? '-' : htmlspecialchars($row['danceability_min']) ?>
                ~
                <?= is_null($row['danceability_max']) ? '-' : htmlspecialchars($row['danceability_max']) ?><br>

                <strong>Energy：</strong>
                <?= is_null($row['energy_min']) ? '-' : htmlspecialchars($row['energy_min']) ?>
                ~
                <?= is_null($row['energy_max']) ? '-' : htmlspecialchars($row['energy_max']) ?><br>

                <strong>Loudness：</strong>
                <?= is_null($row['loudness_min']) ? '-' : htmlspecialchars($row['loudness_min']) ?>
                ~
                <?= is_null($row['loudness_max']) ? '-' : htmlspecialchars($row['loudness_max']) ?><br>

                <strong>Valence：</strong>
                <?= is_null($row['valence_min']) ? '-' : htmlspecialchars($row['valence_min']) ?>
                ~
                <?= is_null($row['valence_max']) ? '-' : htmlspecialchars($row['valence_max']) ?><br>

                <strong>Tempo：</strong>
                <?= is_null($row['tempo_min']) ? '-' : htmlspecialchars($row['tempo_min']) ?>
                ~
                <?= is_null($row['tempo_max']) ? '-' : htmlspecialchars($row['tempo_max']) ?><br>

                <form method="POST" action="delete_req_situation.php" class="delete-form" onsubmit="return confirm('確定要刪除這個情境嗎？');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" class="delete-btn">刪除</button>
                </form>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <form action="home.php">
        <button type="submit">返回主頁</button>
    </form>
</body>
</html>
