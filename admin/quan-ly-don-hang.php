<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-donhang-page';
$current_page = 'quan-ly-don-hang.php';

$message = '';
if (isset($_SESSION['admin_message'])) {
    $msg = $_SESSION['admin_message'];
    $message = '<div class="alert alert-' . $msg['type'] . ' alert-dismissible fade show" role="alert">'
        . htmlspecialchars($msg['text'])
        . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['admin_message']);
}

$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) as total 
             FROM donhang dh
             LEFT JOIN nguoidung nd ON dh.idUser = nd.idUser
             WHERE 1=1";
$countParams = [];
if (!empty($search)) {
    $countSql .= " AND (dh.MaDonHang LIKE :search OR nd.HoTen LIKE :search)";
    $search_param = "%$search%";
    $countParams[':search'] = $search_param;
}
if (!empty($status_filter)) {
    $countSql .= " AND dh.MaTrangThai = :status";
    $countParams[':status'] = $status_filter;
}

$stmtCount = $conn->prepare($countSql);
foreach ($countParams as $key => $val) {
    $stmtCount->bindValue($key, $val);
}
$stmtCount->execute();
$totalOrders = $stmtCount->fetch()['total'];
$totalPages = ceil($totalOrders / $limit);

$sql = "SELECT dh.*, nd.HoTen, tt.TenTrangThai
        FROM donhang dh
        LEFT JOIN nguoidung nd ON dh.idUser = nd.idUser
        LEFT JOIN trangthaidonhang tt ON dh.MaTrangThai = tt.MaTrangThai
        WHERE 1=1";
$params = [];
if (!empty($search)) {
    $sql .= " AND (dh.MaDonHang LIKE :search OR nd.HoTen LIKE :search)";
    $params[':search'] = $search_param;
}
if (!empty($status_filter)) {
    $sql .= " AND dh.MaTrangThai = :status";
    $params[':status'] = $status_filter;
}
$sql .= " ORDER BY dh.NgayDatHang DESC LIMIT :limit OFFSET :offset";
$params[':limit'] = $limit;
$params[':offset'] = $offset;

$stmt = $conn->prepare($sql);
foreach ($params as $key => $val) {
    if ($key == ':limit' || $key == ':offset') {
        $stmt->bindValue($key, $val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
}
$stmt->execute();
$orders = $stmt->fetchAll();

$stmtStatus = $conn->query("SELECT MaTrangThai, TenTrangThai FROM trangthaidonhang ORDER BY MaTrangThai");
$allStatuses = $stmtStatus->fetchAll();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <?php if ($message): ?>
            <?= $message ?>
        <?php endif; ?>
        <div class="admin-header-section">
            <div class="title-area">
                <h1><i class="fa-solid fa-truck-fast"></i> QUẢN LÝ ĐƠN HÀNG</h1>
            </div>
            <div class="search-area">
                <form method="get" id="searchForm" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm kiếm..."
                            value="<?= htmlspecialchars($search) ?>">
                    </div>
                </form>
            </div>
        </div>

        <div class="total-count-wrapper text-end mb-2">
            <span class="total-count-text">Tổng cộng: <strong><?= $totalOrders ?></strong> đơn hàng</span>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Phương thức</th>
                            <th>
                                Trạng thái
                                <span class="filter-status-icon ms-1">
                                    <i class="fa-solid fa-filter"></i>
                                </span>
                                <div class="status-dropdown-menu">
                                    <a href="?<?= http_build_query(array_merge($_GET, ['status' => '', 'page' => 1])) ?>">Tất cả</a>
                                    <?php foreach ($allStatuses as $st): ?>
                                        <a href="?<?= http_build_query(array_merge($_GET, ['status' => $st['MaTrangThai'], 'page' => 1])) ?>">
                                            <?= htmlspecialchars($st['TenTrangThai']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="8" class="text-center py-4">Không có đơn hàng nào.<?php else: ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td style="text-align: center;"><?= $stt++ ?></td>
                                <td style="text-align: center;"><strong><?= htmlspecialchars($order['MaDonHang']) ?></strong></td>
                                <td><?= htmlspecialchars($order['HoTen'] ?? 'Khách vãng lai') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['NgayDatHang'])) ?></td>
                                <td style="text-align: right;"><?= number_format($order['TongTien'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php
                                    switch ($order['PhuongThucThanhToan']) {
                                        case 'cod': echo 'COD'; break;
                                        case 'momo': echo 'MoMo'; break;
                                        case 'bank': echo 'Chuyển khoản'; break;
                                        default: echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($order['MaTrangThai']) {
                                        4 => 'success',
                                        5 => 'success',
                                        6 => 'danger',
                                        default => 'warning'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= htmlspecialchars($order['TenTrangThai'] ?? 'Đang xử lý') ?>
                                    </span>
                                </td>
                                <td class="action-icons">
                                    <a href="chi-tiet-don-hang.php?id=<?= $order['MaDonHang'] ?>" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">«</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">»</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
let searchTimeout;
const searchInput = document.querySelector('#searchForm input[name="search"]');
const searchForm = document.getElementById('searchForm');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => searchForm.submit(), 500);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const filterIcon = document.querySelector('.filter-status-icon');
    const dropdownMenu = document.querySelector('.status-dropdown-menu');
    
    if (filterIcon && dropdownMenu) {
        filterIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = dropdownMenu.style.display === 'block';
            dropdownMenu.style.display = isVisible ? 'none' : 'block';
        });
        
        document.addEventListener('click', function(e) {
            if (!filterIcon.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
        
        dropdownMenu.querySelectorAll('a').forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.style.display = 'none';
            });
        });
    }
});
</script>

<?php include "../layout/footer_admin.php"; ?>