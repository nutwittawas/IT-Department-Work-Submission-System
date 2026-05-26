<?php
require_once '../fpdf186/fpdf.php';
require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

class PDF extends FPDF
{
    function Header()
    {
        global $start_date, $end_date;

        $this->AddFont('angsa', '', 'angsa.php');
        $this->SetFont('angsa', '', 20);
        $this->Cell(0, 9, iconv('UTF-8', 'TIS-620', 'รายงานติดตามงานแผนกไอที'), 0, 1, 'C');
        
        $this->SetFont('angsa', '', 16);
        $this->Cell(0, 7, iconv('UTF-8', 'TIS-620', 'ช่วงวันที่: ' . date("d/m/Y", strtotime($start_date)) . ' - ' . date("d/m/Y", strtotime($end_date))), 0, 1, 'C');
        
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('angsa', '', 14);
        $this->Cell(0, 10, iconv('UTF-8', 'TIS-620', 'หน้าที่ ' . $this->PageNo() . ' จาก {nb}'), 0, 0, 'C');
    }
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if (empty($start_date) || empty($end_date)) {
    die("กรุณาเลือกช่วงวันที่ก่อนดาวน์โหลดรายงาน");
}

$sql = "SELECT job_name, image 
        FROM job 
        WHERE created_date BETWEEN ? AND ? 
        ORDER BY created_date DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("เกิดข้อผิดพลาดในการเตรียม SQL: " . $conn->error);
}
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFillColor(0, 44, 92);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('angsa', '', 18);
$pdf->Cell(100, 10, iconv('UTF-8', 'TIS-620', 'ชื่องาน'), 1, 0, 'C', true);
$pdf->Cell(95, 10, iconv('UTF-8', 'TIS-620', 'รูปภาพ'), 1, 1, 'C', true);

$pdf->SetFont('angsa', '', 16);
$pdf->SetTextColor(0, 0, 0);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rowHeight = 45;

        $pdf->Cell(100, $rowHeight, iconv('UTF-8', 'TIS-620', $row['job_name']), 1, 0, 'L');

        $images = explode(',', $row['image']);
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 5;

        if (!empty($row['image'])) {
            $imgX = $xPos + 5;
            $spacing = 30;
            foreach ($images as $index => $image) {
                $image = trim($image);
                $image_path = "../uploads/" . $image;

                if (file_exists($image_path) && !empty($image)) {
                    $pdf->Image($image_path, $imgX, $yPos, 27, 35);
                    $imgX += $spacing;
                }
            }
            $pdf->Cell(95, $rowHeight, '', 1, 1, 'C');
        } else {
            $pdf->Cell(95, $rowHeight, iconv('UTF-8', 'TIS-620', 'ไม่มีรูป'), 1, 1, 'C');
        }
    }
} else {
    $pdf->Cell(185, 10, iconv('UTF-8', 'TIS-620', 'ไม่มีข้อมูลในช่วงวันที่ที่เลือก'), 1, 1, 'C');
}

$stmt->close();
$conn->close();

$start_date_formatted = date("d-m-Y", strtotime($start_date));
$end_date_formatted = date("d-m-Y", strtotime($end_date));
$filename = "IT_Work_Report_{$start_date_formatted}_to_{$end_date_formatted}.pdf";

$pdf->Output('D', $filename);
exit();
?>