<?php
require 'db.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 取得並過濾輸入資料
    $username = trim($_POST['username'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $user_type = $_POST['user_type'] ?? null;

    // 簡單驗證（可以擴充）
    if ($username === '') {
        $msg = "使用者名稱不能為空";
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, birth_date=?, gender=?, user_type=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $username, $birth_date, $gender, $user_type, $user['user_id']);
        if ($stmt->execute()) {
            $msg = "更新成功！";
            // 重新取得更新後資料
            $user = getCurrentUser();
        } else {
            $msg = "更新失敗：" . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>個人設定</title>
    <style>
        body { font-family: "Noto Sans TC", sans-serif; margin: 20px; }
        label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="date"], select { padding: 5px; width: 200px; }
        button { padding: 6px 12px; cursor: pointer; }
        .message { margin-bottom: 15px; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        nav { margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>個人設定 - <?= htmlspecialchars($user['username']) ?></h1>
    <nav>
        <a href="home.php">歌單首頁</a>
    </nav>

    <?php if ($msg !== ''): ?>
        <p class="message <?= strpos($msg, '成功') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($msg) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="settings.php">
        <label>
            使用者名稱：
            <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>">
        </label>

        <label>
            生日：
            <input type="date" name="birth_date" value="<?= htmlspecialchars($user['birth_date']) ?>">
        </label>

        <label>
            性別：
            <select name="gender">
                <option value="male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>男</option>
                <option value="female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>女</option>
                <option value="other" <?= $user['gender'] === 'other' ? 'selected' : '' ?>>其他</option>
            </select>
        </label>

        <label>
            使用者類型：
            <select name="user_type">
                <option value="listener" <?= $user['user_type'] === 'listener' ? 'selected' : '' ?>>聽眾</option>
                <option value="creator" <?= $user['user_type'] === 'creator' ? 'selected' : '' ?>>創作者</option>
                <option value="manager" <?= $user['user_type'] === 'manager' ? 'selected' : '' ?>>管理員</option>
            </select>
        </label>

        <button type="submit">儲存</button>
    </form>
</body>
</html>
