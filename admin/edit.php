<?php
require 'db.php'; // ใช้ db.php เพื่อเชื่อมต่อฐานข้อมูล

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $job_name = $_POST['job_name'];
        $equipment = $_POST['equipment'];
        $sender_name = $_POST['sender_name'];
        $location = $_POST['location'];

        $sql = "UPDATE job SET job_name='$job_name', equipment='$equipment', sender_name='$sender_name', location='$location' WHERE id=$id";
        $conn->query($sql);
        header("Location: index.php");
        exit();
    }

    $sql = "SELECT * FROM job WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูล</title>
</head>
<body>
<h1>แก้ไขข้อมูล</h1>
<form action="" method="POST">
    <label for="job_name">ชื่องาน:</label>
    <input type="text" id="job_name" name="job_name" value="<?php echo $row['job_name']; ?>" required>

    <label for="equipment">อุปกรณ์ที่เบิก:</label>
    <textarea id="equipment" name="equipment" rows="3" required><?php echo $row['equipment']; ?></textarea>

    <label for="sender_name">ชื่อผู้ส่งงาน:</label>
    <input type="text" id="sender_name" name="sender_name" value="<?php echo $row['sender_name']; ?>" required>

    <label for="location">สถานที่:</label>
    <input type="text" id="location" name="location" value="<?php echo $row['location']; ?>" required>

    <button type="submit">บันทึกการเปลี่ยนแปลง</button>
</form>
</body>
</html>
