<?php
require 'db.php'; // ใช้ db.php เพื่อเชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง test
$sql = "SELECT * FROM test";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบหลังบ้านดูผลงานแผนกไอที</title>
    <link rel="stylesheet" href="styledp.css">
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Lightbox styling */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .lightbox.active {
            display: flex;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 5px;
        }

        .lightbox .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #fff;
            color: #333;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        .lightbox .lightbox-close:hover {
            background: #333;
            color: #fff;
        }

        .lightbox .lightbox-controls {
            position: absolute;
            top: 50%;
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0 20px;
            transform: translateY(-50%);
        }

        .lightbox-controls button {
            background: none;
            border: none;
            color: #fff;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .lightbox-controls button:hover {
            color: #ddd;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
    <h1>ทดสอบหลังบ้านดูผลงานแผนกไอที</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่องาน</th>
                <th>อุปกรณ์ที่เบิก</th>
                <th>ชื่อผู้ส่งงาน</th>
                <th>สถานที่</th>
                <th>Images</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['job_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['equipment']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['sender_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['location']) . '</td>';

                    // แสดงรูปภาพ
                    echo '<td>';
                    if (!empty($row['image'])) {
                        $images = explode(',', $row['image']);
                        foreach ($images as $key => $image) {
                            echo '<img src="uploads/' . htmlspecialchars($image) . '" alt="Image" class="thumbnail" data-index="' . $key . '" data-images="' . htmlspecialchars(implode(',', $images)) . '">';
                        }
                    }
                    echo '</td>';

                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6">No data available.</td></tr>';
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <a href="#" class="lightbox-close">×</a>
        <img src="" alt="Large Image" id="lightbox-img">
        <div class="lightbox-controls">
            <button id="prev-btn">&lt;</button>
            <button id="next-btn">&gt;</button>
        </div>
    </div>

    <script>
        const thumbnails = document.querySelectorAll('.thumbnail');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        let currentImageIndex = 0;
        let currentImageList = [];

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', (e) => {
                const images = e.target.dataset.images.split(',');
                currentImageIndex = parseInt(e.target.dataset.index);
                currentImageList = images;

                lightboxImg.src = `uploads/${images[currentImageIndex]}`;
                lightbox.classList.add('active');
            });
        });

        prevBtn.addEventListener('click', () => {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                lightboxImg.src = `uploads/${currentImageList[currentImageIndex]}`;
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentImageIndex < currentImageList.length - 1) {
                currentImageIndex++;
                lightboxImg.src = `uploads/${currentImageList[currentImageIndex]}`;
            }
        });

        document.querySelector('.lightbox-close').addEventListener('click', (e) => {
            e.preventDefault();
            lightbox.classList.remove('active');
        });

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
            }
        });
    </script>
</body>
</html>
