<?php
include 'config.php';
session_start();

$teacher_id = $_SESSION['teacher_id'];

if (!isset($teacher_id)) {
    header('location:login.php');
    exit();
}
// Thêm bài giảng mới
if (isset($_POST['add_lesson'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $sort_order = mysqli_real_escape_string($conn, $_POST['sort_order']);

    // Xử lý file video
    $video_name = $_FILES['video']['name'];
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_size = $_FILES['video']['size'];
    $video_folder = 'uploaded_videos/' . $video_name;

    // Xử lý file tài liệu
    $materials_name = $_FILES['materials']['name'];
    $materials_tmp_name = $_FILES['materials']['tmp_name'];
    $materials_folder = 'uploaded_materials/' . $materials_name;

    if (!empty($video_name) && $video_size > 0) {
        move_uploaded_file($video_tmp_name, $video_folder);
    } else {
        $video_folder = null;
    }

    if (!empty($materials_name)) {
        move_uploaded_file($materials_tmp_name, $materials_folder);
    } else {
        $materials_folder = null;
    }

    $insert_lesson_query = "INSERT INTO `lessons` (course_id, title, video_url, materials, sort_order) 
                            VALUES ('$course_id', '$title', '$video_folder', '$materials_folder', '$sort_order')";

    if (mysqli_query($conn, $insert_lesson_query)) {
        $message[] = 'Thêm bài giảng thành công!';
    } else {
        $message[] = 'Thêm bài giảng thất bại!';
    }
}

// Xóa bài giảng
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Lấy thông tin trước khi xóa
    $get_lesson_query = mysqli_query($conn, "SELECT video_url, materials FROM `lessons` WHERE id = '$delete_id'");
    $fetch_lesson = mysqli_fetch_assoc($get_lesson_query);

    if (!empty($fetch_lesson['video_url']) && file_exists($fetch_lesson['video_url'])) {
        unlink($fetch_lesson['video_url']);
    }
    if (!empty($fetch_lesson['materials']) && file_exists($fetch_lesson['materials'])) {
        unlink($fetch_lesson['materials']);
    }

    $delete_lesson_query = mysqli_query($conn, "DELETE FROM `lessons` WHERE id = '$delete_id'") or die('Query failed');
    header('location:teacher_lessons.php');
    exit();
}

// Cập nhật bài giảng
if (isset($_POST['update_lesson'])) {
    $lesson_id = mysqli_real_escape_string($conn, $_POST['lesson_id']);
    $update_title = mysqli_real_escape_string($conn, $_POST['update_title']);
    $update_sort_order = mysqli_real_escape_string($conn, $_POST['update_sort_order']);

    $update_lesson_query = "UPDATE `lessons` SET 
                            title = '$update_title', 
                            sort_order = '$update_sort_order' 
                            WHERE id = '$lesson_id'";

    if (mysqli_query($conn, $update_lesson_query)) {
        header('location:teacher_lessons.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài giảng</title>

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
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Thêm bài giảng mới</h3>
            <div class="mb-3">
                <label for="course_id" class="form-label">Chọn khóa học</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <?php
                    $select_courses_query = mysqli_query($conn, "SELECT id, title FROM `courses` WHERE teacher_id = '$teacher_id'") or die('Query failed');
                    while ($course = mysqli_fetch_assoc($select_courses_query)) {
                        echo "<option value='{$course['id']}'>{$course['title']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề bài giảng</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="video" class="form-label">Video bài giảng</label>
                <input type="file" name="video" id="video" class="form-control" accept="video/*">
            </div>
            <div class="mb-3">
                <label for="materials" class="form-label">Tài liệu đính kèm</label>
                <input type="file" name="materials" id="materials" class="form-control">
            </div>
            <div class="mb-3">
                <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" required>
            </div>
            <button type="submit" name="add_lesson" class="new-btn btn-primary">Thêm bài giảng</button>
        </form>
    </div>
</section>

<section class="show-lessons mt-4">
    <div class="container">
        <h3 class="text-center fs-1">Danh sách bài giảng</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Video</th>
                    <th>Tài liệu</th>
                    <th>Thứ tự</th>
                    <th>Khóa học</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_lessons_query = mysqli_query($conn, "SELECT * FROM `lessons` ORDER BY sort_order ASC") or die('Query failed');
                if (mysqli_num_rows($select_lessons_query) > 0) {
                    while ($lesson = mysqli_fetch_assoc($select_lessons_query)) {
                        echo "<tr>
                                <td>{$lesson['id']}</td>
                                <td>{$lesson['title']}</td>
                                <td>" . (!empty($lesson['video_url']) ? "<a href='{$lesson['video_url']}' target='_blank'>Xem</a>" : "N/A") . "</td>
                                <td>" . (!empty($lesson['materials']) ? "<a href='{$lesson['materials']}' target='_blank'>Tải về</a>" : "N/A") . "</td>
                                <td>{$lesson['sort_order']}</td>
                                <td>" . mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM `courses` WHERE id = '{$lesson['course_id']}'"))['title'] . "</td>
                                <td>
                                    <a href='#' class='fs-3 btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#updateModal{$lesson['id']}'>Sửa</a>
                                    <a href='teacher_lessons.php?delete={$lesson['id']}' class='fs-3 btn-danger btn-sm' onclick=\"return confirm('Bạn có chắc chắn muốn xóa?');\">Xóa</a>
                                </td>
                              </tr>";
                        // Modal cập nhật bài giảng
                        echo "<div class='modal fade' id='updateModal{$lesson['id']}' tabindex='-1'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <form action='' method='post'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title'>Cập nhật bài giảng</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <input type='hidden' name='lesson_id' value='{$lesson['id']}'>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Tiêu đề</label>
                                                    <input type='text' name='update_title' class='form-control' value='{$lesson['title']}' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label class='form-label'>Thứ tự</label>
                                                    <input type='number' name='update_sort_order' class='form-control' value='{$lesson['sort_order']}' required>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit' name='update_lesson' class='new-btn btn-primary'>Lưu</button>
                                                <button type='button' class='new-btn btn-secondary' data-bs-dismiss='modal'>Đóng</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                              </div>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Chưa có bài giảng nào.</td></tr>";
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
