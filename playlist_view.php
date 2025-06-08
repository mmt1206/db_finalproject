<?php
session_start();
require 'db.php';
require 'flash.php';

$playlist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($playlist_id <= 0) {
    die("無效的歌單ID");
}

// 取歌單資訊
$sql_playlist = "
    SELECT p.playlist_name, u.username AS owner_name
    FROM playlists p
    LEFT JOIN users u ON p.owner_id = u.user_id
    WHERE p.playlist_id = ?
";
$stmt = $conn->prepare($sql_playlist);
if (!$stmt) {
    die("SQL 錯誤: " . $conn->error);
}
$stmt->bind_param('i', $playlist_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("找不到此歌單");
}
$playlist = $result->fetch_assoc();

// 取歌單裡的歌曲
$sql_tracks = "
    SELECT pt.order_num, tf.id AS track_id, tf.name AS track_name, tf.artists
    FROM playlist_tracks pt
    INNER JOIN tracks_features tf ON pt.track_id = tf.id
    WHERE pt.playlist_id = ?
    ORDER BY pt.order_num ASC
";
$stmt_tracks = $conn->prepare($sql_tracks);
if (!$stmt_tracks) {
    die("SQL 錯誤: " . $conn->error);
}
$stmt_tracks->bind_param('i', $playlist_id);
$stmt_tracks->execute();
$result_tracks = $stmt_tracks->get_result();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>歌單 - <?= htmlspecialchars($playlist['playlist_name']) ?></title>
    <style>
        body { font-family: "Noto Sans TC", Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        tr:nth-child(even) { background: #fafafa; }
        .message.success { color: green; }
        .message.error { color: red; }
    </style>
</head>
<body>

<?php display_flash_message(); ?>

<h1>歌單：<?= htmlspecialchars($playlist['playlist_name']) ?></h1>
<nav>
    <a href="home.php"style="
            position: absolute; right: 20px; top: 23px;
            font-size: 1.15em; /* Slightly smaller than links but still larger */
            padding: 8px 15px;
            background-color:rgb(178, 183, 178); /* A green color for the button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;">
            返回首頁</a>
    <a href="create_situation.php"style="
            position: absolute; right: 128px; top: 23px;
            font-size: 1.15em; /* Slightly smaller than links but still larger */
            padding: 8px 15px;
            background-color:rgb(178, 183, 178); /* A green color for the button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;">回到建立推薦歌單頁面</a>
    </nav>

<p>擁有者：<?= htmlspecialchars($playlist['owner_name']) ?></p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>歌曲名稱</th>
            <th>歌手</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_tracks->num_rows === 0): ?>
            <tr><td colspan="3">此歌單沒有歌曲。</td></tr>
        <?php else: ?>
            <?php while ($row = $result_tracks->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['order_num'] ?></td>
                    <td><?= htmlspecialchars($row['track_name']) ?></td>
                    <td><?= htmlspecialchars($row['artists']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>



</body>
</html>
