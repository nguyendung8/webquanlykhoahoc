<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Tổng số người dùng
$total_users = mysqli_query($conn, "SELECT COUNT(id) AS total FROM `users`");
$total_users = mysqli_fetch_assoc($total_users)['total'];

// Tổng số khóa học
$total_courses = mysqli_query($conn, "SELECT COUNT(id) AS total FROM `courses`");
$total_courses = mysqli_fetch_assoc($total_courses)['total'];

// Tổng số giảng viên
$total_teachers = mysqli_query($conn, "SELECT COUNT(id) AS total FROM `users` WHERE role = 'teacher'");
$total_teachers = mysqli_fetch_assoc($total_teachers)['total'];

// Tổng số sinh viên
$total_students = mysqli_query($conn, "SELECT COUNT(id) AS total FROM `users` WHERE role = 'student'");
$total_students = mysqli_fetch_assoc($total_students)['total'];

// Tổng doanh thu
$total_revenue = mysqli_query($conn, "SELECT SUM(amount) AS total FROM `payments` WHERE payment_status = 'Đã thanh toán'");
$total_revenue = mysqli_fetch_assoc($total_revenue)['total'];

// Doanh thu theo từng khóa học
$revenue_per_course = mysqli_query($conn, "SELECT courses.title, SUM(payments.amount) AS total_revenue
    FROM `payments`
    JOIN `courses` ON payments.course_id = courses.id
    WHERE payments.payment_status = 'Đã thanh toán'
    GROUP BY courses.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thống kê</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        th, td {
            text-align: center;
            font-size: 18px;
        }
        .card {
            margin-bottom: 20px;
        }
        .chart-container {
            width: 80%;
            margin: auto;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="container mt-5">
    <h1 class="title text-center">Thống kê quản trị viên</h1>
    <div class="row">
        <!-- Thẻ thống kê tổng số người dùng -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng số người dùng</h5>
                    <p class="card-text"><?php echo $total_users; ?> người</p>
                </div>
            </div>
        </div>

        <!-- Thẻ thống kê tổng số khóa học -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng số khóa học</h5>
                    <p class="card-text"><?php echo $total_courses; ?> khóa học</p>
                </div>
            </div>
        </div>

        <!-- Thẻ thống kê tổng số giảng viên -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng số giảng viên</h5>
                    <p class="card-text"><?php echo $total_teachers; ?> giảng viên</p>
                </div>
            </div>
        </div>

        <!-- Thẻ thống kê tổng số sinh viên -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng số sinh viên</h5>
                    <p class="card-text"><?php echo $total_students; ?> sinh viên</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu tổng -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Tổng doanh thu</h5>
            <p class="card-text"><?php echo number_format($total_revenue, 0, ',', '.') . " VND"; ?></p>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo khóa học -->
    <div class="chart-container mt-4">
        <h2 class="text-center fs-1">Doanh thu theo khóa học</h2>
        <canvas id="revenueChart"></canvas>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Lấy dữ liệu doanh thu từ PHP
    <?php
    $courses = [];
    $revenues = [];
    while ($row = mysqli_fetch_assoc($revenue_per_course)) {
        $courses[] = $row['title'];
        $revenues[] = (float) $row['total_revenue'];
    }
    ?>

    // Tạo biểu đồ doanh thu
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($courses); ?>,
            datasets: [{
                label: 'Doanh thu (VND)',
                data: <?php echo json_encode($revenues); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' VND';
                        }
                    }
                }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
