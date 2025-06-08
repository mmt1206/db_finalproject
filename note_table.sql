CREATE TABLE users (
    user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    user_type ENUM('listener', 'creator', 'manager')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE post (
    post_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    liked_num INT(11) DEFAULT 0,
    post_date DATE,
    content TEXT,

    post_person INT(11),
    FOREIGN KEY (post_person) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE playlists (
    playlist_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    playlist_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE playlist_tracks (
    playlist_id INT NOT NULL,
    track_id VARCHAR(22) NOT NULL,
    order_num INT NOT NULL,
    PRIMARY KEY (playlist_id, order_num),
    FOREIGN KEY (playlist_id) REFERENCES playlists(playlist_id) ON DELETE CASCADE,
    FOREIGN KEY (track_id) REFERENCES tracks_features(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE req_situation (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    owner_id INT(11) NOT NULL,                         -- 使用者ID
    task VARCHAR(255) NOT NULL,                        -- 任務名稱
    duration INT(11) DEFAULT NULL,                     -- 時長 (秒)
    explicit TINYINT(1) DEFAULT NULL,                  -- 是否 explicit (1/0)

    danceability_min DOUBLE DEFAULT NULL,
    danceability_max DOUBLE DEFAULT NULL,

    energy_min DOUBLE DEFAULT NULL,
    energy_max DOUBLE DEFAULT NULL,

    loudness_min DOUBLE DEFAULT NULL,
    loudness_max DOUBLE DEFAULT NULL,

    valence_min DOUBLE DEFAULT NULL,
    valence_max DOUBLE DEFAULT NULL,

    tempo_min DOUBLE DEFAULT NULL,
    tempo_max DOUBLE DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (owner_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;