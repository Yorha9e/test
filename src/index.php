<?php
session_start();
require_once 'database.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取用户信息
$user = get_user_info($_SESSION['user_id']);

// 获取好友列表
$stmt = $pdo->prepare('SELECT u.id, u.username, u.avatar FROM users u JOIN friends f ON u.id = f.friend_id WHERE f.user_id = :user_id');
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首页 - 社交网络</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 页面整体样式 */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* 头部样式 */
        header {
            background: #007B9E;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 22px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* 导航栏样式 */
        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
            margin: 10px 0;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            background-color: #005F7A;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }

        nav ul li a:hover {
            background-color: #004F69;
        }

        /* 容器样式 */
        .container {
            width: 80%;
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* 用户信息卡片 */
        .user-info {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .user-info img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }

        .user-info h2 {
            margin: 0;
        }

        /* 好友列表 */
        .friend-list {
            margin-top: 20px;
        }

        .friend-item {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .friend-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }

        .friend-item a {
            margin-left: auto;
            background-color: #28A745;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .friend-item a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<header>
    <h1>欢迎来到社交网络</h1>
    <nav>
        <ul>
            <li><a href="index.php">🏠 首页</a></li>
            <li><a href="profile.php">👤 个人资料</a></li>
            <li><a href="message.php">📝 图文留言</a></li>
            <li><a href="add_friend.php">➕ 添加好友</a></li>
            <li><a href="logout.php">🚪 退出登录</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <div class="user-info">
        <?php
        $avatar = isset($user['avatar']) && !empty($user['avatar']) ? 'uploads/' . $user['avatar'] : 'uploads/default.png';
        ?>
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="用户头像">
        <h2>你好，<?php echo htmlspecialchars($user['username']); ?>！</h2>
    </div>

    <div class="main-content">
        <h2>好友列表</h2>
        <div class="friend-list">
            <?php if (!empty($friends)): ?>
                <?php foreach ($friends as $friend): ?>
                    <div class="friend-item">
                        <img src="uploads/<?php echo htmlspecialchars($friend['avatar']); ?>" alt="<?php echo htmlspecialchars($friend['username']); ?> 的头像">
                        <strong><?php echo htmlspecialchars($friend['username']); ?></strong>
                        <a href="chat.php?friend_id=<?php echo $friend['id']; ?>">💬 聊天</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>你还没有好友，快去添加吧！</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
