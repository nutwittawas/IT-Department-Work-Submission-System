<?php
session_start();
require '../db.php'; // ดึงไฟล์เชื่อมต่อฐานข้อมูล

// รับข้อมูลจากฟอร์ม
$username = $_POST['username'];
$password = $_POST['password'];
$remember = isset($_POST['remember']); // ตรวจสอบว่าผู้ใช้เลือก "จดจำฉัน" หรือไม่

// คำสั่ง SQL (ปัจจุบันยังไม่ใช้ Prepared Statement)
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ตรวจสอบรหัสผ่านแบบปกติ (ไม่เข้ารหัส)
    if ($user['password'] === $password) {
        // เก็บข้อมูลผู้ใช้ใน session
        $_SESSION['user'] = $user;

        // ถ้าผู้ใช้เลือก "จดจำฉัน" ให้ตั้งค่า Cookies
        if ($remember) {
            setcookie("username", $username, time() + (86400 * 30), "/"); // เก็บชื่อผู้ใช้ 30 วัน
            setcookie("password", $password, time() + (86400 * 30), "/"); // เก็บรหัสผ่าน 30 วัน
        } else {
            setcookie("username", "", time() - 3600, "/"); // ลบ Cookie ถ้าไม่ได้เลือก "จดจำฉัน"
            setcookie("password", "", time() - 3600, "/");
        }

        // ตรวจสอบค่า status และเปลี่ยนเส้นทางไปหน้าที่เหมาะสม
        if ($user['status'] == 0) {
            echo "<script>alert('เข้าสู่ระบบเรียบร้อยแล้ว'); window.location.href='../submit_job.php';</script>";
        } elseif ($user['status'] == 1) {
            echo "<script>alert('เข้าสู่ระบบเรียบร้อยแล้ว'); window.location.href='../admin/admin.php';</script>";
        } else {
            echo "<script>alert('สิทธิ์ของคุณไม่ถูกต้อง'); window.location.href='login.php?error=2';</script>";
        }
        exit();
    } else {
        echo "<script>alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'); window.location.href='login.php?error=1';</script>";
    }
} else {
    echo "<script>alert('ไม่พบชื่อผู้ใช้นี้ในระบบ'); window.location.href='login.php?error=1';</script>";
}

$conn->close();
?>
