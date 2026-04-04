
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị Jewelry Store</title>

    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>

<body>

<aside class="sidebar">

    <div class="logo text-center">
        <img src="../img/logo.jpg" alt="Jewelry Logo" style="width:80px;">
        <h5>Trang quản trị</h5>
    </div>

    <ul>
        <li class="<?= $current_page == 'tong-quan.php' ? 'active' : '' ?>">
            <a href="tong-quan.php">
                <i class="fa-solid fa-gauge"></i> Tổng quan
            </a>
        </li>

        <li class="<?= $current_page == 'quan-ly-san-pham.php' ? 'active' : '' ?>">
            <a href="quan-ly-san-pham.php">
                <i class="fa-solid fa-gem"></i> Sản phẩm
            </a>
        </li>

        <li class="<?= $current_page == 'quan-ly-don-hang.php' ? 'active' : '' ?>">
            <a href="quan-ly-don-hang.php">
                <i class="fa-solid fa-cart-shopping"></i> Đơn hàng
            </a>
        </li>

        <li class="<?= $current_page == 'quan-ly-khach-hang.php' ? 'active' : '' ?>">
            <a href="quan-ly-khach-hang.php">
                <i class="fa-solid fa-users"></i> Khách hàng
            </a>
        </li>

        <li class="<?= $current_page == 'danh-gia.php' ? 'active' : '' ?>">
            <a href="danh-gia.php">
                <i class="fa-solid fa-star"></i> Đánh giá
            </a>
        </li>

        <li class="<?= $current_page == 'thong-ke.php' ? 'active' : '' ?>">
            <a href="thong-ke.php">
                <i class="fa-solid fa-chart-line"></i> Thống kê
            </a>
        </li>

        <li class="<?= $current_page == 'cai-dat.php' ? 'active' : '' ?>">
            <a href="cai-dat.php">
                <i class="fa-solid fa-gear"></i> Cài đặt
            </a>
        </li>

        <li>
            <a href="../public/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </li>
    </ul>
</aside>
<div class="content"><?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/dang-nhap.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Quản trị Jewelry Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">

</head>
<body>

<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>

<aside class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="../img/logo.jpg" alt="Jewelry Logo">
    </div>
    <ul>
        <li class="<?= $current_page == 'tong-quan.php' ? 'active' : '' ?>">
            <a href="tong-quan.php">
                <i class="fa-solid fa-gauge-high"></i> Tổng quan
            </a>
        </li>
        <li class="<?= $current_page == 'quan-ly-san-pham.php' ? 'active' : '' ?>">
            <a href="quan-ly-san-pham.php">
                <i class="fa-regular fa-gem"></i> Sản phẩm
            </a>
        </li>
        <li class="<?= $current_page == 'quan-ly-don-hang.php' ? 'active' : '' ?>">
            <a href="quan-ly-don-hang.php">
                <i class="fa-solid fa-truck-fast"></i> Đơn hàng
            </a>
        </li>
        <li class="<?= $current_page == 'quan-ly-khach-hang.php' ? 'active' : '' ?>">
            <a href="quan-ly-khach-hang.php">
                <i class="fa-regular fa-user"></i> Khách hàng
            </a>
        </li>
        <li class="<?= $current_page == 'danh-gia.php' ? 'active' : '' ?>">
            <a href="danh-gia.php">
                <i class="fa-regular fa-star"></i> Đánh giá
            </a>
        </li>
        <li class="<?= $current_page == 'thong-ke.php' ? 'active' : '' ?>">
            <a href="thong-ke.php">
                <i class="fa-solid fa-chart-line"></i> Thống kê
            </a>
        </li>
        <li class="<?= $current_page == 'cai-dat.php' ? 'active' : '' ?>">
            <a href="cai-dat.php">
                <i class="fa-solid fa-sliders"></i> Cài đặt
            </a>
        </li>
        <li>
            <a href="../public/logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
            </a>
        </li>
    </ul>
</aside>

<div class="content">
    <div class="page-header">
        <h1><?= ucwords(str_replace('-', ' ', pathinfo($current_page, PATHINFO_FILENAME))) ?></h1>
        <p>Quản lý và theo dõi hoạt động cửa hàng</p>
    </div>

    <div class="main-content">
        <?php
        ?>
    </div>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
    document.addEventListener('click', function(event) {
        const isClickInside = sidebar.contains(event.target);
        const isToggle = menuToggle.contains(event.target);
        if (!isClickInside && !isToggle && window.innerWidth <= 992) {
            sidebar.classList.remove('show');
        }
    });
</script>
</body>
</html>