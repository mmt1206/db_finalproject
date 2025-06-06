<?php
require 'flash.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>建立播放清單</title>
    <style>
        body { font-family: "Noto Sans TC", sans-serif; margin: 20px; }
        label { display: block; margin-bottom: 8px; }
        .range-note { font-size: 0.85em; color: #666; margin-left: 8px; }
    </style>
</head>
<body>
    <h1>建立新的播放清單</h1>

    <?php display_flash_message(); ?>

    <form action="create_playlist_from_situation.php" method="POST">
        <label for="task">任務名稱（task）：</label>
        <input id="task" type="text" name="task" required><br><br>

        <label for="playlist_name">歌單名稱（playlist name）：</label>
        <input id="playlist_name" type="text" name="playlist_name" required><br><br>

        <label for="duration">最大播放時間（秒）：</label>
        <input id="duration" type="number" name="duration" min="17" max="2458">
        <span class="range-note">(範圍：約 17 ~ 2458 秒)</span><br><br>

        <label for="explicit">是否為 Explicit 歌曲：</label>
        <select id="explicit" name="explicit">
            <option value="">不限制</option>
            <option value="1">是</option>
            <option value="0">否</option>
        </select>
        <span class="range-note">(0 = 否，1 = 是)</span><br><br>

        <fieldset>
            <legend>音樂特性篩選（可選）</legend>

            <label>Danceability：
                Min <input type="number" step="0.01" name="danceability_min" min="0.066" max="0.909">
                Max <input type="number" step="0.01" name="danceability_max" min="0.066" max="0.909">
                <span class="range-note">(範圍：0.066 ~ 0.909)</span>
            </label><br>

            <label>Energy：
                Min <input type="number" step="0.01" name="energy_min" min="0.007" max="0.998">
                Max <input type="number" step="0.01" name="energy_max" min="0.007" max="0.998">
                <span class="range-note">(範圍：0.007 ~ 0.998)</span>
            </label><br>

            <label>Loudness：
                Min <input type="number" step="0.1" name="loudness_min" min="-35.316" max="-2.584">
                Max <input type="number" step="0.1" name="loudness_max" min="-35.316" max="-2.584">
                <span class="range-note">(範圍：-35.316 ~ -2.584)</span>
            </label><br>

            <label>Valence（快樂感）：
                Min <input type="number" step="0.01" name="valence_min" min="0.026" max="0.974">
                Max <input type="number" step="0.01" name="valence_max" min="0.026" max="0.974">
                <span class="range-note">(範圍：0.026 ~ 0.974)</span>
            </label><br>

            <label>Tempo（節奏）：
                Min <input type="number" step="0.1" name="tempo_min" min="49.179" max="207.329">
                Max <input type="number" step="0.1" name="tempo_max" min="49.179" max="207.329">
                <span class="range-note">(範圍：49.179 ~ 207.329 BPM)</span>
            </label>
        </fieldset>

        <input type="submit" value="建立播放清單">
    </form>
    <form action="home.php">
        <button type="submit">返回主頁</button>
    </form>
</body>
</html>
