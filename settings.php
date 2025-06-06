<?php
require 'db.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $gender = $_POST['gender'] ?? null;

    if ($user['user_type'] === 'manager') {
        $user_type = $_POST['user_type'] ?? null;
    } else {
        $user_type = $user['user_type'];
    }

    if ($username === '') {
        $msg = "使用者名稱不能為空";
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, birth_date=?, gender=?, user_type=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $username, $birth_date, $gender, $user_type, $user['user_id']);
        if ($stmt->execute()) {
            $msg = "更新成功！";
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
        body {
            font-family: "Noto Sans TC", sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 50px;
        }
        .card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        nav {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 15px;
            font-weight: 500;
        }
        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }
        button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success { color: green; }
        .error { color: red; }
        .home-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>👤 個人設定 - <?= htmlspecialchars($user['username']) ?></h1>
        <nav>
            <a href="home.php" class="home-link">🏠 返回首頁</a>
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
                <?php ?>
                    <input type="text" value="<?= htmlspecialchars($user['user_type']) ?>" disabled>
                    <input type="hidden" name="user_type" value="<?= htmlspecialchars($user['user_type']) ?>">
                <?php ?>
            </label>

            <button type="submit">💾 儲存</button>
        </form>
    </div>
</body>
</html>
