<?php
session_start();
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include 'db.php';

// กำหนดจำนวนงานที่แสดงต่อหน้า
$limit = 4;

// รับหมายเลขหน้าจาก URL (ถ้าไม่มีให้เป็นหน้าแรก)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

// ดึงจำนวนงานทั้งหมดเพื่อคำนวณจำนวนหน้า
$countQuery = "SELECT COUNT(*) as total FROM job";
$countResult = $conn->query($countQuery);
$totalJobs = 0;
if ($countResult && $row = $countResult->fetch_assoc()) {
    $totalJobs = $row['total'];
}
$totalPages = ceil($totalJobs / $limit);

// ดึงข้อมูลงานสำหรับหน้าปัจจุบัน
$query = "SELECT * FROM job LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก</title>
    <link rel="stylesheet" href="sve.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
/* 1) ตั้งค่า body ให้ตำแหน่งเป็น relative และนำ background เดิมออก */
body {
    font-family: 'Montserrat', sans-serif;
    margin: 0;
    padding: 20px;
    color: #fff;
    padding-top: 70px;  /* เผื่อพื้นที่ให้ Navbar */
    position: relative; /* สำคัญ เพื่อให้ pseudo-element อยู่ด้านล่าง */
    background: none;   /* ยกเลิก background-image เดิม (ถ้ามี) */
}

/* 2) สร้าง pseudo-element สำหรับพื้นหลังเบลอและมืด */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), /* เลเยอร์สีดำโปร่งใส */
        url('images/background.jpg') center center / cover no-repeat;
    filter: blur(2px);
    transform: scale(1.1); /* ขยายเล็กน้อยเพื่อซ่อนขอบเบลอ */
    z-index: -1;
}



        /* -----------------------------------------------------
           Layout หลัก (2 คอลัมน์) สำหรับ Card แรก
        ----------------------------------------------------- */
        .container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        /* Card พื้นฐาน */
        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease-in-out;
            background: rgba(0, 0, 0, 0.82);
            color: #fff;
            backdrop-filter: none;
            position: relative;
        }
        .card:hover {
            transform: scale(1.03);
        }

        /* -----------------------------------------------------
           ส่วนแกลเลอรี (container ที่สอง)
        ----------------------------------------------------- */
        .gallery-card {
            grid-column: span 2; /* ให้กินเต็มความกว้าง 2 คอลัมน์ */
            padding: 20px;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .image-card {
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        .image-card:hover {
            transform: scale(1.1);
        }
        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .image-card p {
            margin-top: 10px;
            font-size: 14px;
        }
        .card.gallery-card .card-body h5 {
            font-weight: bold;
         margin-bottom: 40px; /* ปรับค่าตามต้องการ */
                }


        /* -----------------------------------------------------
           สไตล์ของ Modal แสดงรูปภาพ
        ----------------------------------------------------- */
        .modal-content img {
            width: 100%;
            height: auto;
            transition: transform 0.3s ease-in-out;
        }
        .modal-body {
            overflow: hidden;
            text-align: center;
        }
        .modal-content {
            background: rgba(0, 0, 0, 0.8);
        }

        /* -----------------------------------------------------
           สไตล์ Pagination ให้เข้ากับธีม
        ----------------------------------------------------- */
        .pagination .page-link {
            background: rgba(0, 0, 0, 0.82);
            color: #fff;
            border: 1px solid #fff;
            margin: 0 3px;
            transition: transform 0.3s ease-in-out;
        }
        .pagination .page-link:hover {
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            transform: scale(1.1);
        }
        .page-item.active .page-link {
            background: #0066ff;
            border-color: #0066ff;
        }
        .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* -----------------------------------------------------
           Media Queries: ปรับ layout เมื่อย่อจอ
        ----------------------------------------------------- */
        @media (max-width: 1200px) {
            .container {
                grid-template-columns: 1fr; /* ให้เหลือคอลัมน์เดียว */
            }
            .image-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 992px) {
            .image-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .card {
                padding: 15px;
            }
            body {
                padding: 10px;
                padding-top: 60px;
            }
        }
        @media (max-width: 768px) {
            .image-grid {
                grid-template-columns: repeat(1, 1fr);
            }
            .card {
                padding: 10px;
            }
            .card-title {
                font-size: 18px;
            }
            .card-text {
                font-size: 14px;
            }
        }
        @media (max-width: 576px) {
            body {
                padding: 5px;
                padding-top: 50px;
            }
            .card {
                padding: 5px;
                text-align: center;
            }
            .image-card img {
                height: 150px;
            }
            .modal-content img {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- container แรก: แสดงข้อมูลบริษัท และข่าว -->
    <div class="container mt-4">
        <!-- รูปภาพของบริษัท (จัด text ให้อยู่ตรงกลางด้วย text-center) -->
        <div class="card text-center">
            <img src="images/background2.jpg" class="card-img-top" alt="ตัวอย่างรูปภาพ">
            <div class="card-body">
                <h5 class="card-title">บริษัท โชคยืนยงอุตสาหกรรม จำกัด</h5>
                <p class="card-text">
                    ประกอบธุรกิจประเภท รับซื้อหัวมันสำปะหลัง ผลิตและจำหน่ายแป้งมันสำปะหลัง ขายกากสด และเปลือกมันสำปะหลัง
                </p>
                <p class="card-text">
                    ที่อยู่ 100 หมู่ 5 ตำบลโป่งแดง อำเภอขามทะเลสอ จังหวัดนครราชสีมา 30280
                </p>
            </div>
        </div>

        <!-- ข่าววันนี้ (ดึงจาก Facebook) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">ข่าววันนี้</h5>
                <div id="fb-root"></div>
                <script async defer crossorigin="anonymous" 
                        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0">
                </script>
                <div class="fb-page" 
                     data-href="https://www.facebook.com/CHOKYUENYONGOfficial" 
                     data-tabs="timeline" data-width="500" data-height="500" 
                     data-small-header="false" data-adapt-container-width="true" 
                     data-hide-cover="false" data-show-facepile="true">
                </div>
            </div>
        </div>
    </div>

    <!-- container ที่สอง: แสดงแกลเลอรีรูปภาพ (รายงานการทำงานแผนกไอที) -->
    <div class="container mt-4">
        <div class="card gallery-card">
            <div class="card-body">
                <h5 class="text-center">การทำงานของแผนกไอที</h5>
                <div class="image-grid">
                    <?php while ($row = $result->fetch_assoc()) { 
                        $images = explode(',', $row['image']); // แยกรายชื่อไฟล์รูปภาพ (กรณีอัปโหลดหลายไฟล์)
                        $totalImages = count($images);
                        if ($totalImages > 0) {
                            $firstImage = trim($images[0]); // เอาเฉพาะรูปแรกของรายการมาแสดง ?>
                            <div class="image-card">
                                <img src="uploads/<?php echo $firstImage; ?>" 
                                     alt="ภาพ <?php echo $row['id']; ?>" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal"
                                     data-images="<?php echo implode(',', $images); ?>" 
                                     data-title="<?php echo $row['job_name']; ?>">
                                <p><?php echo $row['job_name']; ?></p>
                                <p>อุปกรณ์ที่เบิก : <?php echo $row['equipment']; ?></p>
                            </div>
                        <?php } 
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- แสดงปุ่มแบ่งหน้า -->
    <?php if ($totalPages > 1) { ?>
    <nav aria-label="Page navigation" class="mt-4">
      <ul class="pagination justify-content-center">
        <!-- ปุ่มก่อนหน้า -->
        <?php if ($page > 1) { ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
        <?php } else { ?>
          <li class="page-item disabled">
            <span class="page-link" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </span>
          </li>
        <?php } ?>

        <!-- ลูปแสดงหมายเลขหน้า -->
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
          <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php } ?>

        <!-- ปุ่มถัดไป -->
        <?php if ($page < $totalPages) { ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        <?php } else { ?>
          <li class="page-item disabled">
            <span class="page-link" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </span>
          </li>
        <?php } ?>
      </ul>
    </nav>
    <?php } ?>

    <!-- Modal สำหรับแสดงรูปขยาย -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">ดูรูปภาพขยาย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="ขยายรูป" id="modalImage">
                    <p id="modalImageTitle"></p>
                    <p id="imageIndex"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="prevImage">ก่อนหน้า</button>
                    <button type="button" class="btn btn-primary" id="nextImage">ถัดไป</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        let currentImageIndex = 0;
        let imagesArray = [];

        // เปิด Modal และโหลดภาพทั้งหมดของรายการนั้นๆ
        document.querySelectorAll('.image-card img').forEach(img => {
            img.addEventListener('click', function () {
                imagesArray = this.getAttribute('data-images').split(',');
                currentImageIndex = 0;
                // แสดง Title ของรูป (ชื่อ job) ใน Modal
                document.getElementById('modalImageTitle').innerText = this.getAttribute('data-title');
                updateModal();
            });
        });

        function updateModal() {
            if (imagesArray.length > 0) {
                document.getElementById('modalImage').setAttribute('src', "uploads/" + imagesArray[currentImageIndex]);
                document.getElementById('imageIndex').innerText = `ภาพที่ ${currentImageIndex + 1} / ${imagesArray.length}`;
            }
        }

        // ฟังก์ชันไปยังรูปถัดไป
        document.getElementById('nextImage').addEventListener('click', function () {
            if (currentImageIndex < imagesArray.length - 1) {
                currentImageIndex++;
                updateModal();
            }
        });

        // ฟังก์ชันไปยังรูปก่อนหน้า
        document.getElementById('prevImage').addEventListener('click', function () {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                updateModal();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
