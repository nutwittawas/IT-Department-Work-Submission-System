<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $model = $_POST['model'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO it_inventory (item_name, model, quantity) VALUES ('$item_name', '$model', $quantity)";

    if ($conn->query($sql) === TRUE) {
        header("Location: inventory.php"); // กลับไปหน้า inventory
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มอุปกรณ์</title>
    <style>
             /* รีเซ็ตค่าเริ่มต้น */
             * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            background-image: url('../images/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color:rgb(0, 0, 0);
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
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        input[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 15px;
            }

            input[type="text"],
            input[type="number"] {
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>เพิ่มอุปกรณ์</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="item_name">ชื่ออุปกรณ์:</label>
        <input type="text" id="item_name" name="item_name" required>

        <label for="model">รุ่น:</label>
        <input type="text" id="model" name="model">

        <label for="quantity">จำนวน:</label>
        <input type="number" id="quantity" name="quantity" required>

        <input type="submit" value="เพิ่มอุปกรณ์">
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
