<?php
session_start();
require_once 'database.php'; // 包含数据库连接和更新头像的函数

// 检查用户是否已登录，如果未登录则重定向到登录页面
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 获取当前登录用户的信息
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 获取当前用户的头像，如果 session 中没有，则从数据库中获取
if (!isset($_SESSION['avatar'])) {
    $user_info = get_user_info($user_id);  // 从 database.php 获取用户信息
    $_SESSION['avatar'] = $user_info['avatar'];
}
$avatar = $_SESSION['avatar']; // 使用 session 中的头像信息

// 处理头像上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $avatar_name = $_FILES['avatar']['name'];
    $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
    $avatar_path = 'uploads/' . basename($avatar_name);

    // 验证文件类型（可以根据需要进一步扩展）
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($avatar_name, PATHINFO_EXTENSION));

    if (in_array($file_extension, $allowed_extensions)) {
        // 移动文件到服务器指定目录
        if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
            // 更新 session 中的头像信息
            $_SESSION['avatar'] = $avatar_name;

            // 更新数据库中的头像信息
            update_avatar($user_id, $avatar_name); // 调用更新头像的函数

            header('Location: profile.php'); // 更新成功后重新加载页面
            exit();
        } else {
            $error_message = '上传头像失败，请重试。';
        }
    } else {
        $error_message = '无效的文件类型。仅支持 JPG, JPEG, PNG 和 GIF 格式。';
    }
}

// 更新头像路径到数据库
function update_avatar($user_id, $avatar_name) {
    global $pdo; // 假设 $pdo 是你数据库连接的实例

    // 更新数据库中的头像路径
    $sql = "UPDATE users SET avatar = :avatar WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':avatar' => $avatar_name,
        ':user_id' => $user_id
    ]);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人资料 - 社交网站</title>
    <style>
        /* Reset some basic elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f5;
            color: #333;
            line-height: 1.6;
        }

        /* Container for centering the content */
        .container {
            width: 80%;
            margin: 0 auto;
        }

        /* Header Styling */
        header {
            background-color: #007B9E;  /* 主色调：深蓝色背景 */
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header nav ul {
            list-style-type: none;
            padding: 0;
            margin-top: 10px;
        }

        header nav ul li {
            display: inline;
            margin: 0 15px;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        header nav ul li a:hover {
            text-decoration: underline;
        }

        /* User Profile Section */
        .user-profile {
            background-color: #fff;
            padding: 30px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .user-profile h1 {
            font-size: 36px;
            color: #007B9E;
            margin-bottom: 20px;
        }

        /* Profile Information Section */
        .profile-info {
            margin-bottom: 20px;
        }

        .profile-info img.avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #007B9E; /* 深蓝色边框 */
        }

        .profile-info h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-info label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }

        .profile-info input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #ccc;
            border-radius: 5px;
            background-color: #f0f0f5;
        }

        .profile-info button {
            background-color: #007B9E; /* 使用主色调 */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .profile-info button:hover {
            background-color: #005f6a;
        }

        /* Error Message Styling */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Footer (if needed) */
        footer {
            background-color: #007B9E; /* 主色调：深蓝色 */
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }

        footer a {
            color: white;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .profile-info img.avatar {
                width: 120px;
                height: 120px;
            }

            .profile-info button {
                width: 100%;
            }

            header nav ul li {
                display: block;
                margin: 10px 0;
            }

            .profile-info input[type="file"] {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 顶部导航栏 -->
        <header>
            <nav>
                <ul>
                    <li><a href="index.php">首页</a></li>
                    <li><a href="profile.php">个人资料</a></li>
                    <li><a href="logout.php">退出登录</a></li>
                </ul>
            </nav>
        </header>

        <!-- 用户资料区 -->
        <div class="user-profile">
            <h1>个人资料</h1>

            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="profile-info">
                    <img src="uploads/<?php echo htmlspecialchars($avatar); ?>" alt="头像" class="avatar">
                    <h2><?php echo htmlspecialchars($username); ?></h2>
                    <label for="avatar">上传新头像：</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*">
                    <button type="submit">更新头像</button>
                </div>
            </form>

            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
