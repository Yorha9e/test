// script.js

// 等待 DOM 加载完成后执行
document.addEventListener("DOMContentLoaded", function () {
    
    // 注册表单验证
    const registerForm = document.getElementById("registerForm");
    registerForm.addEventListener("submit", function(event) {
        const username = document.getElementById("registerUsername").value;
        const password = document.getElementById("registerPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        
        // 简单的用户名和密码检查
        if (username.length < 3 || password.length < 6) {
            alert("用户名必须至少3个字符，密码必须至少6个字符");
            event.preventDefault();  // 阻止表单提交
        } else if (password !== confirmPassword) {
            alert("密码和确认密码不匹配");
            event.preventDefault();
        }
    });

    // 登录表单验证
    const loginForm = document.getElementById("loginForm");
    loginForm.addEventListener("submit", function(event) {
        const username = document.getElementById("loginUsername").value;
        const password = document.getElementById("loginPassword").value;

        // 简单的用户名和密码检查
        if (username.length < 3 || password.length < 6) {
            alert("用户名和密码必须至少3个字符");
            event.preventDefault();
        }
    });

    // 更新头像
    const avatarInput = document.getElementById("avatarInput");
    avatarInput.addEventListener("change", function() {
        const formData = new FormData();
        formData.append("avatar", avatarInput.files[0]);

        // 使用 AJAX 上传头像
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "upload_avatar.php", true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert("头像更新成功!");
                // 更新头像显示
                document.getElementById("currentAvatar").src = xhr.responseText;
            } else {
                alert("头像上传失败，请重试!");
            }
        };
        xhr.send(formData);
    });

    // 用户注册（使用 AJAX 提交表单）
    const registerSubmitBtn = document.getElementById("registerSubmit");
    registerSubmitBtn.addEventListener("click", function(event) {
        const username = document.getElementById("registerUsername").value;
        const password = document.getElementById("registerPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;

        // 简单的表单验证
        if (username.length < 3 || password.length < 6) {
            alert("用户名必须至少3个字符，密码至少6个字符");
            event.preventDefault();
            return;
        }

        if (password !== confirmPassword) {
            alert("密码和确认密码不匹配");
            event.preventDefault();
            return;
        }

        // 使用 AJAX 提交注册信息
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "register.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert("注册成功!");
                window.location.href = "login.php";  // 跳转到登录页面
            } else {
                alert("注册失败: " + xhr.responseText);
            }
        };
        xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
    });

    // 用户登录（使用 AJAX 提交表单）
    const loginSubmitBtn = document.getElementById("loginSubmit");
    loginSubmitBtn.addEventListener("click", function(event) {
        const username = document.getElementById("loginUsername").value;
        const password = document.getElementById("loginPassword").value;

        // 简单的表单验证
        if (username.length < 3 || password.length < 6) {
            alert("用户名和密码必须至少3个字符");
            event.preventDefault();
            return;
        }

        // 使用 AJAX 提交登录信息
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert("登录成功!");
                    window.location.href = "profile.php";  // 跳转到个人资料页面
                } else {
                    alert("用户名或密码错误");
                }
            } else {
                alert("登录失败: " + xhr.responseText);
            }
        };
        xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
    });
});
