<?php
require 'db.php'; // ใช้ db.php เพื่อเชื่อมต่อฐานข้อมูล

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $nickname = $_POST["nickname"];
    $phone = $_POST["phone"];
    $username = $_POST["username"];
    $status = $_POST["status"];
    $image = $user["image"];

    // ตรวจสอบ username ซ้ำ (ยกเว้นของตัวเอง)
    $sql_check = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $username, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว กรุณาเปลี่ยนชื่อผู้ใช้ใหม่'); window.history.back();</script>";
        exit();
    }
    $stmt_check->close();

    // ตรวจสอบการกรอกรหัสผ่านใหม่
    $password = $user["password"]; // รหัสผ่านเดิมจากฐานข้อมูล
    if (!empty($_POST["password"])) {
        $password = $_POST["password"]; // ใช้รหัสผ่านใหม่โดยตรง
    }

    // อัปโหลดรูป (ถ้ามี)
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = $target_file;
    }

    // อัปเดตข้อมูล
    $sql = "UPDATE users SET fullname=?, nickname=?, phone=?, username=?, password=?, status=?, image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssisi", $fullname, $nickname, $phone, $username, $password, $status, $image, $id);

    if ($stmt->execute()) {
        echo "<script>alert('แก้ไขเสร็จแล้ว'); window.location.href = 'manage_users.php';</script>";
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสมาชิก</title>
    <style>
        /* รีเซ็ตค่าเริ่มต้น */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* พื้นหลัง */
        body {
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        /* ปรับขนาด container และลดระยะห่าง */
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;        /* ลดจาก 50px เหลือ 30px */
            padding: 20px;           /* คงไว้ หรือปรับลดเหลือ 15px ตามต้องการ */
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        /* หัวข้อ */
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;     /* ลดจาก 20px เหลือ 15px */
            font-weight: 600;        /* ทำให้หนาขึ้น */
        }
        /* ฟอร์ม */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;               /* ลดจาก 15px เหลือ 10px */
        }
        /* label หนาขึ้น */
        form label {
            font-size: 14px;
            color: #333;
            font-weight: 600;        /* เพิ่มความหนา */
        }
        /* ช่องกรอกข้อมูล */
        form input[type="text"],
        form input[type="file"],
        form select {
            width: 100%;
            padding: 8px;           /* ลดจาก 10px เหลือ 8px */
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        /* ปุ่มบันทึก */
        form button {
            padding: 8px 16px;      /* ลดจาก 10px 20px เหลือ 8px 16px */
            background-color: rgb(32, 143, 58);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;       /* เพิ่มความหนา */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #388E3C;
        }
        /* ส่วนรูปภาพ */
        .current-image {
            text-align: center;
        }
        .current-image p {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        /* รูปโปรไฟล์ */
        .thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            transition: transform 0.3s ease;
        }
        .thumbnail:hover {
            transform: scale(1.1);
        }
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px; /* ปรับตามต้องการ */
            }
            form input[type="text"],
            form input[type="file"],
            form select {
                padding: 6px; /* ลดลงอีกในจอเล็ก */
                font-size: 12px;
            }
            form button {
                font-size: 14px;
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>แก้ไขสมาชิก</h1>
        <form action="edit_user.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <label for="fullname">ชื่อ-นามสกุล:</label>
            <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>

            <label for="nickname">ชื่อเล่น:</label>
            <input type="text" name="nickname" id="nickname" value="<?= htmlspecialchars($user['nickname']) ?>" required>

            <label for="phone">เบอร์โทร:</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

            <label for="username">ชื่อผู้ใช้:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="password">รหัสผ่าน (รหัสเก่าของคุณ):</label>
            <input type="text" name="password" id="password" value="<?= htmlspecialchars($user['password']) ?>" required>



            <label for="status">สถานะ:</label>
            <select name="status" id="status">
                <option value="0" <?= ($user['status'] == 0) ? 'selected' : '' ?>>พนักงาน</option>
                <option value="1" <?= ($user['status'] == 1) ? 'selected' : '' ?>>หัวหน้า</option>
            </select>

            <label for="image">อัปโหลดรูปภาพใหม่:</label>
            <input type="file" name="image" id="image">
            
            <!-- แสดงรูปปัจจุบัน -->
            <?php if (!empty($user['image'])): ?>
                <div class="current-image">
                    <p>รูปปัจจุบัน:</p>
                    <img src="<?= htmlspecialchars($user['image']) ?>" alt="รูปโปรไฟล์" class="thumbnail">
                </div>
            <?php endif; ?>

            <button type="submit">บันทึก</button>
        </form>
    </div>
</body>
</html>
