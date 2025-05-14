<?php
session_start();
session_destroy();  // 销毁会话

// 重定向到登录页面
header("Location: login.php");  
exit();
