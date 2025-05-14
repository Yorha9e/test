<?php
ob_start();
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = isset($_GET['friend_id']) ? (int)$_GET['friend_id'] : null;
if (!$friend_id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare('SELECT id, username, avatar FROM users WHERE id = :friend_id');
$stmt->execute([':friend_id' => $friend_id]);
$friend = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>与 <?php echo htmlspecialchars($friend['username']); ?> 的聊天</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 页面背景和字体 */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #2e2e2e;  /* 深灰色背景 */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        /* 聊天容器 */
        .chat-container {
            width: 100%;
            max-width: 650px;
            background-color: #333;  /* 深灰色容器 */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* 顶部栏 */
        .header {
            background-color: #444;  /* 深灰色顶部栏 */
            padding: 10px 20px;
            text-align: left;
            color: #fff;
            font-size: 18px;
        }

        /* 消息区域 */
        .messages {
            flex-grow: 1;
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background-color: #444;  /* 更深的灰色背景 */
            border-bottom: 1px solid #666;  /* 更暗的分隔线 */
        }

        /* 消息样式 */
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .message .user {
            font-weight: bold;
            color: #fff;  /* 白色用户名 */
        }

        .message .text {
            margin-top: 5px;
            font-size: 16px;
            color: #ddd;  /* 浅灰色消息文本 */
        }

        /* 输入框和发送按钮 */
        .send-message {
            display: flex;
            padding: 10px;
            background-color: #444;  /* 深灰色背景 */
            border-top: 1px solid #666;  /* 更暗的分隔线 */
        }
        form {
        background-color:#333;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        }
        .send-message input {
            width: calc(100% - 60px);
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #666;  /* 深灰色边框 */
            font-size: 16px;
            background-color: #555;  /* 更深的灰色背景 */
            color: #fff;  /* 白色文本 */
        }

        .send-message button {
            width: 50px;
            height: 50px;
            background-color: #007BFF;  /* 蓝色发送按钮 */
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .send-message button:hover {
            background-color: #0056b3;  /* 蓝色发送按钮 hover */
        }

        /* 返回首页按钮 */
        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
            text-align: center;
            font-size: 18px;
            color: #fff;
            margin: 10px 0;
            text-decoration: none;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.5);  /* 黑色背景 */
            border-radius: 5px;
        }

        .back-to-home:hover {
            background-color: rgba(0, 0, 0, 0.7);  /* 深色 hover */
        }

        /* 滚动到底部按钮 */
        .scroll-to-bottom-btn {
            padding: 10px 20px;
            background-color: #007BFF;  /* 蓝色按钮 */
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin: 10px auto;
            display: block;
            transition: all 0.3s;
        }

        .scroll-to-bottom-btn:hover {
            background-color: #0056b3;  /* 蓝色 hover */
        }
    </style>
</head>
<body>

<!-- 返回首页按钮 -->
<a href="index.php" class="back-to-home">返回首页</a>

<div class="chat-container">
    <div class="header">
        与 <?php echo htmlspecialchars($friend['username']); ?> 的聊天
    </div>

    <div class="messages" id="messages"></div>

    <div class="send-message">
        <form id="messageForm">
            <input type="text" id="messageInput" name="message" placeholder="输入消息" required>
            <button type="submit">&#8593</button>
        </form>
    </div>

    <!-- 滚动到底部按钮 -->
    <button class="scroll-to-bottom-btn" onclick="scrollToBottom()">滚动到底部</button>
</div>

<script>
    var messagesDiv = document.getElementById('messages');
    var userScrolled = false;

    function loadMessages() {
        var friendId = <?php echo $friend_id; ?>;
        var userId = <?php echo $user_id; ?>;
        
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_messages.php?friend_id=' + friendId + '&user_id=' + userId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                messagesDiv.innerHTML = xhr.responseText;
                if (!userScrolled) {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            }
        };
        xhr.send();
    }

    setInterval(loadMessages, 1000);
    loadMessages();

    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();

        var messageInput = document.getElementById('messageInput');
        var message = messageInput.value.trim();
        
        if (message) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    messageInput.value = '';
                    messageInput.focus();
                    loadMessages(); // 仅刷新聊天记录
                }
            };

            xhr.send('message=' + encodeURIComponent(message) + '&friend_id=<?php echo $friend_id; ?>');
        }
    });

    messagesDiv.addEventListener('scroll', function() {
        if (messagesDiv.scrollHeight - messagesDiv.scrollTop <= messagesDiv.clientHeight + 10) {
            userScrolled = false;
        } else {
            userScrolled = true;
        }
    });

    window.onload = function() {
        loadMessages();
    };

    // 滚动到页面底部
    function scrollToBottom() {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
</script>

<?php
ob_end_flush();
?>

</body>
</html>
