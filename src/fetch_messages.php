<?php
session_start();
require_once 'database.php';

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    echo "未登录";
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = isset($_GET['friend_id']) ? (int)$_GET['friend_id'] : null;

if (!$friend_id) {
    echo "无效的好友 ID";
    exit();
}

// 查询聊天记录
$stmt = $pdo->prepare('
    SELECT * FROM chat_messages 
    WHERE (sender_id = :user_id AND receiver_id = :friend_id) 
       OR (sender_id = :friend_id AND receiver_id = :user_id) 
    ORDER BY created_at ASC
');
$stmt->execute([':user_id' => $user_id, ':friend_id' => $friend_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 输出聊天记录
foreach ($messages as $message) {
    $sender = ($message['sender_id'] == $user_id) ? "我" : "对方";
    echo '<div class="message">';
    echo '<div class="user">' . htmlspecialchars($sender) . ':</div>';
    echo '<div class="text">' . htmlspecialchars($message['message']) . '</div>';
    echo '</div>';
}
?>

