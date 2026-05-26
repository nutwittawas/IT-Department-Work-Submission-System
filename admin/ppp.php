<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
require 'db.php'; // ดึงไฟล์ db.php มาใช้งาน
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบส่งงานแผนกไอที</title>
    <style>
        /* 🔹 สไตล์ Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-logo img {
            height: 35px;
            margin-right: 10px;
        }

        .navbar-logo span {
            color: white;
            font-size: 18px; /* ลดขนาดฟอนต์ลง */
            font-weight: bold;
        }

        /* 🔹 ปุ่มเมนู (☰) */
        .menu-toggle {
            font-size: 24px;
            color: white;
            background: none;
            border: none;
            cursor: pointer;
            display: block;
        }

        /* 🔹 เมนู Dropdown */
        .navbar-content {
            position: absolute;
            top: 60px;
            right: 10px;
            width: 200px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
            padding: 10px 0;
            display: none; /* ซ่อนเมนูก่อน */
            flex-direction: column;
        }

        .navbar-content.active {
            display: flex;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px;
            display: block;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .navbar-welcome {
            color: white;
            font-size: 14px;
            padding: 10px;
            text-align: center;
        }

        .profile-img, .thumbnailsve {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
            display: block;
            margin: 10px auto;
        }

        /* 🔹 Responsive Design */
        @media screen and (max-width: 768px) {
            .navbar {
                flex-direction: row;
                align-items: center;
            }

            .navbar-content {
                right: 10px;
                width: 180px;
            }

            .navbar a {
                text-align: center;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="navbar">
        <!-- โลโก้ และ ชื่อระบบ -->
        <div class="navbar-left">
            <div class="navbar-logo">
                <img src="../images/logo.png" alt="โลโก้">
                <span>ส่งงานแผนก IT</span>
            </div>
        </div>

        <!-- ปุ่มเมนู -->
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>

        <!-- เมนู Dropdown -->
        <div class="navbar-content">
            <a href="admin.php">หน้าแรก</a>
            <a href="manage_users.php">ส่งงาน</a>

            <?php if (isset($_SESSION['user'])): ?>
                <div class="navbar-welcome">
                    <?php if (!empty($_SESSION['user']['image'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user']['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="รูปโปรไฟล์" class="thumbnailsve">
                    <?php else: ?>
                        <img src="../uploads/default-profile.png" alt="รูปโปรไฟล์" class="profile-img">
                    <?php endif; ?>
                    
                    <span>ยินดีต้อนรับคุณ <?= htmlspecialchars($_SESSION['user']['fullname'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <a href="logout.php">ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    function toggleMenu() {
        document.querySelector(".navbar-content").classList.toggle("active");
    }
</script>

</body>
</html>
