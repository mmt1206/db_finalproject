<?php
require 'db.php';
session_start();  // ⬅️ 一定要加這行

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['user_id'];
        header('Location: home.php');
        exit();
    } else {
        $error = "找不到使用者";
    }
}
?>

<form method="POST" action="login.php">
    <label>使用者名稱: <input type="text" name="username"></label>
    <button type="submit">登入</button>
</form>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>