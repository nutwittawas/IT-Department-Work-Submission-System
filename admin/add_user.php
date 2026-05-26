<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $nickname = $_POST["nickname"];
    $phone = $_POST["phone"];
    $username = $_POST["username"];
    $password = $_POST["password"]; // ไม่ใช้ password_hash() ให้เก็บรหัสผ่านธรรมดา
    $status = $_POST["status"];
    $image = null; // กำหนดค่าเริ่มต้นของรูปภาพ

    // ตรวจสอบว่า username ซ้ำในฐานข้อมูลหรือไม่
    $sql_check_username = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check_username);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->bind_result($user_count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($user_count > 0) {
        echo "<script>alert('ชื่อผู้ใช้งานนี้มีอยู่ในระบบแล้ว กรุณาเลือกชื่อผู้ใช้งานใหม่');</script>";
    } else {
        // ตรวจสอบว่าไฟล์อัปโหลดหรือไม่
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $image = $target_file;
        }

        // เพิ่มข้อมูลลงฐานข้อมูล
        $sql = "INSERT INTO users (fullname, nickname, phone, username, password, status, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssis", $fullname, $nickname, $phone, $username, $password, $status, $image);

        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสมาชิก</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .container {
            width: 30%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: rgb(0, 0, 0);
            margin-bottom: 20px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
            width: 100%;
        }

        input[type="text"],
        input[type="password"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }

        input[type="file"] {
            padding: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        /* แก้ไขการแสดงตัวอย่างรูป */
        .preview-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }

        .image-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid #ddd;
            object-fit: cover;
            margin-top: 5px; /* ปรับระยะห่างระหว่างข้อความกับรูป */
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 15px; /* เพิ่มระยะห่างจากรูป */
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 15px;
            }

            input[type="text"],
            input[type="password"],
            select {
                font-size: 14px;
            }

            button {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>เพิ่มสมาชิก</h1>
    <form action="add_user.php" method="POST" enctype="multipart/form-data">
        <label for="fullname">ชื่อ-นามสกุล:</label>
        <input type="text" name="fullname" id="fullname" required>

        <label for="nickname">ชื่อเล่น:</label>
        <input type="text" name="nickname" id="nickname" required>

        <label for="phone">เบอร์โทร:</label>
        <input type="text" name="phone" id="phone" required>

        <label for="username">ชื่อผู้ใช้:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">รหัสผ่าน:</label>
        <input type="password" name="password" id="password" required>

        <label for="status">สถานะ:</label>
        <select name="status" id="status">
            <option value="0">พนักงาน</option>
            <option value="1">หัวหน้า</option>
        </select>

        <!-- ส่วนแสดงรูปตัวอย่าง -->
        <div class="preview-container">
            <label for="image">อัปโหลดรูปโปรไฟล์:</label>
            <input type="file" name="image" id="image" onchange="previewImage(event)">
            <!-- แสดงตัวอย่างรูปที่อัปโหลด -->
            <img id="preview" class="image-preview" src="../uploads/pr.png" title="รูปโปรไฟล์">
        </div>

        <button type="submit">เพิ่มสมาชิก</button>
    </form>
</div>

<script>
function previewImage(event) {
    let reader = new FileReader();
    reader.onload = function () {
        let preview = document.getElementById('preview');
        preview.src = reader.result;
        preview.removeAttribute("alt"); // ลบ alt ออกเมื่อโหลดรูปสำเร็จ
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
