<?php
include 'config.php';
require('fpdf186/fpdf.php');
session_start();

if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Kiểm tra nếu sinh viên hoàn thành khóa học
$query = "
    SELECT enrollments.progress, courses.title AS course_title
    FROM enrollments
    JOIN courses ON enrollments.course_id = courses.id
    WHERE enrollments.student_id = '$student_id' AND enrollments.course_id = '$course_id'";
$result = mysqli_query($conn, $query);
$course = mysqli_fetch_assoc($result);

if (!$course || $course['progress'] < 100) {
    die("Bạn chưa hoàn thành khóa học này!");
}

// Lấy tên sinh viên
$student_query = "SELECT name FROM users WHERE id = '$student_id'";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

// Tạo chứng chỉ PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 24);

// Tiêu đề chứng chỉ
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 20, 'CHUNG NHAN HOAN THANH KHOA HOC', 0, 1, 'C');

// Nội dung
$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(10);
$pdf->Cell(0, 10, "Chung nhan sinh vien:", 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 15, strtoupper($student['name']), 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 18);
$pdf->Cell(0, 10, "Da hoan thanh khoa hoc:", 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 15, strtoupper($course['course_title']), 0, 1, 'C');
$pdf->Ln(20);

// Chữ ký
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(0, 10, "Ngay cap chung chi: " . date('d-m-Y'), 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Chung nhan duoc tu dong tao bang he thong.', 0, 1, 'C');

// Xuất file PDF
$pdf->Output('I', 'chung-chi-' . $course_id . '.pdf');
