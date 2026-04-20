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

$limit = 3;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM donhang WHERE idUser = ?");
$countStmt->execute([$idUser]);
$totalOrders = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalOrders / $limit);

$stmt = $conn->prepare("
    SELECT dh.*, tt.TenTrangThai 
    FROM donhang dh
    LEFT JOIN trangthaidonhang tt ON dh.MaTrangThai = tt.MaTrangThai
    WHERE dh.idUser = ? 
    ORDER BY dh.NgayDatHang DESC
    LIMIT ? OFFSET ?
");
$stmt->bindParam(1, $idUser, PDO::PARAM_INT);
$stmt->bindParam(2, $limit, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
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
                                    <span class="order-id">Mã đơn: #<?= htmlspecialchars($order['MaDonHang']) ?></span>
                                    <span class="order-date"><?= date('d/m/Y H:i', strtotime($order['NgayDatHang'])) ?></span>
                                </div>
                                <div class="order-details">
                                    <div class="order-status">
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['TenTrangThai'] ?? 'Đang xử lý')) ?>">
                                            <?= htmlspecialchars($order['TenTrangThai'] ?? 'Đang xử lý') ?>
                                        </span>
                                        <span class="order-total"><?= number_format($order['TongTien']) ?>đ</span>
                                    </div>
                                    <div class="order-method">
                                        <i class="bi bi-credit-card"></i> 
                                        <?php
                                        switch ($order['PhuongThucThanhToan']) {
                                            case 'cod': echo 'Thanh toán khi nhận hàng (COD)'; break;
                                            case 'momo': echo 'Ví MoMo'; break;
                                            case 'bank': echo 'Chuyển khoản ngân hàng'; break;
                                            default: echo htmlspecialchars($order['PhuongThucThanhToan']);
                                        }
                                        ?>
                                    </div>
                                    <div class="order-action mt-2">
                                        <a href="chi-tiet-don-hang.php?id=<?= $order['MaDonHang'] ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">«</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">»</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>

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