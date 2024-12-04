<?php
include 'config.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Lấy danh sách các khóa học đã tham gia với trạng thái thanh toán 'Đã thanh toán'
$query = "
    SELECT 
        courses.id AS course_id, 
        courses.title AS course_title, 
        courses.description, 
        enrollments.progress, 
        payments.payment_date 
    FROM enrollments
    JOIN courses ON enrollments.course_id = courses.id
    JOIN payments ON payments.course_id = courses.id 
                  AND payments.student_id = enrollments.student_id
    WHERE enrollments.student_id = '$student_id' 
          AND payments.payment_status = 'Đã thanh toán'
    ORDER BY payments.payment_date DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý khóa học</title>
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
    <h2 class="text-center mb-4">Danh sách khóa học đã tham gia</h2>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Tên khóa học</th>
                    <th>Tiến độ</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                        <td>
                            <div class="progress">
                                <div 
                                    class="progress-bar" 
                                    role="progressbar" 
                                    style="width: <?php echo $row['progress']; ?>%;"
                                    aria-valuenow="<?php echo $row['progress']; ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <?php echo $row['progress']; ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($row['progress'] == 100) { ?>
                                <a target="_blank" href="generate_certificate.php?course_id=<?php echo $row['course_id']; ?>" 
                                    class="btn fs-4 btn-success">
                                    Nhận chứng chỉ
                                </a>
                                <a href="review_form.php?course_id=<?php echo $row['course_id']; ?>" 
                                    class="btn fs-4 btn-warning">
                                    Đánh giá
                                </a>
                            <?php } else { ?>
                                <a href="student_lessons.php?course_id=<?php echo $row['course_id']; ?>" 
                                    class="btn fs-4 btn-primary">
                                    Vào học
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center fs-4">Bạn chưa tham gia khóa học nào!</p>
    <?php } ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>
</body>
</html>
