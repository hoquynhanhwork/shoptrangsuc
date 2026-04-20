<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-khachhang-page';
$current_page = 'quan-ly-khach-hang.php';

$message = '';
if (isset($_SESSION['admin_message'])) {
    $msg = $_SESSION['admin_message'];
    $message = '<div class="alert alert-' . $msg['type'] . ' alert-dismissible fade show" role="alert">'
        . htmlspecialchars($msg['text'])
        . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['admin_message']);
}

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) as total FROM nguoidung WHERE VaiTro = 'khachhang'";
$countParams = [];
if (!empty($search)) {
    $countSql .= " AND (HoTen LIKE :search1 OR Email LIKE :search2 OR SoDienThoai LIKE :search3 OR TenDangNhap LIKE :search4)";
    $search_param = "%$search%";
    $countParams = [
        ':search1' => $search_param,
        ':search2' => $search_param,
        ':search3' => $search_param,
        ':search4' => $search_param
    ];
}
$stmtCount = $conn->prepare($countSql);
foreach ($countParams as $key => $val) {
    $stmtCount->bindValue($key, $val);
}
$stmtCount->execute();
$totalCustomers = $stmtCount->fetch()['total'];
$totalPages = ceil($totalCustomers / $limit);

$sql = "SELECT * FROM nguoidung WHERE VaiTro = 'khachhang'";
$params = [];
if (!empty($search)) {
    $sql .= " AND (HoTen LIKE :search1 OR Email LIKE :search2 OR SoDienThoai LIKE :search3 OR TenDangNhap LIKE :search4)";
    $params = [
        ':search1' => $search_param,
        ':search2' => $search_param,
        ':search3' => $search_param,
        ':search4' => $search_param
    ];
}
$sql .= " ORDER BY NgayTao DESC LIMIT :limit OFFSET :offset";
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
$customers = $stmt->fetchAll();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <div class="admin-header-section">
            <div class="title-area">
                <h1><i class="fa-solid fa-users"></i> QUẢN LÝ KHÁCH HÀNG</h1>
            </div>

            <div class="search-area">
                <form method="get" id="searchForm">
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
            <span class="total-count-text">Tổng cộng: <strong><?= $totalCustomers ?></strong> khách hàng</span>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>STT</th>                            
                            <th>Tên khách hàng</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="7" class="text-center py-4">Không có khách hàng nào.<?php else: ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td style="text-align: center;"><?= $stt++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($customer['HoTen']) ?></td>
                                <td><?= htmlspecialchars($customer['SoDienThoai'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($customer['Email']) ?></td>
                                <td><?= htmlspecialchars($customer['DiaChi'] ?? '-') ?></td>
                                <td>
                                    <?= $customer['TrangThai'] == 1 
                                        ? '<span class="status active">Hoạt động</span>' 
                                        : '<span class="status inactive">Khóa</span>' ?>
                                </td>
                                <td class="action-icons">
                                    <a href="chi-tiet-khach-hang.php?id=<?= $customer['idUser'] ?>" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <?php if ($customer['TrangThai'] == 1): ?>
                                        <a href="xu-ly-QLKH.php?action=lock&id=<?= $customer['idUser'] ?>" 
                                           onclick="return confirm('Khóa tài khoản này?')" title="Khóa">
                                            <i class="fa-solid fa-lock"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="xu-ly-QLKH.php?action=unlock&id=<?= $customer['idUser'] ?>" 
                                           onclick="return confirm('Mở khóa tài khoản này?')" title="Mở khóa">
                                            <i class="fa-solid fa-unlock"></i>
                                        </a>
                                    <?php endif; ?>
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
                    <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">«</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">»</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include "../layout/footer_admin.php"; ?>