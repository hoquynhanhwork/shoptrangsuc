<?php
// PREVIEW ONLY – Dữ liệu mẫu để xem giao diện
$order = [
    'MaDonHang' => 'AU-20250403-00128',
    'TongTien' => 1250000,
    'payment_method' => 'cod',
    'TenKhachHang' => 'Nguyễn Thị Hà',
    'SoDienThoai' => '0909 123 456',
    'DiaChiGiaoHang' => 'Số 12, đường Võ Thị Sáu, phường Đa Kao, quận 1, TP.HCM',
    'NgayDat' => '03/04/2025 14:30'
];

$items = [
    ['TenSanPham' => 'Nhẫn vàng 18K đính đá quý', 'DonGia' => 890000, 'SoLuong' => 1, 'HinhAnh' => 'https://placehold.co/400x400/f5e6d3/614649?text=Ring'],
    ['TenSanPham' => 'Bông tai bạc pha zirconia', 'DonGia' => 360000, 'SoLuong' => 1, 'HinhAnh' => 'https://placehold.co/400x400/f5e6d3/614649?text=Earrings']
];
$phiShip = 30000;
$tongThanhToan = $order['TongTien'] + $phiShip;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - Aura Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f4f2ef;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }
        .checkout-success-container {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .card-success {
            background: #ffffff;
            border: none;
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            height: 100%;
        }
        .card-header-custom {
            background: transparent;
            border-bottom: 1px solid #f0eae4;
            padding: 1.5rem 1.8rem;
            font-weight: 600;
        }
        .card-body-custom {
            padding: 1.8rem;
        }
        .success-badge {
            background: #e9f7e1;
            color: #2c6e2c;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .order-code {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #614649;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 10px 0;
            border-bottom: 1px dashed #f0eae4;
        }
        .info-label {
            color: #8b7a7a;
            font-weight: 500;
        }
        .info-value {
            font-weight: 600;
            color: #2d2a2a;
        }
        .status-badge {
            background: #fff3e0;
            color: #b45f2b;
            border-radius: 50px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 16px;
            background: #faf5f0;
        }
        .btn-outline-shop {
            border: 1.5px solid #614649;
            color: #614649;
            border-radius: 40px;
            padding: 10px 20px;
            transition: 0.2s;
        }
        .btn-outline-shop:hover {
            background: #614649;
            color: white;
        }
        .total-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: #614649;
        }
        @media (max-width: 768px) {
            .order-code { font-size: 1.3rem; }
            .card-body-custom { padding: 1.2rem; }
        }
    </style>
</head>
<body>

<div class="checkout-success-container">
    <div class="row g-4">
        <!-- Cột trái - Thông báo + Chi tiết đơn -->
        <div class="col-lg-7">
            <div class="card-success">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center gap-3 flex-wrap justify-content-between">
                        <div class="success-badge">
                            <i class="bi bi-check-circle-fill"></i> ĐẶT HÀNG THÀNH CÔNG
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar3"></i> <?= $order['NgayDat'] ?>
                        </div>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="text-center mb-4">
                        <div class="order-code">#<?= $order['MaDonHang'] ?></div>
                        <p class="text-muted mt-2">Cảm ơn bạn đã mua sắm tại Aura Jewelry</p>
                    </div>

                    <!-- Tóm tắt thanh toán -->
                    <div class="bg-light p-3 rounded-4 mb-4">
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="info-label">Tổng đơn</div>
                                <div class="info-value fs-5"><?= number_format($order['TongTien']) ?>đ</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="info-label">Phí vận chuyển</div>
                                <div class="info-value fs-5"><?= number_format($phiShip) ?>đ</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="info-label">Cần thanh toán</div>
                                <div class="info-value fs-4 fw-bold text-success"><?= number_format($tongThanhToan) ?>đ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán & vận chuyển -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-label"><i class="bi bi-wallet2"></i> Phương thức thanh toán</div>
                            <div class="info-value mt-1">
                                <span class="badge bg-success bg-opacity-10 text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-cash-stack"></i> Thanh toán khi nhận hàng (COD)
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label"><i class="bi bi-truck"></i> Phương thức giao hàng</div>
                            <div class="info-value mt-1">Giao hàng tiêu chuẩn (dự kiến 1-3 ngày)</div>
                        </div>
                    </div>

                    <!-- Trạng thái đơn hàng -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 p-3 rounded-4" style="background: #fef7e8;">
                        <div class="status-badge">
                            <i class="bi bi-hourglass-split"></i> Đang chuẩn bị hàng
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-envelope-paper"></i> Bạn sẽ nhận email xác nhận trong ít phút
                        </div>
                    </div>

                    <!-- Địa chỉ giao hàng -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold"><i class="bi bi-geo-alt-fill"></i> Địa chỉ nhận hàng</h6>
                        <p class="mt-2 mb-0">
                            <?= $order['TenKhachHang'] ?> | <strong><?= $order['SoDienThoai'] ?></strong><br>
                            <?= $order['DiaChiGiaoHang'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải - Giỏ hàng & tổng kết -->
        <div class="col-lg-5">
            <div class="card-success">
                <div class="card-header-custom">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-bag-check"></i> Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body-custom">
                    <?php foreach ($items as $item): ?>
                    <div class="d-flex gap-3 mb-4 align-items-center">
                        <img src="<?= $item['HinhAnh'] ?>" class="product-img" alt="<?= $item['TenSanPham'] ?>">
                        <div class="flex-grow-1">
                            <div class="fw-semibold"><?= $item['TenSanPham'] ?></div>
                            <div class="small text-secondary">Số lượng: <?= $item['SoLuong'] ?></div>
                        </div>
                        <div class="fw-bold"><?= number_format($item['DonGia']) ?>đ</div>
                    </div>
                    <?php endforeach; ?>

                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span><?= number_format($order['TongTien']) ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển</span>
                        <span><?= number_format($phiShip) ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pt-2 border-top">
                        <span class="fw-bold fs-6">Tổng thanh toán</span>
                        <span class="fw-bold total-text fs-5"><?= number_format($tongThanhToan) ?>đ</span>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
 <a href="trang-chu.php" class="btn btn-outline-shop"><i class="bi bi-arrow-left"></i> Tiếp tục mua sắm</a>                     <a href="don-hang-cua-toi.php" class="btn btn-dark" style="background:#614649; border:none;">Xem chi tiết đơn hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>