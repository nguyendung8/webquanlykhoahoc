<?php
include 'config.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Lấy thông tin người dùng từ cơ sở dữ liệu
$query = "SELECT * FROM users WHERE id = '$student_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id'];
    $name = $user['name'];
    $email = $user['email'];
    $phone = $user['phone'];
    $address = $user['address'];
}

if (isset($_POST['update'])) {
    // Cập nhật thông tin hồ sơ cá nhân
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_address = mysqli_real_escape_string($conn, $_POST['address']);

    $update_query = "UPDATE users SET 
                        name = '$new_name', 
                        email = '$new_email', 
                        phone = '$new_phone', 
                        address = '$new_address' 
                    WHERE id = '$user_id'";
    mysqli_query($conn, $update_query);

    $message[] = 'Cập nhật thông tin thành công!';
    header('location:student_profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý hồ sơ cá nhân</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/new-style.css">
</head>
<body>
<?php include 'student_header.php'; ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Quản lý hồ sơ cá nhân</h2>

        <?php if (isset($message)) { 
            foreach ($message as $msg) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $msg; ?>
                </div>
        <?php } } ?>

        <!-- Form cập nhật thông tin -->
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>">
            </div>
            <button type="submit" name="update" class="new-btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>
<script></script>
</body>
</html>
