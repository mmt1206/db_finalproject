<?php
require 'db.php';
require 'flash.php';

$user = getCurrentUser();
if (!$user || !isManager($user)) {
    header('Location: login.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $user_type = $_POST['user_type'];

    if (empty($username) || empty($birth_date) || empty($gender) || empty($user_type)) {
        $errors[] = "請填寫所有欄位";
    }

    if (!in_array($gender, ['male', 'female', 'other'])) {
        $errors[] = "性別錯誤";
    }

    if (!in_array($user_type, ['listener', 'creator', 'manager'])) {
        $errors[] = "角色錯誤";
    }

    // 檢查使用者名稱是否已存在
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "使用者名稱已存在";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (username, birth_date, gender, user_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $birth_date, $gender, $user_type);
        $stmt->execute();

        set_flash_message('success', '成功新增帳號！', 'user_list.php');
        header('Location: user_list.php');
        exit();
    } else {
        // 若有錯誤，也用 flash 顯示並停留本頁
        $error_msg = implode('；', $errors);
        set_flash_message('error', $error_msg, 'register.php');
        header('Location: register.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>新增帳號</title>
    <style>
        body {
            font-family: "Noto Sans TC", sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 5px;
        }
        .message.success {
            color: green;
        }
        .message.error {
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
    <h1>新增使用者帳號</h1>
    <nav>
        <a href="home.php">首頁</a>
        <a href="user_list.php">使用者管理</a>
    </nav>

    <?php display_flash_message(); ?>

    <form method="POST" action="register.php">
        <label>使用者名稱：
            <input type="text" name="username" required>
        </label>

        <label>生日：
            <input type="date" name="birth_date" required>
        </label>

        <label>性別：
            <select name="gender" required>
                <option value="male">男</option>
                <option value="female">女</option>
                <option value="other">其他</option>
            </select>
        </label>

        <label>角色：
            <select name="user_type" required>
                <option value="listener">聽眾</option>
                <option value="creator">創作者</option>
                <option value="manager">管理者</option>
            </select>
        </label>

        <button type="submit">新增帳號</button>
    </form>
</body>
</html>
