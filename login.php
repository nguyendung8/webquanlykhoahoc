<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) { // Xử lý khi người dùng nhấn nút "submit"
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = mysqli_real_escape_string($conn, md5($_POST['password'])); // Mã hóa mật khẩu bằng md5

   // Truy vấn kiểm tra thông tin đăng nhập
   $query = "SELECT * FROM `users` WHERE email = '$email' AND password = '$password'";
   $result = mysqli_query($conn, $query) or die('Query failed');

   // Kiểm tra kết quả truy vấn
   if (mysqli_num_rows($result) > 0) {
       $user = mysqli_fetch_assoc($result);

       if ($user['role'] == 'admin') {
           // Nếu là quản trị viên
           $_SESSION['admin_name'] = $user['name'];
           $_SESSION['admin_id'] = $user['id'];
           header('Location: admin_accounts.php'); // Chuyển đến trang quản trị
           exit();
       } elseif ($user['role'] == 'teacher') {
           // Nếu là giảng viên
             $_SESSION['teacher_name'] = $user['name'];
             $_SESSION['teacher_id'] = $user['id'];
           header('Location: teacher_courses.php'); // Chuyển đến trang giảng viên
           exit();
       } elseif ($user['role'] == 'student') {
           // Nếu là sinh viên
            $_SESSION['student_name'] = $user['name'];
            $_SESSION['student_id'] = $user['id'];
           header('Location: home.php'); // Chuyển đến trang sinh viên
           exit();
       }
   } else {
       $message[] = 'Tên tài khoản hoặc mật khẩu không chính xác!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng nhập</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .forget-btn {
         font-size: 20px;
         color: #9C27B0;
      }
      .forget-btn:hover {
         opacity: 0.8;
      }
   </style>
</head>
<body>

<?php
if (isset($message)) { // Hiển thị thông báo nếu có lỗi
    foreach ($message as $msg) {
        echo '
        <div class="d-flex justify-content-between align-items-center alert alert-info alert-dismissible fade show" role="alert">
            <span style="font-size: 16px;">' . $msg . '</span>
            <i style="font-size: 20px; cursor: pointer" class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<div class="form-container">
   <form action="" method="post">
      <h3>Đăng nhập</h3>
      <input type="email" name="email" placeholder="Nhập email" required class="box">
      <input type="password" name="password" placeholder="Mật khẩu" required class="box">
      <input type="submit" name="submit" value="Đăng nhập" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary">
      <br>
      <p>Bạn chưa có tài khoản? <a style="color: blue; text-decoration: none;" href="register.php">Đăng ký</a></p>
   </form>
</div>

</body>
</html>
