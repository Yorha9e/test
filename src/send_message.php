<?php
session_start();
require_once 'database.php';

// 获取用户和聊天对象的信息
$user_id = $_SESSION['user_id'];
$friend_id = isset($_POST['friend_id']) ? (int)$_POST['friend_id'] : null;
$message = isset($_POST['message']) ? $_POST['message'] : '';

if ($friend_id && $message) {
    // 将消息插入数据库
    $stmt = $pdo->prepare('INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (:user_id, :friend_id, :message)');
    $stmt->execute([':user_id' => $user_id, ':friend_id' => $friend_id, ':message' => $message]);

    // 返回操作成功的响应（可以返回一个状态或刚插入的消息等）
    echo 'success';  // 你可以返回 JSON 数据，例如：
    // echo json_encode(['status' => 'success', 'message' => $message]);
} else {
    echo 'error';
}
?>
