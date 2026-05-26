<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสมาชิก</title>
    <link rel="stylesheet" href="merged.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h1>เพิ่มสมาชิก</h1>

    <!-- แจ้งเตือนเมื่อเพิ่มสมาชิกสำเร็จ -->
    <?php if (isset($_GET['success'])): ?>
        <p class="success-msg">✅ เพิ่มสมาชิกสำเร็จ!</p>
    <?php endif; ?>

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

        <label for="image">อัปโหลดรูปภาพ (JPG, JPEG, PNG เท่านั้น):</label>
        <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">

        <button type="submit">เพิ่มสมาชิก</button>
    </form>
</div>

</body>
</html>
