<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                url('../images/background.jpg') center center / cover no-repeat;
            filter: blur(5px);
            transform: scale(1.1);
            z-index: -1;
        }

        .login-container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .login-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            background-color: rgb(76, 86, 175);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        .login-container .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>เข้าสู่ระบบ</h1>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!</p>';
        }

        // ดึงค่าจาก Cookies ถ้ามี
        $saved_username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
        $saved_password = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
        ?>

        <form action="login_process.php" method="POST">
            <label for="username">ชื่อผู้ใช้:</label>
            <input type="text" id="username" name="username" placeholder="กรอกยูสเซอร์" value="<?php echo htmlspecialchars($saved_username); ?>" required>

            <label for="password">รหัสผ่าน:</label>
            <input type="password" id="password" name="password" placeholder="กรอกรหัสผ่าน" value="<?php echo htmlspecialchars($saved_password); ?>" required>

            <label>
                <input type="checkbox" name="remember" <?php echo $saved_username ? 'checked' : ''; ?>> จดจำฉัน
            </label>

            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
