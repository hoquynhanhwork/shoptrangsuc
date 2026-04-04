<?php
$current_page = 'tong-quan.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - Tổng quan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
        }
        .sidebar {
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            background: #212737;
            color: #e4d4d6;
            transition: 0.3s;
            z-index: 1000;
        }
        .sidebar .logo {
            padding: 20px;
            text-align: center;
        }
        .sidebar .logo img {
            width: 80px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li a {
            display: block;
            padding: 12px 20px;
            color: #e4d4d6;
            text-decoration: none;
            transition: 0.2s;
        }
        .sidebar ul li a:hover, .sidebar ul li.active a {
            background: #e4d4d6;
            color: #212737;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #212737;
            color: #e4d4d6;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        @media (max-width: 992px) {
            .sidebar {
                left: -260px;
            }
            .sidebar.show {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
            }
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>
<aside class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="../img/logo.jpg" alt="Logo">
    </div>
    <ul>
        <li class="<?= $current_page == 'tong-quan.php' ? 'active' : '' ?>">
            <a href="tong-quan.php"><i class="fa-solid fa-gauge-high"></i> Tổng quan</a>
        </li>
        <li><a href="quan-ly-san-pham.php"><i class="fa-regular fa-gem"></i> Sản phẩm</a></li>
        <li><a href="quan-ly-don-hang.php"><i class="fa-solid fa-truck-fast"></i> Đơn hàng</a></li>
        <li><a href="quan-ly-khach-hang.php"><i class="fa-regular fa-user"></i> Khách hàng</a></li>
        <li><a href="danh-gia.php"><i class="fa-regular fa-star"></i> Đánh giá</a></li>
        <li><a href="thong-ke.php"><i class="fa-solid fa-chart-line"></i> Thống kê</a></li>
        <li><a href="cai-dat.php"><i class="fa-solid fa-sliders"></i> Cài đặt</a></li>
        <li><a href="../public/xu-ly-tai-khoan.php?action=dangxuat"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a></li>
    </ul>
</aside>
<div class="content">
    <div class="container-fluid">
        <h1 class="mt-2">Tổng quan</h1>
        <p>Chào mừng <?= htmlspecialchars($_SESSION['nguoidung']['HoTen']) ?> (Admin)</p>
        <hr>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng hôm nay</h5>
                        <p class="card-text display-6">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu tháng</h5>
                        <p class="card-text display-6">0₫</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Sản phẩm tồn kho</h5>
                        <p class="card-text display-6">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Khách hàng mới</h5>
                        <p class="card-text display-6">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>