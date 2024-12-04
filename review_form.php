<?php
include 'config.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Kiểm tra nếu sinh viên đã tham gia khóa học
$query = "
    SELECT * 
    FROM enrollments 
    WHERE student_id = '$student_id' AND course_id = '$course_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Bạn không có quyền đánh giá khóa học này.");
}

// Xử lý form đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    if ($rating >= 1 && $rating <= 5) {
        $insert_query = "
            INSERT INTO reviews (course_id, student_id, rating, comment) 
            VALUES ('$course_id', '$student_id', '$rating', '$comment')";
        mysqli_query($conn, $insert_query);
        header("Location: student_course.php");
        exit();
    } else {
        $error = "Vui lòng chọn số sao hợp lệ!";
    }
}
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
    <h2 class="text-center mb-4">Đánh giá khóa học</h2>

    <form action="" method="post">
        <div class="mb-4">
            <label for="rating" class="form-label fs-3">Đánh giá sao:</label>
            <div class="star-rating">
                <input type="radio" id="5-stars" name="rating" value="5">
                <label for="5-stars" class="star">&#9733;</label>
                <input type="radio" id="4-stars" name="rating" value="4">
                <label for="4-stars" class="star">&#9733;</label>
                <input type="radio" id="3-stars" name="rating" value="3">
                <label for="3-stars" class="star">&#9733;</label>
                <input type="radio" id="2-stars" name="rating" value="2">
                <label for="2-stars" class="star">&#9733;</label>
                <input type="radio" id="1-star" name="rating" value="1">
                <label for="1-star" class="star">&#9733;</label>
            </div>
        </div>
        <div class="mb-4">
            <label for="comment" class="form-label fs-3">Nhận xét:</label>
            <textarea name="comment" id="comment" class="form-control" rows="5" placeholder="Nhập nhận xét của bạn..."></textarea>
        </div>
        <?php if (isset($error)) { ?>
            <div class="text-danger mb-3"><?php echo $error; ?></div>
        <?php } ?>
        <button type="submit" class="btn btn-primary fs-4">Gửi đánh giá</button>
    </form>
</div>

<style>
.star-rating {
    display: flex;
    justify-content: center;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 50px;
    color: #ccc;
    cursor: pointer;
}

.star-rating input:checked ~ label {
    color: #ffc700;
}

.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffc700;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>
</body>
</html>
