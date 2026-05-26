<?php
session_start();
require 'db.php'; // ใช้ db.php เพื่อเชื่อมต่อฐานข้อมูล

// กำหนดจำนวนแถวต่อหน้า
$limit = 5;

// รับค่า page จาก URL (ถ้าไม่มีค่า ให้กำหนดเป็น 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// รับค่าจากฟอร์มค้นหา
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_date = isset($_GET['search_date']) ? $conn->real_escape_string($_GET['search_date']) : '';

// สร้างเงื่อนไขการค้นหา
$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND job_name LIKE '%$search%'";
}
if (!empty($search_date)) {
    $where .= " AND created_date = '$search_date'";
}

// คำนวณ offset สำหรับ LIMIT
$offset = ($page - 1) * $limit;

// ดึงข้อมูลจากตาราง job
$sql = "SELECT * FROM job $where ORDER BY created_date DESC, created_time DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// ดึงจำนวนข้อมูลทั้งหมดสำหรับ pagination
$sql_total = "SELECT COUNT(*) as total FROM job $where";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_records = $row_total['total'];
$total_pages = ceil($total_records / $limit);

// สร้าง query string สำหรับเก็บค่าการค้นหาในลิงค์ pagination
$queryString = '';
$params = array();
if (!empty($search)) {
    $params['search'] = $search;
}
if (!empty($search_date)) {
    $params['search_date'] = $search_date;
}
if (!empty($params)) {
    $queryString = '&' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางติดตามงานแผนกไอที</title>
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* โซนฟอร์ม: ให้อยู่บรรทัดเดียวบนจอใหญ่, แต่จอเล็กจะขึ้นคนละบรรทัด */
        .form-row {
            display: flex;
            flex-wrap: wrap;  /* เมื่อพื้นที่ไม่พอ จะขึ้นบรรทัดใหม่อัตโนมัติ */
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-bottom: 20px;
        }
        .form-row form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }
        .form-row label {
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap; /* ป้องกันคำแตกบรรทัด */
        }
        .form-row input[type="text"],
        .form-row input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-row button,
        .form-row a.export-btn {
            background-color: rgb(32, 143, 58);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            font-size: 14px;
            white-space: nowrap;
        }
        .form-row button:hover,
        .form-row a.export-btn:hover {
            background-color: #218838;
        }
        /* ปุ่มรีเซ็ต */
        .form-row a.reset-btn {
            background-color: #dc3545;
            padding: 5px 10px; 
            font-size: 12px;
        }
        .form-row a.reset-btn:hover {
            background-color: #c82333;
        }

        /* ตาราง */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        thead {
            background-color: #002c5c;
            color: white;
        }
        thead th {
            padding: 12px;
            text-align: center;  /* หัวตารางกึ่งกลาง */
        }
        tbody td {
            padding: 12px;
            text-align: left;    /* ข้อมูลชิดซ้าย */
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
            font-size: 14px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* รูปภาพ */
        .thumbnail1 {
            width: 50px; height: 50px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: transform 0.3s ease-in-out;
        }
        .thumbnail1:hover {
            transform: scale(1.2);
        }

        /* ปุ่มลบ */
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 7px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Pagination */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a, .pagination strong {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
        }
        .pagination a:hover {
            background-color: #f4f4f4;
        }
        /* Styles for Lightbox */
.lightbox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* กรอบรูปใน Lightbox */
.lightbox img {
    max-width: 90%;
    max-height: 90%;
    border: 5px solid #fff; /* กรอบสีขาวรอบรูป */
    border-radius: 10px; /* มุมโค้งมน */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.6); /* เงารอบๆ รูป */
}


/* ปุ่ม prev และ next สำหรับหน้าจอปกติ */
.lightbox .prev, .lightbox .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;  /* ขนาดลูกศร */
    color: white;
    background: rgba(0, 0, 0, 0.6); /* สีพื้นหลังเข้ม */
    border: none;
    padding: 10px 15px;  /* ขนาด padding */
    border-radius: 5px;  /* ปุ่มสีเหลี่ยมมุมโค้ง */
    cursor: pointer;
    transition: background-color 0.3s;
    z-index: 10000;
}

/* เมื่อ hover ปุ่มลูกศร */
.lightbox .prev:hover, .lightbox .next:hover {
    background-color: rgba(0, 0, 0, 0.8);  /* สีเข้มขึ้นเมื่อ hover */
}

/* ปุ่ม prev อยู่ทางซ้าย, ปุ่ม next อยู่ทางขวา */
.lightbox .prev {
    left: 20px;
}

.lightbox .next {
    right: 20px;
}


        /* ปรับลดขนาดสำหรับหน้าจอเล็ก */
        @media (max-width: 480px) {
            /* ซ่อนคอลัมน์ "อุปกรณ์ที่เบิก" และ "วันที่" */
            table th:nth-child(2), table td:nth-child(2),
            table th:nth-child(5), table td:nth-child(5) {
                display: none;
            }

            .form-row label,
            .form-row input[type="text"],
            .form-row input[type="date"],
            .form-row button,
            .form-row a.export-btn {
                font-size: 12px;
            }
            .form-row input[type="text"],
            .form-row input[type="date"] {
                padding: 4px;
                width: 80px; /* บีบไม่ให้ยาวเกินไป */
            }
            .form-row button,
            .form-row a.export-btn {
                padding: 4px 6px;
            }
            .form-row a.reset-btn {
                padding: 3px 5px;
                font-size: 10px;
            }
            table {
                font-size: 12px;
            }
            tbody td {
                padding: 8px;
            }
            .thumbnail1 {
                width: 35px; height: 35px;
            }
            .delete-btn {
                padding: 5px 8px;
                font-size: 12px;
            }
            .lightbox .prev, .lightbox .next {
        font-size: 16px;  /* ลดขนาดลูกศรลง */
        padding: 8px 12px;  /* ลดขนาด padding */
    }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>ตารางการส่งงานแผนกไอที</h1>

    <!-- โซนฟอร์มค้นหา + ออกรายงาน -->
    <div class="form-row">
        <!-- ฟอร์มค้นหา -->
        <form action="" method="GET">
            <label for="search">ชื่องาน:</label>
            <input type="text" name="search" id="search" placeholder="เช่น ติดตั้งโปรแกรม" 
                   value="<?= htmlspecialchars($search) ?>">
            
            <label for="search_date">วัน:</label>
            <input type="date" name="search_date" id="search_date" 
                   value="<?= htmlspecialchars($search_date) ?>">

            <button type="submit">ค้นหา</button>
            <a href="admin.php" class="export-btn reset-btn">รีเซ็ต</a>
        </form>

        <!-- ฟอร์มออก PDF -->
        <form action="export_pdf.php" method="GET">
            <label style="white-space: nowrap; font-weight:bold;" for="start_date_pdf">
                ออกรายงาน : จาก
            </label>
            <input type="date" name="start_date" id="start_date_pdf" required>
            
            <label for="end_date_pdf">ถึง</label>
            <input type="date" name="end_date" id="end_date_pdf" required>
            
            <button type="submit" class="export-btn">PDF</button>
        </form>
    </div>
    
    <!-- ตารางข้อมูล -->
    <table>
        <thead>
            <tr>
                <th>ชื่องาน</th>
                <th>อุปกรณ์ที่เบิก</th>
                <th>ชื่อผู้ส่ง</th>
                <th>รูปภาพ</th>
                <th>วันที่</th>
                <th>ลบ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['job_name'] ?? 'ไม่มีข้อมูล') ?></td>
                        <td><?= htmlspecialchars($row['equipment'] ?? 'ไม่มีข้อมูล') ?></td>
                        <td><?= htmlspecialchars($row['sender_name'] ?? 'ไม่ระบุ') ?></td>
                        <td>
    <?php 
    if (!empty($row['image'])):
        $images = explode(',', $row['image']);
        foreach ($images as $image):
            $image = trim($image);
            if (!empty($image)): ?>
                <img src="../uploads/<?= htmlspecialchars($image) ?>" 
                     alt="Image" class="thumbnail1">
            <?php endif;
        endforeach;
    else:
        echo 'ไม่มีรูป';
    endif;
    ?>
</td>

                        <td><?= date("d/m/Y", strtotime($row['created_date'])) ?></td>
                        <td>
                            <form action="delete.php" method="POST" 
                                  onsubmit="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit" class="delete-btn">ลบ</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">ไม่มีข้อมูล</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- แสดงลิงค์ Pagination -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 . $queryString ?>">ก่อนหน้า</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if($i == $page): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?page=<?= $i . $queryString ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page+1 . $queryString ?>">ถัดไป</a>
        <?php endif; ?>
    </div>
</div>
<!-- Lightbox container -->
<div class="lightbox" id="lightbox">
    <span class="prev" onclick="moveSlide(-1)">&#10094;</span>
    <img id="lightbox-img" src="" alt="Image">
    <span class="next" onclick="moveSlide(1)">&#10095;</span>
</div>

<script>
// Open the lightbox when an image is clicked
let currentSlide = 0;
function openLightbox(img) {
    const images = document.querySelectorAll('.thumbnail1');
    currentSlide = Array.from(images).indexOf(img);
    document.getElementById('lightbox-img').src = img.src;
    document.getElementById('lightbox').style.display = 'flex';
}

// Close the lightbox when clicking outside the image
document.getElementById('lightbox').onclick = function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
};

// Navigate through the images in the lightbox
function moveSlide(n) {
    const images = document.querySelectorAll('.thumbnail1');
    currentSlide = (currentSlide + n + images.length) % images.length;
    document.getElementById('lightbox-img').src = images[currentSlide].src;
}

// Add event listener to open images in lightbox
document.querySelectorAll('.thumbnail1').forEach(image => {
    image.addEventListener('click', () => openLightbox(image));
});
</script>

</body>
</html>

<?php
$conn->close();
?>
