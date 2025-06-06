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
            background-color: #f8f9fa;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            padding: 30px 40px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        nav {
            margin-bottom: 20px;
        }

        nav a {
            text-decoration: none;
            color: #6f42c1;
            margin-right: 12px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #28a745;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #218838;
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
    <div class="card">
        <h1>新增使用者帳號</h1>

        <nav>
            <a href="home.php">首頁</a>
            <a href="user_list.php">使用者管理</a>
        </nav>

        <?php display_flash_message(); ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>使用者名稱：</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>生日：</label>
                <input type="date" name="birth_date" required>
            </div>

            <div class="form-group">
                <label>性別：</label>
                <select name="gender" required>
                    <option value="male">男</option>
                    <option value="female">女</option>
                    <option value="other">其他</option>
                </select>
            </div>

            <div class="form-group">
                <label>角色：</label>
                <select name="user_type" required>
                    <option value="listener">聽眾</option>
                    <option value="creator">創作者</option>
                    <option value="manager">管理者</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">新增帳號</button>
        </form>
    </div>
</body>
</html>
