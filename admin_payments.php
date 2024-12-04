<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Xử lý yêu cầu cập nhật trạng thái từ AJAX
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $update_query = mysqli_query($conn, "UPDATE `payments` SET payment_status = '$status' WHERE id = '$id'") or die('Query failed');

    if ($update_query) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hóa đơn thanh toán</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        th, td {
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="show-payments">
    <h1 class="title">Quản lý hóa đơn thanh toán</h1>
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Học viên</th>
                    <th>ID Khóa học</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th>Ngày thanh toán</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_payments = mysqli_query($conn, "SELECT * FROM `payments`") or die('Query failed');
                if (mysqli_num_rows($select_payments) > 0) {
                    while ($payment = mysqli_fetch_assoc($select_payments)) {
                ?>
                        <tr>
                            <td><?php echo $payment['id']; ?></td>
                            <td><?php echo $payment['student_id']; ?></td>
                            <td><?php echo $payment['course_id']; ?></td>
                            <td><?php echo number_format($payment['amount'], 0, ',', '.'); ?> VND</td>
                            <td><?php echo $payment['payment_method']; ?></td>
                            <td><?php echo $payment['payment_date']; ?></td>
                            <td>
                                <select onchange="updateStatus(<?php echo $payment['id']; ?>, this.value)">
                                    <option value="Chưa thanh toán" <?php echo $payment['payment_status'] == 'Chưa thanh toán' ? 'selected' : ''; ?>>Chưa thanh toán</option>
                                    <option value="Đã thanh toán" <?php echo $payment['payment_status'] == 'Đã thanh toán' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                    <option value="Đang xử lý" <?php echo $payment['payment_status'] == 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                                </select>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center">Chưa có hóa đơn nào.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateStatus(id, status) {
        $.ajax({
            url: 'admin_payments.php',
            type: 'POST',
            data: {
                update_status: true,
                id: id,
                status: status
            },
            success: function(response) {
                if (response === 'success') {
                    alert('Cập nhật trạng thái thành công!');
                } else {
                    alert('Cập nhật trạng thái thất bại!');
                }
            },
            error: function() {
                alert('Đã xảy ra lỗi trong quá trình cập nhật!');
            }
        });
    }
</script>


</body>
</html>
