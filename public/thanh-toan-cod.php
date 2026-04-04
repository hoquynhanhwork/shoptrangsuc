<?php
session_start();
require_once '../config.php';

$orderId = $_GET['id'] ?? $_SESSION['last_order_id'] ?? null;
if (!$orderId) {
    header("Location: index.php");
    exit;
}
$stmt = $conn->prepare("SELECT * FROM donhang WHERE MaDonHang = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    header("Location: index.php");
    exit;
}

// Lấy chi tiết sản phẩm
$stmt = $conn->prepare("
    SELECT ct.SoLuong, ct.DonGia, sp.TenSanPham, sp.HinhAnh
    FROM chitietdonhang ct
    JOIN size s ON ct.id_size = s.id
    JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
    WHERE ct.MaDonHang = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
unset($_SESSION['last_order_id']);

$bodyClass = 'checkout-success-page';
include "../layout/header_public.php";
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-box shadow-sm border-0" style="overflow: hidden;">
                <div class="d-flex align-items-center border-bottom pb-4 mb-4">
                    <div class="success-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold" style="color: #28a745;">ĐẶT HÀNG THÀNH CÔNG!</h3>
                        <p class="mb-0 text-muted">Cảm ơn bạn đã mua sắm tại Aura Jewelry</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                            <div>
                                <span class="text-muted">Mã đơn hàng</span>
                                <div class="order-code">#<?= htmlspecialchars($order['MaDonHang']) ?></div>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-truck"></i> Đang chuẩn bị hàng
                                </span>
                            </div>
                        </div>

                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-3"><i class="bi bi-credit-card"></i> Phương thức thanh toán</h5>
                                <div class="info-row">
                                    <span class="info-label">Hình thức:</span>
                                    <span class="info-value text-uppercase"><?= $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : $order['payment_method'] ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tổng đơn:</span>
                                    <span class="info-value"><?= number_format($order['TongTien']) ?>đ</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Phí vận chuyển:</span>
                                    <span class="info-value">30.000đ</span>
                                </div>
                                <div class="info-row border-bottom-0 fw-bold">
                                    <span class="info-label">Cần thanh toán:</span>
                                    <span class="info-value" style="color: #e89aa9;"><?= number_format($order['TongTien'] + 30000) ?>đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-3"><i class="bi bi-geo-alt"></i> Địa chỉ giao hàng</h5>
                                <div class="info-row">
                                    <span class="info-label">Người nhận:</span>
                                    <span class="info-value"><?= htmlspecialchars($order['TenKhachHang']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Số điện thoại:</span>
                                    <span class="info-value"><?= htmlspecialchars($order['SoDienThoai']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Địa chỉ:</span>
                                    <span class="info-value"><?= htmlspecialchars($order['DiaChiGiaoHang']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Ghi chú:</span>
                                    <span class="info-value"><?= htmlspecialchars($order['GhiChu'] ?? 'Không có') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-3"><i class="bi bi-bag-check"></i> Đơn hàng của bạn</h5>
                                <?php foreach ($items as $item): ?>
                                <div class="d-flex mb-3 align-items-center">
                                    <img src="<?= htmlspecialchars($item['HinhAnh']) ?>" width="60" class="rounded me-3" style="object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?= htmlspecialchars($item['TenSanPham']) ?></div>
                                        <div class="small text-muted">Số lượng: <?= $item['SoLuong'] ?></div>
                                        <div><?= number_format($item['DonGia']) ?>đ</div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span><?= number_format($order['TongTien'] + 30000) ?>đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="action-buttons d-flex justify-content-between flex-wrap gap-3 mt-5 pt-3 border-top">
                    <a href="trang-chu.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Quay lại trang chủ
                    </a>
                    <a href="chi-tiet-don-hang.php" class="btn btn-main" style="background: #212737; border-color: #212737;">
                        Xem đơn hàng của tôi <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer_public.php"; ?>