<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-customer-detail-page';
$current_page = 'quan-ly-khach-hang.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = 'Khách hàng không tồn tại.';
    header('Location: quan-ly-khach-hang.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM nguoidung WHERE idUser = ? AND VaiTro = 'khachhang'");
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) {
    $_SESSION['error'] = 'Khách hàng không tồn tại.';
    header('Location: quan-ly-khach-hang.php');
    exit;
}

$page_orders = isset($_GET['page_orders']) ? max(1, intval($_GET['page_orders'])) : 1;
$limit_orders = 3;
$offset_orders = ($page_orders - 1) * $limit_orders;

$stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM donhang WHERE idUser = ?");
$stmtCount->execute([$id]);
$totalOrders = $stmtCount->fetch()['total'];
$totalPagesOrders = ceil($totalOrders / $limit_orders);

$stmt = $conn->prepare("
    SELECT dh.*, tt.TenTrangThai 
    FROM donhang dh
    LEFT JOIN trangthaidonhang tt ON dh.MaTrangThai = tt.MaTrangThai
    WHERE dh.idUser = ? 
    ORDER BY dh.NgayDatHang DESC
    LIMIT ? OFFSET ?
");
$stmt->bindParam(1, $id, PDO::PARAM_INT);
$stmt->bindParam(2, $limit_orders, PDO::PARAM_INT);
$stmt->bindParam(3, $offset_orders, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

$stmt = $conn->prepare("
    SELECT dg.*, sp.TenSanPham 
    FROM danhgia dg
    LEFT JOIN sanpham sp ON dg.MaSanPham = sp.MaSanPham 
    WHERE dg.idUser = ? 
    ORDER BY dg.NgayDanhGia DESC
");
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();

include "../layout/header_admin.php";
?>
<div class="content">
    <div class="container-fluid">
        <div class="page-header">
            <h1><i class="fa-solid fa-user-circle"></i> THÔNG TIN KHÁCH HÀNG</h1>
            <a href="quan-ly-khach-hang.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="stats-wrapper">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $totalOrders ?></div>
                    <div class="stat-label">Tổng đơn hàng</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count($reviews) ?></div>
                    <div class="stat-label">Tổng đánh giá</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?php
                        $stmtTotal = $conn->prepare("SELECT SUM(TongTien) as total FROM donhang WHERE idUser = ?");
                        $stmtTotal->execute([$id]);
                        $total_spent = $stmtTotal->fetch()['total'] ?? 0;
                        echo number_format($total_spent, 0, ',', '.');
                        ?>đ
                    </div>
                    <div class="stat-label">Tổng chi tiêu</div>
                </div>
            </div>
        </div>

        <div class="account-container">
            <div class="account-wrapper">
                <div class="account-info-card">
                    <div class="card-header">
                        <h5><i class="fa-solid fa-user-pen"></i> THÔNG TIN TÀI KHOẢN</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-label">Họ tên:</span>
                            <span class="info-value"><?= htmlspecialchars($customer['HoTen']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tên đăng nhập:</span>
                            <span class="info-value"><?= htmlspecialchars($customer['TenDangNhap']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?= htmlspecialchars($customer['Email']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Số điện thoại:</span>
                            <span class="info-value"><?= htmlspecialchars($customer['SoDienThoai'] ?: 'Chưa có') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Địa chỉ:</span>
                            <span class="info-value"><?= htmlspecialchars($customer['DiaChi'] ?: 'Chưa có') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ngày đăng ký:</span>
                            <span class="info-value"><?= date('d/m/Y H:i', strtotime($customer['NgayTao'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Trạng thái:</span>
                            <span class="info-value">
                                <?php if ($customer['TrangThai'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Khóa</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="customer-actions">
                            <?php if ($customer['TrangThai'] == 1): ?>
                                <a href="xu-ly-QLKH.php?action=lock&id=<?= $customer['idUser'] ?>" 
                                class="btn btn-lock btn-sm"
                                onclick="return confirm('Khóa tài khoản này?')">
                                    <i class="fa-solid fa-lock"></i> Khóa
                                </a>
                            <?php else: ?>
                                <a href="xu-ly-QLKH.php?action=unlock&id=<?= $customer['idUser'] ?>" 
                                class="btn btn-unlock btn-sm"
                                onclick="return confirm('Mở khóa tài khoản này?')">
                                    <i class="fa-solid fa-unlock"></i> Mở
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="account-orders-card">
                    <div class="card-header">
                        <h5><i class="fa-solid fa-clock-rotate-left"></i> LỊCH SỬ MUA HÀNG</h5>
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
                                            <div class="order-action">
                                                <a href="chi-tiet-don-hang.php?id=<?= $order['MaDonHang'] ?>" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye"></i> Xem chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($totalPagesOrders > 1): ?>
                            <nav aria-label="Orders pagination">
                                <ul class="pagination">
                                    <li class="page-item <?= ($page_orders <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?id=<?= $id ?>&page_orders=<?= $page_orders-1 ?>">«</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPagesOrders; $i++): ?>
                                        <li class="page-item <?= ($i == $page_orders) ? 'active' : '' ?>">
                                            <a class="page-link" href="?id=<?= $id ?>&page_orders=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($page_orders >= $totalPagesOrders) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?id=<?= $id ?>&page_orders=<?= $page_orders+1 ?>">»</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-orders text-center py-4">
                                <i class="bi bi-box-seam fs-1"></i>
                                <p class="mt-2">Chưa có đơn hàng nào</p>
                            </div>
                        <?php endif; ?>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer_admin.php"; ?>