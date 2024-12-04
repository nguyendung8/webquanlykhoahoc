<?php
include 'config.php';
session_start();

$teacher_id = $_SESSION['teacher_id'];

if (!isset($teacher_id)) {
    header('location:login.php');
    exit();
}

// Thêm khóa học mới
if (isset($_POST['add_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);

    // Xử lý file ảnh
    $thumbnail_name = $_FILES['thumbnail']['name'];
    $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
    $thumbnail_size = $_FILES['thumbnail']['size'];
    $thumbnail_error = $_FILES['thumbnail']['error'];
    $thumbnail_folder = 'uploaded_img/' . $thumbnail_name;

    if ($thumbnail_error === 0) {
        if ($thumbnail_size > 2000000) { // Kiểm tra kích thước ảnh (2MB)
            $message[] = 'Kích thước ảnh quá lớn!';
        } else {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $file_ext = strtolower(pathinfo($thumbnail_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_ext)) {
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_folder);

                // Thêm khóa học vào cơ sở dữ liệu
                $insert_course_query = "INSERT INTO `courses` 
                    (title, description, thumbnail, price, category_id, difficulty, teacher_id) 
                    VALUES ('$title', '$description', '$thumbnail_folder', '$price', '$category_id', '$difficulty', '$teacher_id')";

                if (mysqli_query($conn, $insert_course_query)) {
                    $message[] = 'Thêm khóa học thành công!';
                } else {
                    $message[] = 'Thêm khóa học thất bại!';
                }
            } else {
                $message[] = 'Chỉ chấp nhận file ảnh có định dạng JPG, JPEG, PNG, GIF!';
            }
        }
    } else {
        $message[] = 'Lỗi khi tải ảnh lên!';
    }
}

// Xóa khóa học
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Lấy thông tin ảnh trước khi xóa
    $get_thumbnail_query = mysqli_query($conn, "SELECT thumbnail FROM `courses` WHERE id = '$delete_id' AND teacher_id = '$teacher_id'");
    $fetch_thumbnail = mysqli_fetch_assoc($get_thumbnail_query);
    $thumbnail_path = $fetch_thumbnail['thumbnail'];

    if (file_exists($thumbnail_path)) {
        unlink($thumbnail_path); // Xóa file ảnh
    }

    $delete_course_query = mysqli_query($conn, "DELETE FROM `courses` WHERE id = '$delete_id' AND teacher_id = '$teacher_id'") or die('Query failed');
    if ($delete_course_query) {
        $message[] = 'Xóa khóa học thành công!';
    } else {
        $message[] = 'Xóa khóa học thất bại!';
    }

    header('location:teacher_courses.php');
    exit();
}

// Cập nhật khóa học
if (isset($_POST['update_course'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $update_title = mysqli_real_escape_string($conn, $_POST['update_title']);
    $update_price = mysqli_real_escape_string($conn, $_POST['update_price']);
    $update_difficulty = mysqli_real_escape_string($conn, $_POST['update_difficulty']);

    $update_course_query = "UPDATE `courses` SET 
                            title = '$update_title', 
                            price = '$update_price', 
                            difficulty = '$update_difficulty' 
                            WHERE id = '$course_id' AND teacher_id = '$teacher_id'";

    if (mysqli_query($conn, $update_course_query)) {
        $message[] = 'Cập nhật khóa học thành công!';
    } else {
        $message[] = 'Cập nhật khóa học thất bại!';
    }

    header('location:teacher_courses.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khóa học</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="css/new_style.css">
    
    <style>
        th {
            font-size: 18px;
            text-align: center;
        }
        td {
            font-size: 16px;
            padding: 1rem 0.5rem !important;
            text-align: center;
        }
        label {
            float: left !important;
        }
        input {
            padding: 10px;
            font-size: 17px !important;
        }
    </style>
</head>
<body>

<?php include 'teacher_header.php'; ?>

<section class="add-products">

    <form action="" method="post" enctype="multipart/form-data" class="container mb-5">
        <h3 class="mb-3">Thêm khóa học mới</h3>
        <div class="mb-3">
            <label for="title" class="form-label">Tên khóa học</label>
            <input type="text" name="title" class="form-control" id="title" placeholder="Tên khóa học" required>
        </div>
        <div class="mb-3">
            <label for="thumbnail" class="form-label">Ảnh đại diện</label>
            <input type="file" name="thumbnail" class="form-control" id="thumbnail" accept="image/*" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" name="price" class="form-control" id="price" step="0.01" placeholder="Giá khóa học" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục</label>
            <?php
                $select_categories_query = mysqli_query($conn, "SELECT * FROM `categories`") or die('Query failed');
                $fetch_categories = mysqli_fetch_assoc($select_categories_query);

                if (mysqli_num_rows($select_categories_query) > 0) {
            ?>
                <select name="category_id" id="category_id" class="form-select" required>
                    <?php
                        do {
                    ?>
                        <option value="<?php echo $fetch_categories['id']; ?>"><?php echo $fetch_categories['name']; ?></option>
                    <?php
                        } while ($fetch_categories = mysqli_fetch_assoc($select_categories_query));
                    ?>
                </select>
            <?php
                } else {
                    echo '<p class="text-danger">Không có danh mục nào!</p>';
                }
            ?>
        </div>
        <div class="mb-3">
            <label for="difficulty" class="form-label">Độ khó</label>
            <select name="difficulty" id="difficulty" class="form-select" required>
                <option value="beginner">Dễ</option>
                <option value="intermediate">Trung bình</option>
                <option value="advanced">Khó</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" id="description" placeholder="Mô tả khóa học" rows="3" required></textarea>
        </div>
        <button type="submit" name="add_course" class="new-btn btn-primary">Thêm khóa học</button>
    </form>
</section>

<section class="show-courses">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên khóa học</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Độ khó</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_courses_query = mysqli_query($conn, "SELECT * FROM `courses` WHERE teacher_id = '$teacher_id'") or die('Query failed');
                if (mysqli_num_rows($select_courses_query) > 0) {
                    while ($fetch_course = mysqli_fetch_assoc($select_courses_query)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_course['id']; ?></td>
                            <td><?php echo $fetch_course['title']; ?></td>
                            <td>
                                <?php
                                    $category_id = $fetch_course['category_id'];
                                    $select_category_query = mysqli_query($conn, "SELECT name FROM `categories` WHERE id = '$category_id'") or die('Query failed');
                                    $fetch_category = mysqli_fetch_assoc($select_category_query);
                                    echo $fetch_category['name'];
                                ?>
                            <td>
                                <?php
                                    if ($fetch_course['price'] == 0) {
                                        echo 'Miễn phí';
                                    } else {
                                        echo number_format($fetch_course['price'], 0, ',', '.') . ' VNĐ';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if ($fetch_course['difficulty'] == 'beginner') {
                                        echo 'Dễ';
                                    } elseif ($fetch_course['difficulty'] == 'intermediate') {
                                        echo 'Trung bình';
                                    } else {
                                        echo 'Khó';
                                    }
                                ?>
                            </td>
                            <td><?php echo $fetch_course['created_at']; ?></td>
                            <td>
                                <a href="#" class="fs-2 btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $fetch_course['id']; ?>">Cập nhật</a>
                                <a href="teacher_courses.php?delete=<?php echo $fetch_course['id']; ?>" class="fs-2 btn-danger btn-sm" onclick="return confirm('Xóa khóa học này?');">Xóa</a>
                            </td>
                        </tr>

                    <!-- Modal Cập nhật khóa học -->
                        <div class="modal fade" id="updateModal<?php echo $fetch_course['id']; ?>" tabindex="-1" 
                            aria-labelledby="updateModalLabel<?php echo $fetch_course['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel<?php echo $fetch_course['id']; ?>">Cập nhật khóa học</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="course_id" value="<?php echo $fetch_course['id']; ?>">
                                            <div class="mb-3">
                                                <label for="update_title_<?php echo $fetch_course['id']; ?>" class="form-label">Tên khóa học</label>
                                                <input type="text" name="update_title" class="form-control" 
                                                    id="update_title_<?php echo $fetch_course['id']; ?>" 
                                                    value="<?php echo $fetch_course['title']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="update_price_<?php echo $fetch_course['id']; ?>" class="form-label">Giá</label>
                                                <input type="number" name="update_price" class="form-control" 
                                                    id="update_price_<?php echo $fetch_course['id']; ?>" 
                                                    value="<?php echo $fetch_course['price']; ?>" step="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="update_difficulty_<?php echo $fetch_course['id']; ?>" class="form-label">Độ khó</label>
                                                <select name="update_difficulty" id="update_difficulty_<?php echo $fetch_course['id']; ?>" 
                                                        class="form-select" required>
                                                    <option value="beginner" <?php if ($fetch_course['difficulty'] == 'beginner') echo 'selected'; ?>>Dễ</option>
                                                    <option value="intermediate" <?php if ($fetch_course['difficulty'] == 'intermediate') echo 'selected'; ?>>Trung bình</option>
                                                    <option value="advanced" <?php if ($fetch_course['difficulty'] == 'advanced') echo 'selected'; ?>>Khó</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="new-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" name="update_course" class="new-btn btn-primary">Cập nhật</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">Không có khóa học nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/slide_show.js"></script>
</body>
</html>
