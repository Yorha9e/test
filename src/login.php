<?php
session_start(); // 开启会话，存储用户数据

include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 调用登录函数
    $user = login_user($username, $password);

    if ($user) {
        // 登录成功，保存用户信息到 session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];

        // 跳转到首页
        header("Location: index.php");  // 假设首页是 index.php
        exit();  // 确保脚本停止执行
    } else {
        $error = "用户名或密码错误";
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 社交网络</title>
    <style>
        /* 基本样式重置 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            color: #333;
            line-height: 1.6;
        }

        /* 登录表单容器 */
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007B9E;
            margin-bottom: 20px;
        }

        /* 错误信息 */
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }

        /* 表单输入框和标签 */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .input-group input:focus {
            border-color: #007B9E;
            outline: none;
            background-color: #fff;
        }

        /* 登录按钮 */
        button {
            width: 100%;
            padding: 12px;
            background-color: #007B9E;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #005f6a;
        }

        /* 注册链接 */
        p {
            text-align: center;
            margin-top: 20px;
        }

        p a {
            color: #007B9E;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>登录到社交网络</h2>

    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

    <form method="POST" class="login-form">
        <div class="input-group">
            <label for="username">用户名</label>
            <input type="text" id="username" name="username" placeholder="请输入用户名" required>
        </div>
        <div class="input-group">
            <label for="password">密码</label>
            <input type="password" id="password" name="password" placeholder="请输入密码" required>
        </div>
        <button type="submit">登录</button>
        <p>还没有账户？<a href="register.php">注册</a></p>
    </form>
</div>

</body>
</html>
