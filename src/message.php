<?php
session_start();
require_once 'database.php';

// 获取当前用户信息
$user = get_user_info($_SESSION['user_id']);
$friend_id = isset($_GET['friend_id']) ? $_GET['friend_id'] : null;

// 获取当前用户的好友列表
function get_user_friends($user_id) {
    global $pdo;
    // 获取用户的所有好友
    $stmt = $pdo->prepare('
        SELECT u.id, u.username
        FROM users u
        JOIN friends f ON (f.user_id = u.id OR f.friend_id = u.id)
        WHERE (f.user_id = :user_id OR f.friend_id = :user_id)
        AND u.id != :user_id
        GROUP BY u.id
    ');
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$friends = get_user_friends($_SESSION['user_id']);  // 获取用户的所有好友

// 获取好友的用户名（如果指定了好友ID）
$friend_name = '';
if ($friend_id) {
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id = :friend_id');
    $stmt->execute([':friend_id' => $friend_id]);
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);
    $friend_name = $friend ? $friend['username'] : '未知用户';
}

// 处理留言的提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $friend_id = $_POST['friend_id'];  // 留言对象
    $image_path = null;

    // 处理图片上传
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/messages/";

        // 检查目标目录是否存在，如果不存在则创建
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);  // 0777 允许所有权限，true 表示递归创建
        }

        $target_file = $target_dir . basename($_FILES['image']['name']);
        
        // 移动上传的文件
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = basename($_FILES['image']['name']);
        } else {
            echo "文件上传失败，请检查权限或文件大小限制。";
        }
    }

    // 保存留言到数据库
    $stmt = $pdo->prepare('INSERT INTO messages (user_id, friend_id, message, image_path) VALUES (:user_id, :friend_id, :message, :image_path)');
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':friend_id' => $friend_id,
        ':message' => $message,
        ':image_path' => $image_path
    ]);
}

// 获取所有给当前好友的留言（包括所有用户的留言）
$messages = [];
if ($friend_id) {
    $stmt = $pdo->prepare('
        SELECT m.message, m.created_at, m.image_path, u.username 
        FROM messages m 
        JOIN users u ON m.user_id = u.id
        WHERE m.friend_id = :friend_id
        ORDER BY m.created_at DESC
    ');
    $stmt->execute([':friend_id' => $friend_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言功能 - 社交网络</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            text-align: center;
            background-color: #007B9E;
            color: white;
            padding: 10px;
            font-size: 22px;
            border-radius: 8px;
            position: relative;
        }

        header .back-button {
            position: absolute;
            top: 10px;
            left: 20px;
            background-color: #007B9E;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        .message-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            resize: vertical;
        }

        .message-form input[type="file"] {
            margin-bottom: 10px;
        }

        .message-form button {
            background-color: #28A745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message-form button:hover {
            background-color: #218838;
        }

        .message-list {
            margin-top: 20px;
            overflow-y: auto;
            height: 400px;
        }

        .message-item {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .message-item .message-text {
            margin-bottom: 10px;
        }

        .message-item img {
            max-width: 100%;
            max-height: 300px;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
        }

        .message-item .message-info {
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>

<header>
    留言墙
    <!-- 返回首页按钮 -->
    <a href="index.php" class="back-button">返回首页</a>
</header>

<div class="container">
    <h2>选择好友发布留言</h2>

    <!-- 选择好友 -->
    <form method="GET" action="message.php">
        <label for="friend_id">选择好友：</label>
        <select name="friend_id" id="friend_id">
            <?php foreach ($friends as $friend): ?>
                <option value="<?php echo $friend['id']; ?>" <?php echo ($friend['id'] == $friend_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($friend['username']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">选择好友</button>
    </form>

    <!-- 留言表单 -->
    <?php if ($friend_id): ?>
        <h3>与 <?php echo htmlspecialchars($friend_name); ?> 的留言</h3>

        <form method="POST" enctype="multipart/form-data" class="message-form">
            <textarea name="message" placeholder="写下你的留言..." required></textarea><br>
            <input type="file" name="image"><br>
            <button type="submit">发布留言</button>
            <input type="hidden" name="friend_id" value="<?php echo $friend_id; ?>">
        </form>
    <?php endif; ?>

    <!-- 显示所有给当前好友的留言 -->
    <div class="message-list">
        <?php if ($messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-item">
                    <div class="message-text">
                        <strong><?php echo htmlspecialchars($msg['username']); ?>：</strong>
                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    </div>
                    <?php if ($msg['image_path']): ?>
                        <img src="uploads/messages/<?php echo htmlspecialchars($msg['image_path']); ?>" alt="留言图片">
                    <?php endif; ?>
                    <div class="message-info">
                        <?php echo $msg['created_at']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>还没有留言，快来留下你的第一条留言吧！</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
