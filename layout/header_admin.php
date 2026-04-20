<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Quản trị Aura Jewelry</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="<?= $bodyClass ?? '' ?>">
<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>
<aside class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="../img/logo.jpg" alt="Jewelry Logo">
    </div>
    <ul>
        <li class="<?= $current_page == 'tong-quan.php' ? 'active' : '' ?>">
            <a href="tong-quan.php"><i class="fa-solid fa-gauge-high"></i> Tổng quan</a>
        </li>
        <li class="<?= $current_page == 'quan-ly-san-pham.php' ? 'active' : '' ?>">
            <a href="quan-ly-san-pham.php"><i class="fa-regular fa-gem"></i> Sản phẩm</a>
        </li>
        <li class="<?= $current_page == 'quan-ly-don-hang.php' ? 'active' : '' ?>">
            <a href="quan-ly-don-hang.php"><i class="fa-solid fa-truck-fast"></i> Đơn hàng</a>
        </li>
        <li class="<?= $current_page == 'quan-ly-khach-hang.php' ? 'active' : '' ?>">
            <a href="quan-ly-khach-hang.php"><i class="fa-solid fa-users"></i> Khách hàng</a>
        </li>
        <li class="<?= $current_page == 'quan-ly-danh-gia.php' ? 'active' : '' ?>">
            <a href="quan-ly-danh-gia.php"><i class="fa-regular fa-star"></i> Đánh giá</a>
        </li>
        <li class="<?= $current_page == 'cai-dat.php' ? 'active' : '' ?>">
            <a href="cai-dat.php"><i class="fa-solid fa-sliders"></i> Cài đặt</a>
        </li>
        <li>
            <a href="../public/xu-ly-tai-khoan.php?action=dangxuat">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
            </a>
        </li>
    </ul>
</aside>
