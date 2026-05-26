<?php
require 'db.php'; // เชื่อมต่อฐานข้อมูล

// รับค่าจาก POST
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($id > 0 && $quantity >= 0) {
    // สร้างคำสั่ง SQL เพื่ออัปเดตจำนวนในฐานข้อมูล
    $sql = "UPDATE it_inventory SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $id); // ผูกค่ากับคำสั่ง SQL

    if ($stmt->execute()) {
        // ถ้าอัปเดตสำเร็จ ส่งข้อมูลกลับ
        echo json_encode(['success' => true]);
    } else {
        // ถ้าเกิดข้อผิดพลาดในการอัปเดต
        echo json_encode(['success' => false, 'error' => 'ไม่สามารถอัปเดตจำนวนได้']);
    }
    $stmt->close();
} else {
    // ถ้าข้อมูลไม่ถูกต้อง
    echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ถูกต้อง']);
}

$conn->close();
?>
