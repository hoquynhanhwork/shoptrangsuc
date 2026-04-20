<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$cart_count = 0;
if (isset($_SESSION['giohang'])) {
    foreach ($_SESSION['giohang'] as $qty) {
        $cart_count += $qty;
    }
}
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aura Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/public.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="<?= htmlspecialchars($bodyClass) ?>">

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
                <li class="nav-item dropdown">
                     <a class="nav-link d-flex align-items-center" href="san-pham.php">
                        SẢN PHẨM <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu p-2">
                        <li><h6 class="dropdown-header">Phân loại</h6></li>
                        <li><a class="dropdown-item" href="san-pham.php?madanhmuc=1">Nhẫn</a></li>
                        <li><a class="dropdown-item" href="san-pham.php?madanhmuc=2">Vòng tay</a></li>
                        <li><a class="dropdown-item" href="san-pham.php?madanhmuc=3">Dây chuyền</a></li>
                        <li><a class="dropdown-item" href="san-pham.php?madanhmuc=4">Mặt dây chuyền</a></li>
                        <li><a class="dropdown-item" href="san-pham.php?madanhmuc=5">Hoa tai</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link  d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        HỖ TRỢ <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="supportDropdown">
                        <li><a class="dropdown-item" href="chinh-sach-doi-tra.php">Chính sách đổi trả</a></li>
                        <li><a class="dropdown-item" href="chinh-sach-van-chuyen.php">Chính sách vận chuyển</a></li>
                        <li><a class="dropdown-item" href="huong-dan-bao-duong.php">Hướng dẫn bảo dưỡng</a></li>
                        <li><a class="dropdown-item" href="huong-dan-do-kich-thuoc.php">Hướng dẫn đo kích thước</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lien-he.php">LIÊN HỆ</a>
                </li>
            </ul>
        </div>
    </div>
</nav>