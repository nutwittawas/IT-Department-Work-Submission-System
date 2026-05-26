<?php
session_start();
require 'db.php'; // ใช้ db.php เพื่อเชื่อมต่อฐานข้อมูล

// รับค่าการค้นหาและหน้าปัจจุบันจาก URL
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$rowsPerPage = 5; // จำนวนแถวต่อหน้า
$offset = ($page - 1) * $rowsPerPage;

// นับจำนวนทั้งหมด (สำหรับแบ่งหน้า)
$countSql = "SELECT COUNT(*) as total FROM users";
if (!empty($searchTerm)) {
    $escapedSearch = $conn->real_escape_string($searchTerm);
    // ค้นหาใน fullname
    $countSql .= " WHERE fullname LIKE '%$escapedSearch%'";
}
$countResult = $conn->query($countSql);
$rowCount = ($countResult && $row = $countResult->fetch_assoc()) ? $row['total'] : 0;
$totalPages = ceil($rowCount / $rowsPerPage);

// สร้างคำสั่ง SQL ดึงข้อมูล พร้อมเงื่อนไขค้นหาและแบ่งหน้า
$sql = "SELECT * FROM users";
if (!empty($searchTerm)) {
    $escapedSearch = $conn->real_escape_string($searchTerm);
    $sql .= " WHERE fullname LIKE '%$escapedSearch%'";
}
$sql .= " ORDER BY id DESC LIMIT $offset, $rowsPerPage";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสมาชิก</title>
    <style>
        /* รีเซ็ตค่าเริ่มต้น */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: rgb(0, 0, 0);
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* จัดปุ่ม "เพิ่มสมาชิก" ให้อยู่ตรงกลาง */
        .button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            background-color: rgb(32, 143, 58);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* ฟอร์มค้นหา */
        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            padding: 8px;
            font-size: 14px;
            width: 220px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-form button {
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-left: 5px;
        }

        .search-form .search-btn {
            background-color: #002c5c;
            color: white;
        }

        .search-form .search-btn:hover {
            background-color: #004080;
        }

        .search-form .reset-btn {
            background-color: #dc3545;
            color: white;
            font-size: 12px;
        }

        .search-form .reset-btn:hover {
            background-color: #c82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
        }

        thead {
            background-color: #002c5c;
            color: white;
            font-weight: bold;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            text-align: center;
            font-size: 16px;
        }

        td {
            text-align: center;
            font-size: 14px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
            transition: transform 0.3s ease-in-out;
        }

        .thumbnail:hover {
            transform: scale(1.2);
        }

        .edit-btn {
            text-decoration: none;
            background-color: #ffc107;
            color: #212529;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #e0a800;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* สไตล์สำหรับแบ่งหน้า */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 3px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 4px;
            font-size: 14px;
        }
        .pagination a.active {
            background-color: #002c5c;
            color: white;
            border-color: #002c5c;
        }
        .pagination a:hover {
            background-color: #f0f0f0;
        }

        /* ทำให้หน้าตาดู responsive */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }
            h1 {
                font-size: 20px;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
            .thumbnail {
                width: 40px;
                height: 40px;
            }
            .edit-btn, .delete-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
            .search-form input[type="text"] {
                width: 150px;
                font-size: 12px;
            }
            .search-form button {
                font-size: 12px;
                padding: 6px 8px;
            }
            .pagination a {
                padding: 6px 10px;
                font-size: 12px;
            }
            .btn {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>จัดการสมาชิก</h1>

        <!-- ปุ่ม "เพิ่มสมาชิก" อยู่ตรงกลาง -->
        <div class="button-container">
            <a href="add_user.php" class="btn">เพิ่มสมาชิก</a>
        </div>

        <!-- ฟอร์มค้นหา -->
        <form class="search-form" method="GET" action="">
            <input type="text" name="search" placeholder="ค้นหาสมาชิก..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit" class="search-btn">ค้นหา</button>
            <button type="button" class="reset-btn" onclick="window.location.href='<?= basename($_SERVER['PHP_SELF']) ?>'">รีเซ็ต</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>รูปภาพ</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>สถานะ</th>
                    <th>แก้ไข</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="รูปโปรไฟล์" class="thumbnail">
                                <?php else: ?>
                                    <span>ไม่มีรูป</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= ($row['status'] == 1) ? 'หัวหน้า' : 'พนักงาน'; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="edit-btn">แก้ไข</a>
                            </td>
                            <td>
                                <form action="delete_user.php" method="POST" onsubmit="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?');">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="delete-btn">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">ไม่มีข้อมูล</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- การแบ่งหน้า -->
        <?php if($totalPages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $page - 1 ?>">ก่อนหน้า</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <a 
                        href="?search=<?= urlencode($searchTerm) ?>&page=<?= $i ?>" 
                        class="<?= ($i == $page) ? 'active' : '' ?>"
                    >
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                    <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $page + 1 ?>">ถัดไป</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>
