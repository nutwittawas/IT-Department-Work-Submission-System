<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบส่งงานแผนกไอที</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>

        
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #333;
            z-index: 1000;
        }

        .navbar3 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            min-height: 60px;
        }

        .navbar-logo3 {
        display: flex;
        align-items: center;
    }

    .navbar-logo3 img {
        height: 40px;
        margin-right: 10px;
        border-radius: 10px;
        transition: height 0.3s ease; /* เพิ่ม transition เพื่อความสมูท */
    }

    .navbar-logo3 span {
        color: white;
        font-size: 20px;
        font-weight: bold;
        transition: font-size 0.3s ease; /* เพิ่ม transition เพื่อความสมูท */
    }

        .navbar-content3 {
            display: flex;
            align-items: center;
            gap: 10px;
        }
/* ไฮไลต์เมนูที่กำลังใช้งาน */
.navbar-nav .nav-item .nav-link.active {
    color: #ffcc00; /* สีข้อความไฮไลต์ */
    font-weight: bold;
    border-bottom: 2px solid #ffcc00; /* เส้นขีดใต้ */
}

        .navbar3 a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 20px;
            transition: background-color 0.3s ease;
        }

        .navbar3 a.active {
    color: #ffcc00; /* สีข้อความไฮไลต์ */
    font-weight: bold;
    border-bottom: 2px solid #ffcc00; /* เส้นขีดใต้ */
}


.navbar-welcome3 {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    color:rgb(255, 239, 250); /* สีข้อความที่ต้องการ */
}


        .profile-img3, .thumbnail33 {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
        }

        .menu-toggle3 {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }

        .dropdown-menu3 {
            display: none;
            flex-direction: column;
            background-color: #222;
            position: absolute;
            top: 60px;
            right: 10px;
            width: 220px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            padding: 10px 0;
            text-align: center;
        }

        .dropdown-menu3 a {
            padding: 12px;
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
        }

        .dropdown-menu3 a:hover {
            background-color: #575757;
        }

        .main-content {
            margin-top: 80px;
            padding: 20px;
        }

        @media screen and (max-width: 768px) {
            .navbar-logo3 img {
            height: 30px; /* ลดขนาดโลโก้ */
        }

        .navbar-logo3 span {
            font-size: 16px; /* ลดขนาดตัวอักษรชื่อระบบ */
        }
            .navbar-content3 {
                display: none;
            }

            .menu-toggle3 {
                display: block;
            }

            .dropdown-menu3 {
                position: absolute;
                width: 100%;
                top: 60px;
                left: 0;
            }
        }
    </style>
    <script>
        $(document).ready(function () {
            // ดึง URL ปัจจุบัน
            var currentUrl = window.location.href;

            // วนลูปตรวจสอบว่าลิงก์ไหนตรงกับ URL ปัจจุบัน
            $(".navbar3 a").each(function () {
                if (this.href === currentUrl) {
                    $(this).addClass("active");
                }
            });

            // Toggle Dropdown Menu
            $(".menu-toggle3").click(function () {
                $("#dropdown-menu3").toggle();
            });

            // ปิดเมนูเมื่อคลิกข้างนอก
            $(document).click(function (event) {
                if (!$(event.target).closest(".menu-toggle3, .dropdown-menu3").length) {
                    $("#dropdown-menu3").hide();
                }
            });
        });
    </script>
</head>
<body>
<header>
    <div class="navbar3">
        <div class="navbar-logo3">
            <img src="../images/logo.png" alt="โลโก้">
            <span>ระบบส่งงานแผนกไอที (สำหรับแอดมิน)</span>
        </div>
        <div class="navbar-content3">
            <a href="admin.php">ตารางการส่งงานแผนกไอที</a>
            <a href="manage_users.php">จัดการสมาชิก</a>
            <a href="inventory.php">จัดการอุปกรณ์แผนกไอที</a>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="navbar-welcome3">
                    <?php if (!empty($_SESSION['user']['image'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user']['image']); ?>" alt="รูปโปรไฟล์" class="thumbnail33">
                    <?php else: ?>
                        <img src="../uploads/default-profile.png" alt="รูปโปรไฟล์" class="profile-img3">
                    <?php endif; ?>
                    <span>ยินดีต้อนรับคุณ <?= htmlspecialchars($_SESSION['user']['fullname']); ?></span>
                </div>
                <a href="../login/logout.php">ออกจากระบบ</a>
            <?php else: ?>
                <a href="../login/login.php">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
        <button class="menu-toggle3">☰</button>
        <div class="dropdown-menu3" id="dropdown-menu3">
            <a href="admin.php">ตารางการส่งงานแผนกไอที</a>
            <a href="manage_users.php">จัดการสมาชิก</a>
            <a href="inventory.php">จัดการอุปกรณ์แผนกไอที</a>
            <a href="../login/logout.php">ออกจากระบบ</a>
        </div>
    </div>
</header>
<div class="main-content">
    <!-- เนื้อหาหลักของหน้าเว็บ -->
</div>
</body>
</html>
<script>
    $(document).ready(function () {
        // ดึง URL ปัจจุบัน
        var currentUrl = window.location.href;
        
        // วนลูปตรวจสอบว่าลิงก์ไหนตรงกับ URL ปัจจุบัน
        $(".navbar-nav .nav-item .nav-link").each(function () {
            if (this.href === currentUrl) {
                $(this).addClass("active");
            }
        });
    });
</script>