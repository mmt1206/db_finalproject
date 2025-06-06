<?php
require 'flash.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'test';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 取得目前使用者資料函式
function getCurrentUser() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function isManager($user) {
    return $user['user_type'] === 'manager';
}

function isCreator($user) {
    return $user['user_type'] === 'creator';
}

function isListener($user) {
    return $user['user_type'] === 'listener';
}
?>

