<?php
include 'config.php';

$student_id = @$_SESSION['student_id'];

   //nhúng vào các trang bán hàng
   if(isset($message)){ // hiển thị thông báo sau khi thao tác với biến message được gán giá trị
      foreach($message as $msg){
         echo '
         <div class=" alert alert-info alert-dismissible fade show" role="alert">
            <span style="font-size: 16px;">'.$msg.'</span>
            <i style="font-size: 20px; cursor: pointer" class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>';
      }
   }
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="./css/new_style.css">

<style>
   .changepw-btn {
    border-radius: 4px;
    font-size: 20px;
    background: blue;
    color: #fff;
    padding: 7px 12px;
 }
 .changepw-btn:hover {
    opacity: 0.7;
 }
 .header .header-2 .flex .navbar a:hover,
 .header .header-2 .flex .icons > *:hover,
 .header .header-2 .flex .user-box p span {
    color: #0D6EFD !important;
}
</style>

<style>
   .box p {
      font-size: 17px;
      padding-bottom: 5px;
   }
   .action {
      display: flex;
      align-items: center;
   }
   .view-product {
      margin-top: 5px;
      padding: 5px 20px;
      background-color: burlywood;
      font-size: 16px;
      color: #fff;
      border-radius: 6px;
   }
   .view-product:hover {
      opacity: 0.9;
   }
   .slideshow-container {
      position: relative;
      max-width: 800px;
      margin: 0 auto;
      overflow: hidden; /* Để ẩn phần ngoài khung hình ảnh */
   }
   .slide {
      display: none;
      animation: fade 2s ease-in-out infinite; /* Sử dụng animation để thêm hiệu ứng lướt sang */
   }
   @keyframes fade {
      0%, 100% {
         opacity: 0;
      }
      25%, 75% {
         opacity: 1;
      }
   }
   .slide img {
      width: 100%;
      height: 485px;
      border-radius: 9px;
   }
   .borrow_book:hover { 
      opacity: 0.9;
   }
   .borrow_book {
      padding: 5px 25px;
      background-image: linear-gradient(to right, #ff9800, #F7695D);
      border-radius: 4px;
      cursor: pointer;
      font-size: 20px;
      color: #fff;
      font-weight: 700;
   }
   .home-banner {
      min-height: 70vh;
      background:linear-gradient(rgba(0,0,0,.1), rgba(0,0,0,.1)), url(./image/bg-course.png) no-repeat;
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .title {
      font-size: 30px;
      color: #00695c;
      text-align: center;
      margin: 20px 0;
      font-weight: bold;
   }

   /* Phong cách cho box dịch vụ */
   .service-container {
      display: flex;
      justify-content: center;
      gap: 40px;
      padding: 20px 0;
   }
   .service-box {
      width: 300px;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      text-align: center;
      background-color: #fff;
      transition: all 0.3s ease;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
   }
   .service-box:hover {
      transform: translateY(-5px);
      box-shadow: 0px 6px 12px rgba(0,0,0,0.15);
   }
   .service-box i {
      font-size: 50px;
      color: #009688;
      margin-bottom: 15px;
   }
   .service-box a {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      text-decoration: none;
      display: block;
      margin-top: 10px;
   }
   .service-box a:hover {
      color: #009688;
   }

   /* Phong cách cho danh sách tin tức */
   .news-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 20px 0;
   }
   .news-item {
      display: flex;
      gap: 15px;
      padding: 15px;
      border-bottom: 1px solid #ddd;
      transition: all 0.3s ease;
      align-items: center;
   }
   .news-item:hover {
      background-color: #f0f0f0;
   }
   .news-img {
      min-width: 120px;
      height: 70px;
      border-radius: 6px;
      overflow: hidden;
   }
   .news-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
   }
   .news-details {
      display: flex;
      flex-direction: column;
   }
   .news-details h4 {
      font-size: 18px;
      margin: 0;
      font-weight: bold;
      color: #333;
   }
   .news-details p {
      font-size: 14px;
      color: #666;
      margin: 5px 0 0;
   }
</style>

<header class="header">
   <div class="header-2">
      <div style="padding: 10px 20px;" class="flex">
         <a href="home.php" class="logo"><img width="80px" height="80px" src="./image/logo_course.png"></a>

         <nav class="navbar">
            <a href="home.php">Trang chủ</a>
            <a href="student_profile.php">Hồ sơ cá nhân</a>
            <a href="student_course.php">Khóa học của tôi</a>
            <a href="student_payment.php">Hóa đơn</a>
            </nav>

         <?php if (empty($student_id)) { ?>
            <div class="fs-2">
               <a class="new-btn btn-primary" href="login.php">Đăng nhập</a>
               <a class="new-btn btn-info" href="register.php">Đăng ký</a>
            </div>
         <?php } else  {  ?>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
         </div>

         <div class="user-box" style="z-index: 100;">
            <p>Name : <span><?php echo $_SESSION['student_name']; ?></span></p>
            <a href="change_password.php" class="changepw-btn">Đổi mật khẩu</a>
            <a style="margin-top: 13px;" href="logout.php" class="delete-btn">Đăng xuất</a>
         </div>
         <?php } ?>
      </div>
   </div>

</header>

<section class="home home-banner">

   <div class="content">
      <div class="slideshow-container">
         <div class="slide fade">
            <img src="./image/slide_1.png" alt="slide 1">
         </div>
         <div class="slide fade">
            <img src="./image/slide_2.png" alt="slide 2">
         </div>
         <div class="slide fade">
            <img src="./image/slide_3.png" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./image/slide_4.png" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./image/slide_5.png" alt="slide 3">
         </div>
      </div>
   </div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>