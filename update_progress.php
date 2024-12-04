<?php
include 'config.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["success" => false, "message" => "Chưa đăng nhập."]);
    exit();
}

$student_id = $_SESSION['student_id'];
$lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($lesson_id === 0 || $course_id === 0) {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ."]);
    exit();
}

// Lấy tổng số bài giảng của khóa học
$total_lessons_query = "
    SELECT COUNT(*) AS total 
    FROM lessons 
    WHERE course_id = '$course_id'";
$total_lessons_result = mysqli_query($conn, $total_lessons_query);
$total_lessons = mysqli_fetch_assoc($total_lessons_result)['total'];

// Lấy số bài giảng đã hoàn thành
$enrollment_query = "
    SELECT progress 
    FROM enrollments 
    WHERE student_id = '$student_id' AND course_id = '$course_id'";
$enrollment_result = mysqli_query($conn, $enrollment_query);
$current_progress = mysqli_fetch_assoc($enrollment_result)['progress'];

// Tính toán mức độ hoàn thành mới
$new_progress = $current_progress + (100 / $total_lessons);

// Cập nhật tiến độ vào bảng enrollments
$update_progress_query = "
    UPDATE enrollments 
    SET progress = GREATEST(LEAST('$new_progress', 100), 0)
    WHERE student_id = '$student_id' AND course_id = '$course_id'";
mysqli_query($conn, $update_progress_query);

// Cập nhật trạng thái hoàn thành bài giảng
$update_lesson_query = "
    UPDATE lessons 
    SET is_completed = 1 
    WHERE id = '$lesson_id'";
mysqli_query($conn, $update_lesson_query);

echo json_encode(["success" => true, "progress" => $new_progress]);
