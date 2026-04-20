<?php
$bodyClass = 'product-page';
require_once "../config.php";
include "../layout/header_public.php";

$keyword = trim($_GET['keyword'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$count_sql = "SELECT COUNT(*) as total FROM sanpham WHERE TenSanPham LIKE :keyword AND DelAt = 0";
$stmt = $conn->prepare($count_sql);
$stmt->execute([':keyword' => "%$keyword%"]);
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / $limit);

$sql = "SELECT * FROM sanpham WHERE TenSanPham LIKE :keyword AND DelAt = 0 ORDER BY MaSanPham DESC LIMIT $offset, $limit";
$stmt = $conn->prepare($sql);
$stmt->execute([':keyword' => "%$keyword%"]);
$products = $stmt->fetchAll();
?>

<div class="container py-4">
    <h2 class="mb-4">Kết quả tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</h2>
    
    <?php if (empty($keyword)): ?>
        <p class="text-center">Vui lòng nhập từ khóa tìm kiếm.</p>
    <?php elseif ($products): ?>
        <div class="row g-3">
            <?php foreach ($products as $sp): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="chi-tiet-san-pham.php?id=<?= $sp['MaSanPham'] ?>" class="text-decoration-none text-dark">
                        <div class="product-card">
                            <img src="../img/<?= htmlspecialchars($sp['HinhAnh']) ?>" class="product-img">
                            <div class="product-info">
                                <h6><?= htmlspecialchars($sp['TenSanPham']) ?></h6>
                                <p class="price"><?= number_format($sp['Gia'], 0, ',', '.') ?> đ</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page-1 ?>">❮</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page+1 ?>">❯</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
    <?php else: ?>
        <p class="text-center">Không tìm thấy sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>".</p>
    <?php endif; ?>
</div>

<?php include "../layout/footer_public.php"; ?>