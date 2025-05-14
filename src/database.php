<?php
$host = 'mysql';  // MySQL 服务的容器名称或主机地址
$dbname = 'social_network';  // 数据库名称
$username = 'yorha';  // 数据库用户名
$password = 'hxj365988';  // 数据库密码

// 数据库连接
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // 设置 PDO 错误模式为异常
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 注册用户
function register_user($username, $password, $avatar_file) {
    global $pdo;

    // 检查用户名是否已存在
    $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    if ($stmt->fetchColumn() > 0) {
        return "用户名已存在";
    }

    // 处理头像文件上传
    $avatar = 'default.png'; // 默认头像
    if ($avatar_file && $avatar_file['error'] === UPLOAD_ERR_OK) {
        $avatar = upload_avatar($avatar_file); // 获取上传头像的文件名
        if (strpos($avatar, 'error') !== false) {
            return $avatar; // 如果出现错误，返回错误信息
        }
    }

    // 密码加密
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // 插入用户数据
    $sql = "INSERT INTO users (username, password, avatar) VALUES (:username, :password, :avatar)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username, ':password' => $hashed_password, ':avatar' => $avatar]);

    return null;  // 注册成功
}

// 登录用户
function login_user($username, $password) {
    global $pdo;

    // 查找用户
    $sql = "SELECT id, username, password, avatar FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // 登录成功，返回用户信息
        return $user;
    }

    return null;  // 用户名或密码错误
}

// 获取用户信息
function get_user_info($user_id) {
    global $pdo;

    $sql = "SELECT username, avatar FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 上传头像功能
function upload_avatar($file) {
    // 上传文件的目标路径
    $upload_dir = 'uploads/';
    
    // 检查是否上传了文件并且没有错误
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // 限制上传的文件类型
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_exts)) {
            // 给文件重命名，避免重名文件被覆盖
            $new_file_name = uniqid('', true) . '.' . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            // 移动文件到目标路径
            if (move_uploaded_file($file_tmp, $file_path)) {
                return $new_file_name; // 返回文件名
            } else {
                return '文件上传失败';
            }
        } else {
            return '只允许上传 JPG, PNG, GIF 格式的图片';
        }
    }
    return '文件上传错误';
}
?>
