<?php
// Trang chủ
$bodyClass = 'home-page';
require_once "../config.php";
include "../layout/header_public.php";

$stmtCats = $conn->query("SELECT * FROM danhmuc WHERE TrangThai = 1 ORDER BY MaDanhMuc");
$categories = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

$stmtFeatured = $conn->prepare("
    SELECT sp.*, sp.Gia as gia 
    FROM sanpham sp 
    WHERE sp.NoiBat = 1 AND sp.TrangThai = 1 AND sp.DelAt = 0
    ORDER BY sp.LuotXem DESC 
    LIMIT 8
");
$stmtFeatured->execute();
$featuredProducts = $stmtFeatured->fetchAll(PDO::FETCH_ASSOC);

$stmtNew = $conn->prepare("
    SELECT sp.*, sp.Gia as gia 
    FROM sanpham sp 
    WHERE sp.TrangThai = 1 AND sp.DelAt = 0
    ORDER BY sp.NgayTao DESC 
    LIMIT 8
");
$stmtNew->execute();
$newProducts = $stmtNew->fetchAll(PDO::FETCH_ASSOC);

$stmtNews = $conn->prepare("SELECT * FROM tintuc ORDER BY NgayDang DESC LIMIT 3");
$stmtNews->execute();
$news = $stmtNews->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../css/home.css">

<section class="home-slider position-relative">
    <div id="mainSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../img/bgr6.jpg" class="d-block w-100" alt="Trang sức cao cấp">
            </div>
            <div class="carousel-item">
                <img src="../img/bgr4.jpg" class="d-block w-100" alt="Trang sức kim cương">
            </div>
            <div class="carousel-item">
                <img src="../img/bgr3.jpg" class="d-block w-100" alt="Phụ kiện thời trang">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <div class="carousel-caption-global d-none d-md-block">
        <h2>Bộ sưu tập mới</h2>
        <p>Phong cách thanh lịch – Tỏa sáng mọi khoảnh khắc</p>
        <a href="san-pham.php" class="btn btn-primary">Xem ngay</a>
    </div>
</section>

<section class="home-categories py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2>Danh mục sản phẩm</h2>
            <p>Lựa chọn theo sở thích của bạn</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php 
            $catImages = [
                1 => 'nhan.jpg',
                2 => 'vongtay.jpg',
                3 => 'daychuyen.jpg',
                4 => 'matdaychuyen.jpg',
                5 => 'hoatai.jpg',
            ];
            foreach ($categories as $cat): 
                $imageName = $catImages[$cat['MaDanhMuc']] ?? 'default.jpg';
            ?>
                <div class="col-6 col-md-4 col-lg-2 text-center">
                    <a href="san-pham.php?madanhmuc=<?= $cat['MaDanhMuc'] ?>" class="category-card">
                        <div class="category-icon">
                            <img src="../img/<?= $imageName ?>" 
                                 alt="<?= htmlspecialchars($cat['TenDanhMuc']) ?>" 
                                 class="img-fluid rounded-circle">
                        </div>
                        <h5><?= htmlspecialchars($cat['TenDanhMuc']) ?></h5>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-featured py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2>Sản phẩm nổi bật</h2>
            <p>Được yêu thích nhất tháng</p>
        </div>
        <div class="row g-4">
            <?php if ($featuredProducts): ?>
                <?php foreach ($featuredProducts as $sp): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <a href="chi-tiet-san-pham.php?id=<?= $sp['MaSanPham'] ?>" class="text-decoration-none">
                                <img src="../img/<?= htmlspecialchars($sp['HinhAnh']) ?>" class="product-img" alt="<?= htmlspecialchars($sp['TenSanPham']) ?>">
                                <div class="product-info">
                                    <h6><?= htmlspecialchars($sp['TenSanPham']) ?></h6>
                                    <p class="price"><?= number_format($sp['gia'] ?? 0, 0, ',', '.') ?> đ</p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Chưa có sản phẩm nổi bật.</p>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="san-pham.php" class="btn btn-outline-primary">Xem tất cả sản phẩm</a>
        </div>
    </div>
</section>

<section class="home-new py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2>Sản phẩm mới</h2>
            <p>Xu hướng mới nhất từ Aura Jewelry</p>
        </div>
        <div class="row g-4">
            <?php if ($newProducts): ?>
                <?php foreach ($newProducts as $sp): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card">
                            <a href="chi-tiet-san-pham.php?id=<?= $sp['MaSanPham'] ?>" class="text-decoration-none">
                                <img src="../img/<?= htmlspecialchars($sp['HinhAnh']) ?>" class="product-img" alt="<?= htmlspecialchars($sp['TenSanPham']) ?>">
                                <div class="product-info">
                                    <h6><?= htmlspecialchars($sp['TenSanPham']) ?></h6>
                                    <p class="price"><?= number_format($sp['gia'] ?? 0, 0, ',', '.') ?> đ</p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Chưa có sản phẩm mới.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="home-banner my-5">
    <div class="container">
        <div class="row g-0 align-items-center bg-dark text-white rounded overflow-hidden" style="min-height: 200px;">
            <div class="col-md-6 p-3 p-md-4">
                <h3 class="mb-1 fs-4">Bộ sưu tập đặc biệt</h3>
                <p class="mb-2 small">Nhiều ưu đãi hấp dẫn cho thành viên mới. Đăng ký ngay!</p>
                <a href="dang-ky.php" class="btn btn-warning btn-sm">Đăng ký thành viên</a>
            </div>
            <div class="col-md-6">
                <img src="../img/bgr8.jpg" class="img-fluid" alt="Special collection" style="height: 200px; width: 100%; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<?php include "../layout/footer_public.php"; ?>