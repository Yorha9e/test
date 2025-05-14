<?php
include('database.php');
session_start();

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $friend_username = $_POST['friend_username'];

    // 使用 PDO 连接查询数据库
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute([':username' => $friend_username]);
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($friend) {
        $user_id = $_SESSION['user_id'];
        $friend_id = $friend['id'];

        // 检查是否已经是好友
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM friends WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)');
        $stmt->execute([':user_id' => $user_id, ':friend_id' => $friend_id]);
        $is_friend = $stmt->fetchColumn();

        if ($is_friend > 0) {
            // 已经是好友
            header("Location: index.php?message=你们已经是好友了！");
            exit();
        } else {
            // 添加好友关系
            $stmt = $pdo->prepare('INSERT INTO friends (user_id, friend_id) VALUES (:user_id, :friend_id), (:friend_id, :user_id)');
            $stmt->execute([':user_id' => $user_id, ':friend_id' => $friend_id]);

            header("Location: index.php?message=好友添加成功！");
            exit();
        }
    } else {
        // 用户不存在
        header("Location: index.php?message=该用户不存在！");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加好友</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007B9E;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #007B9E;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #005f7f;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }

        .message.success {
            color: #28a745;
        }

        .message.error {
            color: #dc3545;
        }

        .message a {
            text-decoration: none;
            color: #007B9E;
        }

        .message a:hover {
            text-decoration: underline;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007B9E;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-button a:hover {
            background-color: #005f7f;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>添加好友</h2>

    <!-- 提示消息 -->
    <?php if (isset($_GET['message'])): ?>
        <div class="message <?php echo (strpos($_GET['message'], '成功') !== false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <br>
            <a href="index.php">返回首页</a>
        </div>
    <?php endif; ?>

    <!-- 添加好友表单 -->
    <form method="POST">
        <div class="form-group">
            <input type="text" name="friend_username" placeholder="输入好友用户名" required>
        </div>
        <div class="form-group">
            <button type="submit">添加好友</button>
        </div>
    </form>

    <!-- 返回首页按钮 -->
    <div class="back-button">
        <a href="index.php">返回首页</a>
    </div>
</div>

</body>
</html>
