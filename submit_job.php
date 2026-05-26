<?php
require 'db.php'; // ไฟล์เชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำการส่งงาน'); window.location.href='login/login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_name = $_POST['job_name'];
    $sender_name = $_SESSION['user']['nickname']; // ดึงชื่อเล่นจาก session
    $equipment = $_POST['equipment'] == "ไม่มี" ? "ไม่มี" : ''; // ถ้าเลือก "ไม่มี" ให้เก็บว่า "ไม่มี" หรือถ้าเลือก "มี" จะไม่เก็บอะไร

    $equipment_detail = isset($_POST['equipment_detail']) ? $_POST['equipment_detail'] : ''; // ตรวจสอบค่าอุปกรณ์ที่พิมพ์
    if ($_POST['equipment'] == "มี" && !empty($equipment_detail)) {
        $equipment = $equipment_detail; // ถ้าเลือก "มี" และมีการพิมพ์รายละเอียด ให้เก็บเฉพาะสิ่งที่พิมพ์
    }
    
    // ตรวจสอบและอัปโหลดรูปภาพ (ขั้นต่ำ 1 รูป สูงสุด 3 รูป)
    if (isset($_FILES["image"])) {
        $total_files = count($_FILES["image"]["name"]);
        if ($total_files < 1 || $total_files > 3) {
            echo "<script>alert('กรุณาอัพโหลดรูปภาพอย่างน้อย 1 รูป และไม่เกิน 3 รูป'); window.location.href='submit_job.php';</script>";
            exit();
        }

        $uploaded_files = [];
        $target_dir = "uploads/";
        for ($i = 0; $i < $total_files; $i++) {
            $file_name = basename($_FILES["image"]["name"][$i]);
            $target_file = $target_dir . $file_name;
            // ตรวจสอบการอัปโหลดไฟล์แต่ละไฟล์
            if (move_uploaded_file($_FILES["image"]["tmp_name"][$i], $target_file)) {
                $uploaded_files[] = $file_name;
            } else {
                $error = $_FILES["image"]["error"][$i];
                $error_message = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์รูปภาพ";
                
                switch ($error) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_message = "ไฟล์มีขนาดใหญ่เกินไป";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error_message = "การอัปโหลดไฟล์ไม่สมบูรณ์";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error_message = "ไม่พบไฟล์ที่ต้องการอัปโหลด";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error_message = "ไม่มีไดเรกทอรีชั่วคราว";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error_message = "ไม่สามารถเขียนไฟล์ลงดิสก์ได้";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error_message = "มีข้อผิดพลาดจากส่วนขยายของ PHP";
                        break;
                    default:
                        $error_message = "เกิดข้อผิดพลาดที่ไม่รู้จัก";
                        break;
                }
                echo "<script>alert('$error_message'); window.location.href='submit_job.php';</script>";
                exit();
            }
            
        }
        // เก็บชื่อไฟล์ทั้งหมดในรูปแบบคั่นด้วยเครื่องหมายจุลภาค
        $images_string = implode(',', $uploaded_files);
    } else {
        echo "<script>alert('กรุณาอัพโหลดรูปภาพ'); window.location.href='submit_job.php';</script>";
        exit();
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO job (job_name, equipment, image, sender_name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $job_name, $equipment, $images_string, $sender_name);

    if ($stmt->execute()) {
        echo "<script>alert('ส่งงานสำเร็จ!'); window.location.href='submit_job.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่งงาน</title>
    <link rel="stylesheet" href="sve.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 20px;
            color: #fff;
            padding-top: 70px;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            width: 100%;
            max-width: 700px;
        }
        .card {
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            border-radius: 15px;
            padding: 35px;
            box-shadow: 0 10px 20px rgba(39, 39, 39, 0.6);
            width: 100%;
        }
        h3 {
            text-align: center;
            font-weight: 700;
            font-size: 24px;
            color: #fff;
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            font-size: 18px;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 5px;
            font-size: 15px;
            font-weight: 500;
            color: #fff;
            transition: 0.3s;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.5);
            border-color: #ffcc00;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.6);
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .btn-primary {
            background: #0066ff;
            border: none;
            font-size: 20px;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            transition: 0.3s;
            margin-top: 15px;
        }
        .btn-primary:hover {
            background: #0044cc;
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .card {
                padding: 20px;
            }
            body {
                background-attachment: scroll;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <div class="card p-4">
            <h3 class="text-center">ส่งงาน</h3>
            <form action="submit_job.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">ชื่องาน</label>
                    <input type="text" name="job_name" class="form-control" required>
                </div>
                <div class="mb-3">
    <label class="form-label">อุปกรณ์ที่ใช้</label>
    <select id="equipment_select" class="form-control" name="equipment">
        <option value="ไม่มี" selected>ไม่มี</option>
        <option value="มี">มี</option>
    </select>
    <textarea id="equipment_input" name="equipment_detail" class="form-control mt-2" placeholder="กรอกอุปกรณ์ที่ใช้" style="display: none;"></textarea>
</div>


                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ส่ง</label>
                    <input type="text" name="sender_name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user']['nickname']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">อัปโหลดรูปภาพ (1-3 รูป)</label>
                    <input type="file" name="image[]" class="form-control" accept="image/*" multiple required>
                </div>
                <button type="submit" class="btn btn-primary w-100">ส่งงาน</button>
            </form>
        </div>
    </div>
    <script>
document.getElementById("equipment_select").addEventListener("change", function() {
    let inputField = document.getElementById("equipment_input");
    if (this.value === "มี") {
        inputField.style.display = "block";
    } else {
        inputField.style.display = "none";
    }
});

</script>


    <?php include 'footer.php'; ?>
</body>
</html>