<?php
session_start();
require 'flash.php';
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>建立播放清單</title>
    <style>
        body {
            font-family: "Noto Sans TC", sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            padding: 50px 20px;
        }
        .card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            font-size: 1.5em;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 15px;
        }
        .label-title {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }
        .range-group {
            display: flex;
            gap: 10px;
            margin-bottom: 5px;
        }
        .range-group input {
            width: 100%;
        }
        .note {
            font-size: 0.85em;
            color: #555;
        }
        fieldset {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
        }
        legend {
            padding: 0 10px;
            font-weight: bold;
        }
        button, input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1em;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
        }
        button:hover, input[type="submit"]:hover {
            background-color: #218838;
        }
        .secondary-button {
            background-color: #6c757d;
            margin-top: 10px;
        }
        .secondary-button:hover {
            background-color: #5a6268;
        }
        .flash {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🎵 建立新的播放清單</h1>

        <?php display_flash_message(); ?>

        <form action="create_playlist_from_situation.php" method="POST">
            <label>
                <span class="label-title">任務名稱（task）：</span>
                <input type="text" name="task" required>
            </label>

            <label>
                <span class="label-title">歌單名稱（playlist name）：</span>
                <input type="text" name="playlist_name" required>
            </label>

            <label>
                <span class="label-title">最大播放時間（秒）：</span>
                <input type="number" name="duration" min="17" max="2458">
                <span class="note">（範圍：約 17 ~ 2458 秒）</span>
            </label>

            <label>
                <span class="label-title">是否為 Explicit 歌曲：</span>
                <select name="explicit">
                    <option value="">不限制</option>
                    <option value="1">是</option>
                    <option value="0">否</option>
                </select>
                <span class="note">（0 = 否，1 = 是）</span>
            </label>

            <fieldset>
                <legend>音樂特性篩選（可選）</legend>

                <label>
                    <span class="label-title">Danceability：</span>
                    <div class="range-group">
                        <input type="number" step="0.01" name="danceability_min" min="0.066" max="0.909" placeholder="Min">
                        <input type="number" step="0.01" name="danceability_max" min="0.066" max="0.909" placeholder="Max">
                    </div>
                    <span class="note">範圍：0.066 ~ 0.909</span>
                </label>

                <label>
                    <span class="label-title">Energy：</span>
                    <div class="range-group">
                        <input type="number" step="0.01" name="energy_min" min="0.007" max="0.998" placeholder="Min">
                        <input type="number" step="0.01" name="energy_max" min="0.007" max="0.998" placeholder="Max">
                    </div>
                    <span class="note">範圍：0.007 ~ 0.998</span>
                </label>

                <label>
                    <span class="label-title">Loudness：</span>
                    <div class="range-group">
                        <input type="number" step="0.1" name="loudness_min" min="-35.316" max="-2.584" placeholder="Min">
                        <input type="number" step="0.1" name="loudness_max" min="-35.316" max="-2.584" placeholder="Max">
                    </div>
                    <span class="note">範圍：-35.316 ~ -2.584</span>
                </label>

                <label>
                    <span class="label-title">Valence（快樂感）：</span>
                    <div class="range-group">
                        <input type="number" step="0.01" name="valence_min" min="0.026" max="0.974" placeholder="Min">
                        <input type="number" step="0.01" name="valence_max" min="0.026" max="0.974" placeholder="Max">
                    </div>
                    <span class="note">範圍：0.026 ~ 0.974</span>
                </label>

                <label>
                    <span class="label-title">Tempo（節奏）：</span>
                    <div class="range-group">
                        <input type="number" step="0.1" name="tempo_min" min="49.179" max="207.329" placeholder="Min">
                        <input type="number" step="0.1" name="tempo_max" min="49.179" max="207.329" placeholder="Max">
                    </div>
                    <span class="note">範圍：49.179 ~ 207.329 BPM</span>
                </label>
            </fieldset>

            <input type="submit" value="✅ 建立播放清單">
        </form>

        <form action="home.php">
            <button type="submit" class="secondary-button">🔙 返回主頁</button>
        </form>
    </div>
</body>
</html>
