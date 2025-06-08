<?php
require 'db.php';
require 'flash.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['user_id'];
        header('Location: index.php');
        exit();
    } else {
        $error = "找不到使用者";
    }
}
?>

<form method="POST" action="login.php">
    <style>
        body {
            background-image: url('material/background.jpg');
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 20%;
        }
        form {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        input[type="text"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            width: 200px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
    <label>使用者名稱: <input type="text" name="username"></label>
    <button type="submit">登入</button>
</form>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>