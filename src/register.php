<?php
include('database.php');  // 引入数据库连接文件

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 调用注册函数
    $error = register_user($username, $password);

    if ($error) {
        echo "<p style='color:red;'>$error</p>";  // 输出错误消息
    } else {
        echo "<p style='color:green;'>注册成功！现在可以<a href='login.php'>登录</a></p>";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册 - 社交网站</title>
    <style>
        /* Reset some basic elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        /* Container for centering the content */
        .container {
            width: 80%;
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007B9E;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007B9E;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #005f6a;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            font-size: 16px;
            margin-top: 10px;
            text-align: center;
        }

        .redirect-link {
            color: #007B9E;
            text-decoration: none;
        }

        .redirect-link:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>用户注册</h1>

        <!-- 注册表单 -->
        <form method="POST">
            <input type="text" name="username" placeholder="用户名" required>
            <input type="password" name="password" placeholder="密码" required>
            <button type="submit">注册</button>
        </form>

        <p class="redirect-link">已有账号？ <a href="login.php">登录</a></p>

        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
