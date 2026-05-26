<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "cyy";

// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตั้งค่าให้รองรับภาษาไทย
$conn->set_charset("utf8mb4");
?>
