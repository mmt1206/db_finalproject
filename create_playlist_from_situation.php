<?php
session_start();
require 'db.php';
require 'flash.php';  // 確保載入 flash 函式

$owner_id = $_SESSION['user_id'] ?? null;
if (!$owner_id) {
    header("Location: login.php");
    exit;
}

$task = trim($_POST['task'] ?? '');
if ($task === '') {
    set_flash_message('error', "任務名稱不可為空", 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}

$duration = isset($_POST['duration']) && is_numeric($_POST['duration']) ? intval($_POST['duration']) : null;
$explicit = (isset($_POST['explicit']) && $_POST['explicit'] !== '') ? intval($_POST['explicit']) : null;

function get_float_val($key) {
    return (isset($_POST[$key]) && is_numeric($_POST[$key])) ? floatval($_POST[$key]) : null;
}

$danceability_min = get_float_val('danceability_min');
$danceability_max = get_float_val('danceability_max');
$energy_min = get_float_val('energy_min');
$energy_max = get_float_val('energy_max');
$loudness_min = get_float_val('loudness_min');
$loudness_max = get_float_val('loudness_max');
$valence_min = get_float_val('valence_min');
$valence_max = get_float_val('valence_max');
$tempo_min = get_float_val('tempo_min');
$tempo_max = get_float_val('tempo_max');

$where = [];
$params = [];
$types = '';

if ($explicit !== null) {
    $where[] = "explicit = ?";
    $params[] = $explicit;
    $types .= 'i';
}
if ($danceability_min !== null) { $where[] = "danceability >= ?"; $params[] = $danceability_min; $types .= 'd'; }
if ($danceability_max !== null) { $where[] = "danceability <= ?"; $params[] = $danceability_max; $types .= 'd'; }
if ($energy_min !== null) { $where[] = "energy >= ?"; $params[] = $energy_min; $types .= 'd'; }
if ($energy_max !== null) { $where[] = "energy <= ?"; $params[] = $energy_max; $types .= 'd'; }
if ($loudness_min !== null) { $where[] = "loudness >= ?"; $params[] = $loudness_min; $types .= 'd'; }
if ($loudness_max !== null) { $where[] = "loudness <= ?"; $params[] = $loudness_max; $types .= 'd'; }
if ($valence_min !== null) { $where[] = "valence >= ?"; $params[] = $valence_min; $types .= 'd'; }
if ($valence_max !== null) { $where[] = "valence <= ?"; $params[] = $valence_max; $types .= 'd'; }
if ($tempo_min !== null) { $where[] = "tempo >= ?"; $params[] = $tempo_min; $types .= 'd'; }
if ($tempo_max !== null) { $where[] = "tempo <= ?"; $params[] = $tempo_max; $types .= 'd'; }
if ($duration !== null) {
    $where[] = "duration_ms <= ?";
    $params[] = $duration * 1000;
    $types .= 'i';
}

$sql = "SELECT id FROM tracks_features";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " LIMIT 100";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    set_flash_message('error', "SQL 查詢錯誤：" . $conn->error, 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_flash_message('error', "⚠️ 找不到符合條件的歌曲，請調整條件再試一次。", 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}

$track_ids = [];
while ($row = $result->fetch_assoc()) {
    $track_ids[] = $row['id'];
}

$insert_req = $conn->prepare("
    INSERT INTO req_situation (
        owner_id, task, duration, explicit,
        danceability_min, danceability_max,
        energy_min, energy_max,
        loudness_min, loudness_max,
        valence_min, valence_max,
        tempo_min, tempo_max
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$insert_req) {
    set_flash_message('error', "建立 req_situation 失敗：" . $conn->error, 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}
$insert_req->bind_param(
    'isii' . str_repeat('d', 10),
    $owner_id, $task, $duration, $explicit,
    $danceability_min, $danceability_max,
    $energy_min, $energy_max,
    $loudness_min, $loudness_max,
    $valence_min, $valence_max,
    $tempo_min, $tempo_max
);
$insert_req->execute();
$req_id = $conn->insert_id;

$playlist_name = trim($_POST['playlist_name'] ?? '');
if ($playlist_name === '') {
    set_flash_message('error', "歌單名稱不可為空", 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}
$insert_playlist = $conn->prepare("INSERT INTO playlists (owner_id, playlist_name) VALUES (?, ?)");
if (!$insert_playlist) {
    set_flash_message('error', "建立播放清單失敗：" . $conn->error, 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}
$insert_playlist->bind_param('is', $owner_id, $playlist_name);
$insert_playlist->execute();
$playlist_id = $conn->insert_id;

$insert_track_stmt = $conn->prepare("INSERT INTO playlist_tracks (playlist_id, track_id, order_num) VALUES (?, ?, ?)");
if (!$insert_track_stmt) {
    set_flash_message('error', "加入歌曲到播放清單失敗：" . $conn->error, 'create_situation.php');
    header("Location: create_situation.php");
    exit;
}

$order_num = 1;
foreach ($track_ids as $track_id) {
    $insert_track_stmt->bind_param('isi', $playlist_id, $track_id, $order_num);
    $insert_track_stmt->execute();
    $order_num++;
}

set_flash_message('success', "成功建立歌單：$playlist_name，共 " . count($track_ids) . " 首歌曲。", "playlist_view.php?id=$playlist_id");
header("Location: playlist_view.php?id=$playlist_id");
exit;
