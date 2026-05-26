<?php
session_start();
require 'db.php';

// รับค่าการค้นหาและหน้าปัจจุบันจาก URL
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { 
    $page = 1; 
}
$rowsPerPage = 5;
$offset = ($page - 1) * $rowsPerPage;

// นับจำนวนแถวทั้งหมด (สำหรับแบ่งหน้า)
$countSql = "SELECT COUNT(*) as total FROM it_inventory";
if (!empty($searchTerm)) {
    $escapedSearch = $conn->real_escape_string($searchTerm);
    $countSql .= " WHERE item_name LIKE '%$escapedSearch%' OR model LIKE '%$escapedSearch%'";
}
$countResult = $conn->query($countSql);
$rowCount = ($countResult && $row = $countResult->fetch_assoc()) ? $row['total'] : 0;
$totalPages = ceil($rowCount / $rowsPerPage);

// สร้างคำสั่ง SQL สำหรับดึงข้อมูล พร้อมเงื่อนไขค้นหาและแบ่งหน้า
$sql = "SELECT * FROM it_inventory";
if (!empty($searchTerm)) {
    $escapedSearch = $conn->real_escape_string($searchTerm);
    $sql .= " WHERE item_name LIKE '%$escapedSearch%' OR model LIKE '%$escapedSearch%'";
}
$sql .= " ORDER BY item_name ASC LIMIT $offset, $rowsPerPage";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลังอุปกรณ์ไอที</title>
    <style>
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        /* จัดตำแหน่งปุ่มเพิ่มอุปกรณ์ให้อยู่ตรงกลาง */
        .button-container {
            text-align: center;
            margin-bottom: 20px;
        }
        /* สไตล์สำหรับปุ่มทั่วไป */
        a.add-btn, button, .search-form button {
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        /* ปุ่มเพิ่มอุปกรณ์ */
        a.add-btn {
            background-color: rgb(32, 143, 58);
            color: white;
        }
        a.add-btn:hover {
            background-color: #388E3C;
        }
        /* ปุ่มใน quantity-controls */
        .quantity-controls button {
            background-color: #002c5c;
            color: white;
        }
        .quantity-controls button:hover {
            background-color: #004080;
        }
        /* ปุ่มลบ */
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 7px 12px;
            font-size: 14px;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        /* สไตล์ฟอร์มค้นหา */
        .search-form {
            margin-bottom: 15px;
            text-align: center;
        }
        .search-form input[type="text"] {
            padding: 8px;
            font-size: 14px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-form button {
            background-color: #002c5c;
            color: white;
            margin-left: 5px;
        }
        .search-form button.reset-btn {
            background-color: #dc3545;
            font-size: 12px;
        }
        .search-form button.reset-btn:hover {
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
            margin-top: 10px;
        }
        thead {
            background-color: #002c5c;
            color: white;
            font-weight: bold;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
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
            background-color: #f9f9f9;
        }
        /* สไตล์สำหรับแบ่งหน้า */
        .pagination {
            margin-top: 15px;
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

        .quantity-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;       /* ลดช่องว่างเพื่อกันการตกบรรทัด */
    flex-wrap: nowrap; /* บังคับไม่ให้มีการขึ้นบรรทัดใหม่ */
    white-space: nowrap; /* ป้องกันข้อความห่อบรรทัด */
}

.quantity-controls button {
    padding: 6px 10px; /* ลดขนาดปุ่มตามต้องการ */
    font-size: 14px;
    min-width: 32px;   /* กำหนดความกว้างขั้นต่ำเพื่อไม่ให้ปุ่มแคบเกินไป */
}

.quantity-controls span {
    min-width: 30px;   /* กำหนดความกว้างขั้นต่ำของช่องตัวเลข */
    font-size: 14px;
}

        /* Responsive adjustments */
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
            .quantity-controls {
                gap: 8px;
            }
            a.add-btn, .quantity-controls button, .search-form button {
                padding: 8px 10px;
                font-size: 12px;
            }
            .delete-btn {
                padding: 5px 10px;
                font-size: 12px;
            }
            .search-form input[type="text"] {
                width: 150px;
                font-size: 12px;
            }
            .pagination a {
                padding: 6px 10px;
                font-size: 12px;
            }
            /* จัดกลางปุ่มในส่วนของปุ่มเพิ่มและฟอร์มค้นหา */
            .button-container, .search-form {
                text-align: center;
            }
            .quantity-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 3px;       /* ลดช่องว่างเพื่อกันการตกบรรทัด */
    flex-wrap: nowrap; /* บังคับไม่ให้มีการขึ้นบรรทัดใหม่ */
    white-space: nowrap; /* ป้องกันข้อความห่อบรรทัด */
}

.quantity-controls button {
    padding: 6px 10px; /* ลดขนาดปุ่มตามต้องการ */
    font-size: 12px;
    min-width: 22px;   /* กำหนดความกว้างขั้นต่ำเพื่อไม่ให้ปุ่มแคบเกินไป */
}

.quantity-controls span {
    min-width: 20px;   /* กำหนดความกว้างขั้นต่ำของช่องตัวเลข */
    font-size: 12px;
}
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>จัดการอุปกรณ์แผนกไอที</h1>
    <!-- ปุ่มเพิ่มอุปกรณ์อยู่ตรงกลาง -->
    <div class="button-container">
        <a href="add_inventory.php" class="add-btn">เพิ่มอุปกรณ์</a>
    </div>
    
    <!-- ฟอร์มค้นหา -->
    <form class="search-form" method="GET" action="">
        <input type="text" name="search" placeholder="ค้นหาอุปกรณ์..." value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">ค้นหา</button>
        <button type="button" class="reset-btn" onclick="window.location.href='<?= basename($_SERVER['PHP_SELF']) ?>'">รีเซ็ต</button>
    </form>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ชื่ออุปกรณ์</th>
                    <th>รุ่น</th>
                    <th>จำนวน</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['item_name']) ?></td>
                            <td><?= htmlspecialchars($row['model']) ?></td>
                            <td>
                                <div class="quantity-controls">
                                    <button onclick="updateQuantity(<?= $row['id'] ?>, -1)">-</button>
                                    <span id="quantity_<?= $row['id'] ?>"><?= $row['quantity'] ?></span>
                                    <button onclick="updateQuantity(<?= $row['id'] ?>, 1)">+</button>
                                </div>
                            </td>
                            <td>
                                <form method="post" action="delete_inventory.php" onsubmit="return confirm('คุณต้องการลบอุปกรณ์นี้หรือไม่?');">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="delete-btn">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">ไม่มีข้อมูล</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- การแบ่งหน้า -->
    <?php if($totalPages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $page - 1 ?>">ก่อนหน้า</a>
        <?php endif; ?>
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if($page < $totalPages): ?>
            <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $page + 1 ?>">ถัดไป</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
</div>

<script>
    function updateQuantity(id, change) {
        let quantitySpan = document.getElementById('quantity_' + id);
        let currentQuantity = parseInt(quantitySpan.innerText);
        let newQuantity = currentQuantity + change;

        if (newQuantity >= 0) {
            fetch('update_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id + '&quantity=' + newQuantity,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    quantitySpan.innerText = newQuantity;
                } else {
                    alert('เกิดข้อผิดพลาดในการอัปเดตจำนวน');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการอัปเดตจำนวน');
            });
        }
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
