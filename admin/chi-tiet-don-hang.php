<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-order-detail-page';
$current_page = 'quan-ly-don-hang.php';

$maDonHang = intval($_GET['id'] ?? 0);
if ($maDonHang <= 0) {
    $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Mã đơn hàng không hợp lệ.'];
    header('Location: quan-ly-don-hang.php');
    exit;
}

// Lấy thông tin đơn hàng chính (kết hợp nguoidung và trangthaidonhang)
$stmt = $conn->prepare("
    SELECT dh.*, nd.HoTen, nd.Email, nd.SoDienThoai, tt.TenTrangThai
    FROM donhang dh
    LEFT JOIN nguoidung nd ON dh.idUser = nd.idUser
    LEFT JOIN trangthaidonhang tt ON dh.MaTrangThai = tt.MaTrangThai
    WHERE dh.MaDonHang = ?
");
$stmt->execute([$maDonHang]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Đơn hàng không tồn tại.'];
    header('Location: quan-ly-don-hang.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT ct.MaChiTiet, ct.SoLuong, ct.DonGia, ct.ThanhTien, 
           sp.TenSanPham, sp.HinhAnh, sz.size
    FROM chitietdonhang ct
    LEFT JOIN size sz ON ct.id_size = sz.id
    LEFT JOIN sanpham sp ON sz.MaSanPham = sp.MaSanPham
    WHERE ct.MaDonHang = ?
");
$stmt->execute([$maDonHang]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtStatus = $conn->query("SELECT MaTrangThai, TenTrangThai FROM trangthaidonhang ORDER BY MaTrangThai");
$allStatuses = $stmtStatus->fetchAll();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert alert-<?= $_SESSION['admin_message']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['admin_message']['text'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['admin_message']); ?>
        <?php endif; ?>

        <div class="page-header">
            <h1><i class="fa-solid fa-receipt"></i> CHI TIẾT ĐƠN HÀNG</h1>
            <a href="quan-ly-don-hang.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="stats-wrapper">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($order['TongTien'], 0, ',', '.') ?>đ</div>
                    <div class="stat-label">Tổng tiền</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= array_sum(array_column($orderItems, 'SoLuong')) ?></div>
                    <div class="stat-label">Số lượng sản phẩm</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        <?php
                        $statusClass = match($order['MaTrangThai']) {
                            4 => 'success',
                            5 => 'success',
                            6 => 'danger',
                            default => 'warning'
                        };
                        ?>
                        <span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars($order['TenTrangThai'] ?? 'Đang xử lý') ?></span>
                    </div>
                    <div class="stat-label">Trạng thái hiện tại</div>
                </div>
            </div>
        </div>

        <div class="order-info-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-info-circle"></i> THÔNG TIN ĐƠN HÀNG</h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">Mã đơn hàng:</span>
                    <span class="info-value"><strong><?= htmlspecialchars($order['MaDonHang']) ?></strong></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ngày đặt:</span>
                    <span class="info-value"><?= date('d/m/Y H:i:s', strtotime($order['NgayDatHang'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Khách hàng:</span>
                    <span class="info-value"><?= htmlspecialchars($order['HoTen'] ?? 'Khách vãng lai') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($order['Email'] ?? 'Chưa có') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value"><?= htmlspecialchars($order['SoDienThoai'] ?? 'Chưa có') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Địa chỉ giao hàng:</span>
                    <span class="info-value"><?= nl2br(htmlspecialchars($order['DiaChiGiaoHang'] ?? 'Chưa có')) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phương thức thanh toán:</span>
                    <span class="info-value">
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
                <div class="info-item">
                    <span class="info-label">Phí ship:</span>
                    <span class="info-value"><?= number_format($order['PhiShip'], 0, ',', '.') ?>đ</span>
                </div>
                <?php if (!empty($order['HinhAnhTT'])): ?>
                <div class="info-item">
                    <span class="info-label">Ảnh thanh toán:</span>
                    <span class="info-value">
                        <img src="../uploads/transfer_proof/<?= htmlspecialchars($order['HinhAnhTT']) ?>" style="max-width:200px; border-radius:8px;">
                    </span>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <span class="info-label">Ghi chú:</span>
                    <span class="info-value"><?= nl2br(htmlspecialchars($order['GhiChu'] ?? 'Không có')) ?></span>
                </div>

                <hr class="my-4">
                <div class="status-update-section">
                    <form method="post" action="xu-ly-QLDH.php" class="status-update-form">
                        <input type="hidden" name="ma_don_hang" value="<?= $maDonHang ?>">
                        <div class="form-group">
                            <label>Chọn trạng thái mới</label>
                            <select name="trang_thai" class="form-select">
                                <?php foreach ($allStatuses as $st): ?>
                                    <option value="<?= $st['MaTrangThai'] ?>" <?= ($order['MaTrangThai'] == $st['MaTrangThai']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($st['TenTrangThai']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-update">Cập nhật trạng thái</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="order-items-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-boxes"></i> SẢN PHẨM TRONG ĐƠN</h5>
            </div>
            <div class="card-body">
                <?php if (count($orderItems) > 0): ?>
                    <div class="table-responsive">
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th style="text-align: center;">Size</th>
                                    <th style="text-align: center;">Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if (!empty($item['HinhAnh'])): ?>
                                            <img src="../img/<?= htmlspecialchars($item['HinhAnh']) ?>" class="product-image">
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['TenSanPham'] ?? 'Sản phẩm đã bị xóa') ?></td>
                                    <td style="text-align: center">
                                        <?php 
                                        $sizeValue = $item['size'] ?? '';
                                        if ($sizeValue === '0') {
                                            echo '<span class="text-muted">Không size</span>';
                                        } else {
                                            echo htmlspecialchars($sizeValue) ?: '<span class="text-muted">-</span>';
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align: center"><?= $item['SoLuong'] ?></td>
                                    <td><?= number_format($item['DonGia'], 0, ',', '.') ?>đ</td>
                                    <td><?= number_format($item['ThanhTien'], 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" style="text-align: right; font-weight: 600;">Tổng cộng:</td>
                                    <td style="font-weight: 600;"><?= number_format($order['TongTien'], 0, ',', '.') ?>đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">Không có sản phẩm nào trong đơn hàng này.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer_admin.php"; ?>