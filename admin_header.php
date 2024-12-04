<?php
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
<style>
   .header .flex .navbar a:hover,
   .header .flex .icons > *:hover,
   .header .flex .user-box p span {
    color: #0D6EFD !important;
}
</style>

<link rel="stylesheet" href="./css/new_style.css">

<header class="header">

   <div class="flex" style="padding: 10px 0 !important;">

      <div class="logo">
         <img width="90px" src="./image/logo_course.png" alt="">
      </div>

      <nav style="margin-bottom: 0px !important; min-height: unset !important;" class="navbar">
         <a style="text-decoration: none !important;" href="admin_accounts.php">Giảng viên</a>
         <a style="text-decoration: none !important;" href="admin_categories.php">Danh mục khóa học</a>
         <a style="text-decoration: none !important;" href="admin_payments.php">Hóa đơn</a>
      </nav>

      <a style="text-decoration: none" href="logout.php" class="delete-btn">Đăng xuất</a>

   </div>

</header>
