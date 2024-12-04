<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Thêm tài khoản mới
if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $role = 'teacher';

    // Kiểm tra email đã tồn tại
    $check_email_query = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('Query failed');

    if (mysqli_num_rows($check_email_query) > 0) {
        $message[] = 'Email đã tồn tại!';
    } else {
        // Thêm người dùng mới
        $insert_user_query = mysqli_query($conn, "INSERT INTO `users` (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')") or die('Query failed');

        if ($insert_user_query) {
            $message[] = 'Thêm tài khoản giảng viên thành công!';
        } else {
            $message[] = 'Thêm tài khoản giảng viên thất bại!';
        }
    }
}

// Xóa tài khoản
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Xóa tài khoản
    $delete_user_query = mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id' AND role != 'admin'") or die('Query failed');

    if ($delete_user_query) {
        $message[] = 'Xóa tài khoản giảng viên thành công!';
    } else {
        $message[] = 'Xóa tài khoản giảng viên thất bại!';
    }

    header('location:admin_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        th {
            font-size: 20px;
            text-align: center;
        }
        td {
            font-size: 18px;
            padding: 1.5rem 0.5rem !important;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="title">Quản lý tài khoản giảng viên</h1>

    <form action="" method="post">
        <h3>Thêm tài khoản giảng viên mới</h3>
        <input type="text" name="name" class="box" placeholder="Tên người dùng" required>
        <input type="email" name="email" class="box" placeholder="Email" required>
        <input type="password" name="password" class="box" placeholder="Mật khẩu" required>
        <input type="submit" value="Thêm tài khoản" name="add_user" class="btn-primary" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;">
    </form>
</section>

<section class="show-users">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_users_query = mysqli_query($conn, "SELECT * FROM `users` WHERE role != 'admin' ") or die('Query failed');
                if (mysqli_num_rows($select_users_query) > 0) {
                    while ($fetch_user = mysqli_fetch_assoc($select_users_query)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_user['id']; ?></td>
                            <td><?php echo $fetch_user['name']; ?></td>
                            <td><?php echo $fetch_user['email']; ?></td>
                            <td><?php echo $fetch_user['role'] == 'student' ? 'Học sinh' : 'Giảng viên'  ?></td>
                            <td><?php echo $fetch_user['created_at']; ?></td>
                            <td>
                                <a href="admin_accounts.php?delete=<?php echo $fetch_user['id']; ?>" class="btn-danger btn-sm" style="padding: 10px; text-decoration: none; font-size: 14px;" onclick="return confirm('Xóa tài khoản này?');">Xóa</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center" style="font-size: 25px;">Không có tài khoản nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
