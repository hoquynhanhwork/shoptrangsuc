<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-reviews-page';
$current_page = 'quan-ly-danh-gia.php';

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

$countSql = "SELECT COUNT(*) as total 
             FROM danhgia dg
             LEFT JOIN sanpham sp ON dg.MaSanPham = sp.MaSanPham
             LEFT JOIN nguoidung nd ON dg.idUser = nd.idUser
             WHERE 1=1";
$countParams = [];
if (!empty($search)) {
    $countSql .= " AND (sp.TenSanPham LIKE :search OR nd.HoTen LIKE :search OR dg.NoiDung LIKE :search)";
    $search_param = "%$search%";
    $countParams[':search'] = $search_param;
}
$stmtCount = $conn->prepare($countSql);
foreach ($countParams as $key => $val) {
    $stmtCount->bindValue($key, $val);
}
$stmtCount->execute();
$totalReviews = $stmtCount->fetch()['total'];
$totalPages = ceil($totalReviews / $limit);

$sql = "SELECT dg.*, sp.TenSanPham, nd.HoTen
        FROM danhgia dg
        LEFT JOIN sanpham sp ON dg.MaSanPham = sp.MaSanPham
        LEFT JOIN nguoidung nd ON dg.idUser = nd.idUser
        WHERE 1=1";
$params = [];
if (!empty($search)) {
    $sql .= " AND (sp.TenSanPham LIKE :search OR nd.HoTen LIKE :search OR dg.NoiDung LIKE :search)";
    $params[':search'] = $search_param;
}
$sql .= " ORDER BY dg.NgayDanhGia DESC LIMIT :limit OFFSET :offset";
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
$reviews = $stmt->fetchAll();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <?php if ($message): ?>
            <?= $message ?>
        <?php endif; ?>

        <div class="admin-header-section">
            <div class="title-area">
                <h1><i class="fa-regular fa-star"></i> QUẢN LÝ ĐÁNH GIÁ</h1>
            </div>
            <div class="search-area">
                <form method="get" id="searchForm" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm kiếm..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </form>
            </div>
        </div>

        <div class="total-count-wrapper text-end mb-2">
            <span class="total-count-text">Tổng cộng: <strong><?= $totalReviews ?></strong> đánh giá</span>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
                            <th>Người dùng</th>
                            <th>Nội dung</th>
                            <th>Số sao</th>
                            <th>Ngày đánh giá</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                            <tr><td colspan="7" class="text-center py-4">Không có đánh giá nào.<?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?= $review['MaDanhGia'] ?></td>
                                <td><?= htmlspecialchars($review['TenSanPham'] ?? 'Sản phẩm đã xóa') ?></td>
                                <td><?= htmlspecialchars($review['HoTen'] ?? 'Người dùng đã xóa') ?></td>
                                <td class="review-content"><?= nl2br(htmlspecialchars($review['NoiDung'] ?: 'Không có nội dung')) ?></td>
                                <td class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-<?= $i <= $review['SoSao'] ? 'solid' : 'regular' ?> fa-star"></i>
                                    <?php endfor; ?>
                                 </td>
                                <td><?= date('d/m/Y H:i', strtotime($review['NgayDanhGia'])) ?></td>
                                <td>
                                    <a href="xu-ly-QLDG.php?action=delete&id=<?= $review['MaDanhGia'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Xóa đánh giá này?')" title="Xóa">
                                        <i class="fa-solid fa-trash"></i> Xóa
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
</script>

<?php include "../layout/footer_admin.php"; ?>