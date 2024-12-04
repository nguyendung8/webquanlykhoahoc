<?php
include 'config.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

$query = "
    SELECT 
        payments.id AS payment_id, 
        courses.title AS course_title, 
        payments.amount, 
        payments.payment_method, 
        payments.payment_date 
    FROM payments
    JOIN courses ON payments.course_id = courses.id
    WHERE payments.student_id = '$student_id' AND payments.amount > 0
    ORDER BY payments.payment_date DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý hóa đơn</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/new-style.css">
</head>
<body>
<?php include 'student_header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Quản lý hóa đơn thanh toán</h2>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Tên khóa học</th>
                    <th>Số tiền</th>
                    <th>Phương thức thanh toán</th>
                    <th>Ngày thanh toán</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                        <td><?php echo number_format($row['amount'], 2, ',', '.') . ' VNĐ'; ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo date('d-m-Y H:i:s', strtotime($row['payment_date'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center fs-4">Bạn chưa có hóa đơn thanh toán nào!</p>
    <?php } ?>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>
</body>
</html>
