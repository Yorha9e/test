<?php
include('database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $avatar = $_FILES['avatar'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($avatar["name"]);

    if (move_uploaded_file($avatar["tmp_name"], $target_file)) {
        $stmt = $conn->prepare('UPDATE users SET avatar = ? WHERE id = ?');
        $stmt->bind_param('si', $target_file, $_SESSION['user_id']);
        if ($stmt->execute()) {
            echo "头像上传成功";
        } else {
            echo "头像上传失败";
        }
    } else {
        echo "上传失败";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar" required>
    <button type="submit">上传头像</button>
</form>
