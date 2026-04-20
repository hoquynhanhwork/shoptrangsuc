<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để xem chi tiết đơn hàng.';
    header('Location: dang-nhap.php');
    exit;
}

$idUser = $_SESSION['nguoidung']['idUser'];
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$orderId) {
    $_SESSION['error'] = 'Không tìm thấy đơn hàng.';
    header('Location: tai-khoan.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT dh.*, tt.TenTrangThai 
    FROM donhang dh
    LEFT JOIN trangthaidonhang tt ON dh.MaTrangThai = tt.MaTrangThai
    WHERE dh.idUser = ? AND dh.MaDonHang = ?
");
$stmt->execute([$idUser, $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error'] = 'Đơn hàng không tồn tại hoặc không thuộc về bạn.';
    header('Location: tai-khoan.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT ct.SoLuong, ct.DonGia, sp.TenSanPham, sp.HinhAnh, s.size
    FROM chitietdonhang ct
    JOIN size s ON ct.id_size = s.id
    JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
    WHERE ct.MaDonHang = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bodyClass = 'order-detail-page';
include "../layout/header_public.php";
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-box d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="fw-bold mb-0">CHI TIẾT ĐƠN HÀNG</h4>
                    <span class="text-muted small">Mã đơn: #<?= htmlspecialchars($order['MaDonHang']) ?></span>
                </div>
                <div>
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['TenTrangThai'] ?? 'Đang xử lý')) ?>">
                        <?= htmlspecialchars($order['TenTrangThai'] ?? 'Đang xử lý') ?>
                    </span>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card-box">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-credit-card me-2"></i> Thông tin thanh toán</h5>
                        <div class="info-row">
                            <span class="info-label">Phương thức:</span>
                            <span class="info-value text-uppercase">
                                <?php 
                                switch ($order['PhuongThucThanhToan']) {
                                    case 'cod': echo 'Thanh toán khi nhận hàng (COD)'; break;
                                    case 'momo': echo 'Ví MoMo'; break;
                                    case 'bank': echo 'Chuyển khoản ngân hàng'; break;
                                    default: echo htmlspecialchars($order['PhuongThucThanhToan']);
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tổng tiền hàng:</span>
                            <span class="info-value"><?= number_format($order['TongTien'] - $order['PhiShip']) ?>đ</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phí vận chuyển:</span>
                            <span class="info-value"><?= number_format($order['PhiShip']) ?>đ</span>
                        </div>
                        <div class="info-row fw-bold">
                            <span class="info-label">Cần thanh toán:</span>
                            <span class="info-value" style="color: #e89aa9;"><?= number_format($order['TongTien']) ?>đ</span>
                        </div>
                    </div>

                    <div class="card-box">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-geo-alt me-2"></i> Địa chỉ giao hàng</h5>
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
                        <div class="info-row">
                            <span class="info-label">Ngày đặt:</span>
                            <span class="info-value"><?= date('d/m/Y H:i', strtotime($order['NgayDatHang'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card-box">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-bag-check me-2"></i> Sản phẩm đã đặt</h5>
                        <?php if (empty($items)): ?>
                            <p class="text-muted">Không có sản phẩm nào.</p>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <div class="d-flex mb-3 align-items-center">
                                    <img src="../img/<?= htmlspecialchars($item['HinhAnh']) ?>" width="60" class="rounded me-3" style="object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?= htmlspecialchars($item['TenSanPham']) ?></div>
                                        <div class="small text-muted">
                                            Số lượng: <?= $item['SoLuong'] ?>
                                            <?php if (!empty($item['size'])): ?>
                                                | Size: <?= htmlspecialchars($item['size']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div><?= number_format($item['DonGia']) ?>đ</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Tổng cộng:</span>
                                <span><?= number_format($order['TongTien']) ?>đ</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="tai-khoan.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại lịch sử đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer_public.php"; ?>