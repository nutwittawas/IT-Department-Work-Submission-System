<?php
require 'db.php'; // ใช้ db.php เพื่อเชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["id"]);
    
    // ลบข้อมูลจากฐานข้อมูล
    $sql = "DELETE FROM job WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลเรียบร้อย'); window.location.href='admin.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
