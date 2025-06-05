<?php
session_start();  // 很重要：要能操作 session，就一定要先啟用它
session_unset();  // 清除所有 session 變數
session_destroy();  // 銷毀 session
header("Location: login.php");  // 導回登入頁
exit();