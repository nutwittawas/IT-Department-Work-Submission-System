<?php
// เริ่มต้น session
session_start();
?>
<!-- Header Styles -->
 
<style>
/* ตั้งค่า Navbar */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(90deg, rgb(0, 0, 0), rgb(94, 94, 94));
    padding: 10px 20px;
    z-index: 1000;
}

/* ป้องกัน Navbar บังเนื้อหา */
body {
    padding-top: 80px;
}

/* ปรับ Navbar Brand (โลโก้ + ข้อความ) */
.navbar-brand {
    display: flex;
    align-items: center; /* จัดให้อยู่กึ่งกลาง */
    font-size: 20px; /* ปรับขนาดตัวอักษร */
    font-weight: bold;
    color: white;
    text-decoration: none;
}
/* ไฮไลต์เมนูที่กำลังใช้งาน */
.navbar-nav .nav-item .nav-link.active {
    color: #ffcc00; /* สีข้อความไฮไลต์ */
    font-weight: bold;
    border-bottom: 2px solid #ffcc00; /* เส้นขีดใต้ */
}

/* ปรับขนาดโลโก้ */
.navbar-brand img {
    height: 50px; /* ขยายขนาดโลโก้ */
    margin-right: 10px; /* เพิ่มระยะห่างระหว่างโลโก้กับข้อความ */
    border-radius: 10px;
}

/* ปรับ Navbar Menu */
.navbar-nav {
    display: flex;
    align-items: center;
    gap: 15px; /* เพิ่มช่องว่างระหว่างเมนู */
}

/* ปรับให้ลิงก์ใน Navbar ไม่เบียดกัน */
.navbar-nav .nav-item {
    margin: 0 10px;
}

/* ปรับสีและขนาดตัวหนังสือ */
.navbar-nav .nav-item .nav-link,
.navbar-text {
    color: white;
    font-weight: 600;
    font-size: 16px;
    white-space: nowrap; /* ป้องกันข้อความตัดบรรทัด */
}

/* ปรับข้อความ "ยินดีต้อนรับ" */
.navbar-text {
    padding-right: 10px;
}

/* ปุ่มออกจากระบบ */
.logout-link {
    color: #ffcc00;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s;
}

.logout-link:hover {
    color: red;
}

/* ปรับ Navbar บนจอขนาดเล็ก (มือถือ) */
@media (max-width: 768px) {
    .navbar {
        padding: 10px 15px;
    }
    
    .navbar-nav {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .navbar-text {
        display: block;
        padding-left: 10px;
    }
    
    .navbar-toggler {
        border: none;
        background: transparent;
    }

    .navbar-toggler:focus {
        outline: none;
        box-shadow: none;
    }

    /* ปรับเมนูให้แสดงแบบ dropdown */
    .navbar-collapse {
        background: rgba(71, 71, 71, 0.8);
        padding: 10px;
        border-radius: 5px;
    }

    /* ลดขนาดโลโก้และข้อความ */
    .navbar-brand {
        font-size: 16px;
    }

    .navbar-brand img {
        height: 30px; /* ลดขนาดโลโก้ลงเล็กน้อย */
    }
}
</style>

<!-- Header Content -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" alt="โลโก้"> ระบบส่งงานแผนกไอที
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">หน้าแรก</a></li>

                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item"><a class="nav-link" href="submit_job.php">ส่งงาน</a></li>
                    <li class="nav-item">
                        <span class="navbar-text">ยินดีต้อนรับคุณ <?php echo htmlspecialchars($_SESSION['user']['fullname']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="login/logout.php">ออกจากระบบ</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login/login.php">เข้าสู่ระบบ</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ใส่ Bootstrap JS และ jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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