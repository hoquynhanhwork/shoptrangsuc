<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-sanpham-page';
$current_page = 'quan-ly-san-pham.php';

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

$sort_price = $_GET['sort_price'] ?? '';
$valid_sort = ['asc', 'desc'];
if (!in_array($sort_price, $valid_sort)) {
    $sort_price = '';
}

$filter_category = isset($_GET['category']) ? intval($_GET['category']) : 0;

$stmtCats = $conn->query("SELECT MaDanhMuc, TenDanhMuc FROM danhmuc WHERE TrangThai = 1 ORDER BY TenDanhMuc");
$categories = $stmtCats->fetchAll();

$countSql = "SELECT COUNT(*) as total 
             FROM sanpham sp 
             LEFT JOIN danhmuc dm ON sp.MaDanhMuc = dm.MaDanhMuc 
             WHERE 1=1";
$countParams = [];
if (!empty($search)) {
    $countSql .= " AND (sp.TenSanPham LIKE :search OR dm.TenDanhMuc LIKE :search)";
    $search_param = "%$search%";
    $countParams[':search'] = $search_param;
}
if ($filter_category > 0) {
    $countSql .= " AND sp.MaDanhMuc = :category";
    $countParams[':category'] = $filter_category;
}
$stmtCount = $conn->prepare($countSql);
foreach ($countParams as $key => $val) {
    $stmtCount->bindValue($key, $val);
}
$stmtCount->execute();
$totalProducts = $stmtCount->fetch()['total'];
$totalPages = ceil($totalProducts / $limit);

$sql = "SELECT sp.*, dm.TenDanhMuc,
        (SELECT SUM(sz.SoLuongTon)
         FROM size sz
         WHERE sz.MaSanPham = sp.MaSanPham) AS TonKho
        FROM sanpham sp
        LEFT JOIN danhmuc dm ON sp.MaDanhMuc = dm.MaDanhMuc
        WHERE 1=1";
$params = [];
if (!empty($search)) {
    $sql .= " AND (sp.TenSanPham LIKE :search OR dm.TenDanhMuc LIKE :search)";
    $params[':search'] = $search_param;
}
if ($filter_category > 0) {
    $sql .= " AND sp.MaDanhMuc = :category";
    $params[':category'] = $filter_category;
}
if ($sort_price == 'asc') {
    $sql .= " ORDER BY sp.Gia ASC";
} elseif ($sort_price == 'desc') {
    $sql .= " ORDER BY sp.Gia DESC";
} else {
    $sql .= " ORDER BY sp.MaSanPham DESC";
}
$sql .= " LIMIT :limit OFFSET :offset";
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
$products = $stmt->fetchAll();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <?php if ($message): ?>
            <?= $message ?>
        <?php endif; ?>
        <div class="admin-header-section">
            <div class="title-area">
                <h1><i class="fa-regular fa-gem"></i> QUẢN LÝ SẢN PHẨM</h1>
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
                <a href="form-san-pham.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                </a>
            </div>
        </div>

        <div class="total-count-wrapper text-end mb-2">
            <span class="total-count-text">Tổng cộng: <strong><?= $totalProducts ?></strong> sản phẩm</span>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                           <th>
                                Danh mục
                                <span class="filter-category-icon ms-1" style="cursor: pointer; display: inline-block;">
                                    <i class="fa-solid fa-filter"></i>
                                </span>
                                <div class="category-dropdown-menu" style="text-align: center; display: none; position: absolute; background: white; border: 1px solid #ccc; border-radius: 8px; width: 160px; height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">                                    <a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['category' => 0, 'page' => 1])) ?>" style="display: block; padding: 6px 16px; text-decoration: none; color: #212737;">Tất cả</a>
                                    <?php foreach ($categories as $cat): ?>
                                    <a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['category' => $cat['MaDanhMuc'], 'page' => 1])) ?>" style="display: block; padding: 6px 16px; text-decoration: none; color: #212737;"><?= htmlspecialchars($cat['TenDanhMuc']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </th>
                            <th class="text-end">Tồn kho</th>
                            <th class="text-end">
                                Giá bán
                                <a href="?<?= http_build_query(array_merge($_GET, ['sort_price' => ($sort_price == 'asc' ? 'desc' : 'asc'), 'page' => 1])) ?>" class="ms-1 text-decoration-none">
                                    <?php if ($sort_price == 'asc'): ?>
                                        <i class="fa-solid fa-arrow-up-wide-short"></i>
                                    <?php elseif ($sort_price == 'desc'): ?>
                                        <i class="fa-solid fa-arrow-down-wide-short"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-sort"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="8" class="text-center py-4">Không có sản phẩm nào.</td>
                        <?php else: ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td style="text-align: center;"><?= $stt++ ?></td>
                                <td style="text-align: center;">
                                    <?php if (!empty($product['HinhAnh'])): ?>
                                        <img src="../img/<?= htmlspecialchars($product['HinhAnh']) ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($product['TenSanPham']) ?></td>
                                <td><?= htmlspecialchars($product['TenDanhMuc'] ?? '-') ?></td>
                                <td style="text-align: right;"><?= number_format($product['TonKho'] ?? 0) ?></td>
                                <td style="text-align: right; white-space: nowrap;"><?= number_format($product['Gia'] ?? 0, 0, ',', '.') ?>đ</td>
                                <td style="text-align: center;">
                                    <?php if ($product['TrangThai'] == 1): ?>
                                        <span class="status active">Đang hoạt động</span>
                                    <?php else: ?>
                                        <span class="status inactive">Ngừng hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-icons">
                                    <a href="form-san-pham.php?id=<?= $product['MaSanPham'] ?>" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="xu-ly-QLSP.php?action=delete&id=<?= $product['MaSanPham'] ?>" 
                                    onclick="return confirm('Xóa sản phẩm này?')" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
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
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page-1])) ?>">«</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page+1])) ?>">»</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelector('#searchForm input[name="search"]')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const filterIcon = document.querySelector('.filter-category-icon');
        const dropdownMenu = document.querySelector('.category-dropdown-menu');
        
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
            
            dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    dropdownMenu.style.display = 'none';
                });
            });
        }
    });
</script>

<?php include "../layout/footer_admin.php"; ?>