<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Đếm số lượng giỏ hàng
$cart_count = 0;

if (isset($_SESSION['giohang'])) {
    foreach ($_SESSION['giohang'] as $qty) {
        $cart_count += $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewelry Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/public.css?v=20260407-2">
</head>

<body>

<div class="header-top">
    <div class="container d-flex align-items-center justify-content-between flex-wrap">

        <a href="trang-chu.php" class="logo d-flex align-items-center text-decoration-none">
            <img src="../img/logo.jpg" class="logo-img">
        </a>

        <form class="search-box d-flex mx-3" action="tim-kiem.php" method="get">
            <input type="text" name="keyword" placeholder="Tìm kiếm trang sức..." class="form-control">
            <button class="btn" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <div class="user-menu d-flex gap-4 align-items-center">

            <a href="dia-chi.php">
                <i class="bi bi-geo-alt-fill"></i> Cửa hàng
            </a>
            <?php if (isset($_SESSION['nguoidung'])): ?>
                <a href="tai-khoan.php">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= $_SESSION['nguoidung']['HoTen'] ?>
                </a>

                <a href="gio-hang.php" class="position-relative">
                    <i class="bi bi-bag me-1"></i> Giỏ hàng

                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

            <?php else: ?>
                <a href="dang-nhap.php">
                    <i class="bi bi-person-circle me-1"></i> Đăng nhập
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg">
    <div class="container">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="gioi-thieu.php">GIỚI THIỆU</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="bo-suu-tap.php">BỘ SƯU TẬP</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="san-pham.php" data-bs-toggle="dropdown">
                        SẢN PHẨM <i class="bi bi-chevron-down ms-1"></i>
                    </a>

                    <ul class="dropdown-menu p-3" style="min-width: 400px;">
                        <div class="row">

                            <div class="col-4">
                                <h6>Phân loại</h6>
                                <a class="dropdown-item" href="danh-muc.php?loai=vong-tay">Vòng tay</a>
                                <a class="dropdown-item" href="danh-muc.php?loai=day-chuyen">Dây chuyền</a>
                                <a class="dropdown-item" href="danh-muc.php?loai=hoa-tai">Hoa tai</a>
                                <a class="dropdown-item" href="danh-muc.php?loai=nhan">Nhẫn</a>
                                <a class="dropdown-item" href="danh-muc.php?loai=mat-day">Mặt dây</a>
                            </div>

                            <div class="col-4">
                                <h6>Sản phẩm đôi</h6>
                                <a class="dropdown-item" href="danh-muc.php?loai=nhan-doi">Nhẫn đôi</a>
                                <a class="dropdown-item" href="danh-muc.php?loai=vong-doi">Vòng đôi</a>
                            </div>

                            <div class="col-4">
                                <h6>Chất liệu</h6>
                                <a class="dropdown-item" href="danh-muc.php?chatlieu=bac">Bạc</a>
                                <a class="dropdown-item" href="danh-muc.php?chatlieu=ma-vang">Mạ vàng</a>
                                <a class="dropdown-item" href="danh-muc.php?chatlieu=titan">Titan</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="dich-vu.php">DỊCH VỤ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="lien-he.php">LIÊN HỆ</a>
                </li>

            </ul>
        </div>
    </div>
</nav>
