<?php
include 'config.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Kiểm tra nếu sinh viên đã tham gia khóa học này
$enrollment_check_query = "
    SELECT * 
    FROM enrollments 
    WHERE student_id = '$student_id' AND course_id = '$course_id'";
$enrollment_result = mysqli_query($conn, $enrollment_check_query);

if (mysqli_num_rows($enrollment_result) == 0) {
    die("Bạn không có quyền truy cập khóa học này.");
}

// Lấy danh sách bài giảng của khóa học
$query = "
    SELECT * 
    FROM lessons 
    WHERE course_id = '$course_id' 
    ORDER BY sort_order ASC";
$result = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý bài giảng</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/new-style.css">
   <style>
        .progress {
            height: 24px !important;
        }
       .progress-bar {
           background-color: #28a745;
           text-align: center;
           color: #fff;
           line-height: 24px;
           font-size: 14px;
       }
   </style>
</head>
<body>
<?php include 'student_header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Danh sách bài giảng</h2>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Tiêu đề</th>
                    <th>Tài liệu</th>
                    <th>Video</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <?php if (!empty($row['materials'])) { ?>
                                <a href="<?php echo htmlspecialchars($row['materials']); ?>" 
                                   target="_blank" 
                                   class="btn fs-4 btn-secondary">
                                   Tải tài liệu
                                </a>
                            <?php } else { ?>
                                Không có tài liệu
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($row['video_url'])) { ?>
                                <a href="<?php echo htmlspecialchars($row['video_url']); ?>" 
                                   target="_blank" 
                                   class="btn fs-4 btn-primary">
                                   Xem video
                                </a>
                            <?php } else { ?>
                                Không có video
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($row['is_completed'] == 1) { ?>
                                <span class="text-success">Đã hoàn thành</span>
                            <?php } else { ?>
                                <button 
                                    class="btn fs-4 btn-success complete-lesson" 
                                    data-lesson-id="<?php echo $row['id']; ?>" 
                                    data-course-id="<?php echo $course_id; ?>">
                                    Hoàn thành
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center fs-2">Chưa có bài giảng nào!</p>
    <?php } ?>
</div>

<script>
$(document).ready(function () {
    $(".complete-lesson").click(function () {
        let lessonId = $(this).data("lesson-id");
        let courseId = $(this).data("course-id");
        let button = $(this);

        $.ajax({
            url: "update_progress.php",
            method: "POST",
            data: {
                lesson_id: lessonId,
                course_id: courseId
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    button.replaceWith('<span class="text-success">Đã hoàn thành</span>');
                } else {
                    alert("Đã xảy ra lỗi, vui lòng thử lại!");
                }
            },
            error: function () {
                alert("Đã xảy ra lỗi, vui lòng thử lại!");
            }
        });
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>
</body>
</html>
