<?php
// 開啟錯誤顯示，方便除錯
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user || !isManager($user)) {
    header('Location: login.php');
    exit();
}

// 處理權限修改
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $target_id = (int)$_POST['user_id'];
    $new_role = $_POST['user_type'];
    if (in_array($new_role, ['manager', 'creator', 'listener'])) {
        $stmt = $conn->prepare("UPDATE users SET user_type = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_role, $target_id);
        $stmt->execute();
        set_flash_message("success", "成功修改使用者權限", "user_list.php");
        header('Location: user_list.php');
        exit();
    } else {
        set_flash_message("error", "無效的角色類型", "user_list.php");
        header('Location: user_list.php');
        exit();
    }
}

// 處理刪除帳號
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $target_id = (int)$_POST['user_id'];
    if ($target_id !== (int)$user['user_id']) {  // 防止刪除自己
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $target_id);
        if ($stmt->execute()) {
            set_flash_message("success", "帳號已刪除", "user_list.php");
            header('Location: user_list.php');
            exit();
        } else {
            // 刪除失敗顯示錯誤訊息並停止
            echo "刪除失敗：" . htmlspecialchars($stmt->error);
            exit();
        }
    } else {
        set_flash_message("error", "無法刪除自己帳號", "user_list.php");
        header('Location: user_list.php');
        exit();
    }
}

// 抓取所有帳號
$result = $conn->query("SELECT user_id, username, birth_date, gender, user_type FROM users ORDER BY user_id");
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>使用者管理</title>
    <style>
        body {
            font-family: "Noto Sans TC", sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #aaa;
        }
        th {
            background-color: #f5f5f5;
        }
        .actions form {
            display: inline;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        nav a {
            margin-right: 10px;
            text-decoration: none;
            color: #337ab7;
        }
    </style>
</head>
<body>
    <h1>使用者管理</h1>
    <nav>
        <a href="home.php">返回首頁</a>
        <a href="register.php">新增帳號</a>
    </nav>

    <?php display_flash_message(); ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>使用者名稱</th>
                <th>生日</th>
                <th>性別</th>
                <th>角色</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['birth_date']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                        <select name="user_type" onchange="this.form.submit()" <?= $row['user_id'] == $user['user_id'] ? 'disabled' : '' ?>>
                            <option value="manager" <?= $row['user_type'] === 'manager' ? 'selected' : '' ?>>管理者</option>
                            <option value="creator" <?= $row['user_type'] === 'creator' ? 'selected' : '' ?>>創作者</option>
                            <option value="listener" <?= $row['user_type'] === 'listener' ? 'selected' : '' ?>>聽眾</option>
                        </select>
                    </form>
                </td>
                <td class="actions">
                    <?php if ((int)$row['user_id'] !== (int)$user['user_id']): ?>
                        <form method="POST" onsubmit="return confirm('確定要刪除帳號「<?= htmlspecialchars($row['username']) ?>」嗎？');">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                            <button type="submit">刪除</button>
                        </form>
                    <?php else: ?>
                        <em>（自己）</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
