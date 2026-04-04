<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để xem thông tin tài khoản.';
    header('Location: dang-nhap.php');
    exit;
}

$user = $_SESSION['nguoidung'];
$idUser = $user['idUser'];

$stmt = $conn->prepare("SELECT SoDienThoai, DiaChi FROM nguoidung WHERE idUser = ?");
$stmt->execute([$idUser]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if ($info) {
    $user['SoDienThoai'] = $info['SoDienThoai'] ?? '';
    $user['DiaChi']      = $info['DiaChi'] ?? '';
    $_SESSION['nguoidung']['SoDienThoai'] = $user['SoDienThoai'];
    $_SESSION['nguoidung']['DiaChi']      = $user['DiaChi'];
} else {
    $user['SoDienThoai'] = '';
    $user['DiaChi']      = '';
}

$stmt = $conn->prepare("
    SELECT * FROM donhang 
    WHERE idUser = ? 
    ORDER BY NgayDatHang DESC
");
$stmt->execute([$idUser]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bodyClass = 'account-page';
include "../layout/header_public.php";
?>

<div class="account-container">
    <div class="account-wrapper">
        <div class="account-info-card">
            <div class="card-header">
                <h5>👤 THÔNG TIN TÀI KHOẢN</h5>
                <a href="sua-tai-khoan.php" class="btn-edit">Chỉnh sửa</a>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">Họ tên:</span>
                    <span class="info-value"><?= htmlspecialchars($user['HoTen']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tên đăng nhập:</span>
                    <span class="info-value"><?= htmlspecialchars($user['TenDangNhap']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($user['Email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value"><?= htmlspecialchars($user['SoDienThoai'] ?: 'Chưa có') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Địa chỉ:</span>
                    <span class="info-value"><?= htmlspecialchars($user['DiaChi'] ?: 'Chưa có') ?></span>
                </div>
                <div class="action-buttons">
                    <a href="doi-mat-khau.php" class="btn btn-outline-primary">🔑 Đổi mật khẩu</a>
                    <a href="xu-ly-tai-khoan.php?action=dangxuat" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">🚪 Đăng xuất</a>
                </div>
            </div>
        </div>

        <div class="account-orders-card">
            <div class="card-header">
                <h5>📦 LỊCH SỬ MUA HÀNG</h5>
            </div>
            <div class="card-body">
                <?php if (count($orders) > 0): ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <span class="order-id">Mã đơn: #<?= $order['MaDonHang'] ?></span>
                                    <span class="order-date"><?= date('d/m/Y H:i', strtotime($order['NgayDatHang'])) ?></span>
                                </div>
                                <div class="order-details">
                                    <div class="order-status">
                                        <span class="status-badge status-<?= strtolower($order['payment_status']) ?>">
                                            <?= $order['payment_status'] == 'pending' ? 'Chờ thanh toán' : ($order['payment_status'] == 'paid' ? 'Đã thanh toán' : 'Thất bại') ?>
                                        </span>
                                        <span class="order-total"><?= number_format($order['TongTien']) ?>đ</span>
                                    </div>
                                    <div class="order-method">
                                        <i class="bi bi-credit-card"></i> 
                                        <?= $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng' : ($order['payment_method'] == 'momo' ? 'Ví MoMo' : 'Chuyển khoản ngân hàng') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-orders">
                        <i class="bi bi-box-seam"></i>
                        <p>Chưa có đơn hàng nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer_public.php"; ?>