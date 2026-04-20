<?php
$bodyClass = 'product-page';
require_once "../config.php";
include "../layout/header_public.php";

$madanhmuc = $_GET['madanhmuc'] ?? '';
$price_range = $_GET['price_range'] ?? '';
$keyword = trim($_GET['keyword'] ?? '');

$limit = 12;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$count_sql = "SELECT COUNT(*) as total FROM sanpham sp WHERE 1=1";
$params = [];

if ($madanhmuc != '') {
    $count_sql .= " AND sp.MaDanhMuc = :madanhmuc";
    $params[':madanhmuc'] = $madanhmuc;
}

if ($price_range != '') {
    switch ($price_range) {
        case '0-500000':
            $count_sql .= " AND sp.Gia < 500000";
            break;
        case '500000-1000000':
            $count_sql .= " AND sp.Gia BETWEEN 500000 AND 1000000";
            break;
        case '1000000-2000000':
            $count_sql .= " AND sp.Gia BETWEEN 1000000 AND 2000000";
            break;
        case '2000000+':
            $count_sql .= " AND sp.Gia >= 2000000";
            break;
    }
}

if ($keyword != '') {
    $count_sql .= " AND sp.TenSanPham LIKE :keyword";
    $params[':keyword'] = "%$keyword%";
}

$stmt = $conn->prepare($count_sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / $limit);

$sql = "
    SELECT sp.*, sp.Gia as gia,
        CASE 
            WHEN EXISTS (SELECT 1 FROM size s WHERE s.MaSanPham = sp.MaSanPham AND s.size != '0') THEN
                EXISTS (SELECT 1 FROM size s WHERE s.MaSanPham = sp.MaSanPham AND s.size != '0' AND s.SoLuongTon > 0)
            ELSE
                EXISTS (SELECT 1 FROM size s WHERE s.MaSanPham = sp.MaSanPham AND s.size = '0' AND s.SoLuongTon > 0)
        END as has_stock
    FROM sanpham sp
    WHERE 1=1
";

if ($madanhmuc != '') $sql .= " AND sp.MaDanhMuc = :madanhmuc";
if ($price_range != '') {
    switch ($price_range) {
        case '0-500000':
            $sql .= " AND sp.Gia < 500000";
            break;
        case '500000-1000000':
            $sql .= " AND sp.Gia BETWEEN 500000 AND 1000000";
            break;
        case '1000000-2000000':
            $sql .= " AND sp.Gia BETWEEN 1000000 AND 2000000";
            break;
        case '2000000+':
            $sql .= " AND sp.Gia >= 2000000";
            break;
    }
}
if ($keyword != '') $sql .= " AND sp.TenSanPham LIKE :keyword";

$sql .= " ORDER BY sp.MaSanPham DESC LIMIT $offset, $limit";

$stmt = $conn->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$products = $stmt->fetchAll();
?>
<div class="container py-4">
    <div class="filter-bar d-flex flex-wrap align-items-center gap-3 mb-4">
        <select id="filter-category" class="form-select form-select-sm w-auto">
            <option value="">Tất cả danh mục</option>
            <?php
            $danhmucs = [
                '1' => 'Nhẫn', '2' => 'Vòng tay', '3' => 'Dây chuyền',
                '4' => 'Mặt dây chuyền', '5' => 'Hoa tai'
            ];
            foreach ($danhmucs as $id => $name):
                ?>
                <option value="<?= $id ?>" <?= ($madanhmuc == $id) ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>

        <select id="filter-price" class="form-select form-select-sm w-auto">
            <option value="">Tất cả giá</option>
            <option value="0-500000" <?= ($price_range == '0-500000') ? 'selected' : '' ?>>Dưới 500.000₫</option>
            <option value="500000-1000000" <?= ($price_range == '500000-1000000') ? 'selected' : '' ?>>500.000₫ - 1.000.000₫</option>
            <option value="1000000-2000000" <?= ($price_range == '1000000-2000000') ? 'selected' : '' ?>>1.000.000₫ - 2.000.000₫</option>
            <option value="2000000+" <?= ($price_range == '2000000+') ? 'selected' : '' ?>>Trên 2.000.000₫</option>
        </select>

        <button id="apply-filter" class="btn btn-sm apply-btn">Áp dụng</button>
    </div>

    <div class="row g-4">
        <?php if ($products): ?>
            <?php foreach ($products as $sp): 
                $isOutOfStock = !$sp['has_stock'];
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="chi-tiet-san-pham.php?id=<?= $sp['MaSanPham'] ?>" class="text-decoration-none text-dark">
                        <div class="product-card position-relative">
                            <img src="../img/<?= htmlspecialchars($sp['HinhAnh']) ?>" class="product-img" alt="<?= htmlspecialchars($sp['TenSanPham']) ?>">
                            <?php if ($isOutOfStock): ?>
                                <div class="out-of-stock-badge">Hết hàng</div>
                            <?php endif; ?>
                            <div class="product-info">
                                <h6><?= htmlspecialchars($sp['TenSanPham']) ?></h6>
                                <p class="price"><?= number_format($sp['gia'] ?? 0, 0, ',', '.') ?> đ</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Không có sản phẩm nào phù hợp.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="?page=<?= $page - 1 ?>&madanhmuc=<?= urlencode($madanhmuc) ?>&price_range=<?= urlencode($price_range) ?>&keyword=<?= urlencode($keyword) ?>">
                        ❮
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link"
                           href="?page=<?= $i ?>&madanhmuc=<?= urlencode($madanhmuc) ?>&price_range=<?= urlencode($price_range) ?>&keyword=<?= urlencode($keyword) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="?page=<?= $page + 1 ?>&madanhmuc=<?= urlencode($madanhmuc) ?>&price_range=<?= urlencode($price_range) ?>&keyword=<?= urlencode($keyword) ?>">
                        ❯
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<script>
    document.getElementById('apply-filter').addEventListener('click', function () {
        const cat = document.getElementById('filter-category').value;
        const price = document.getElementById('filter-price').value;
        let keyword = '<?= htmlspecialchars($keyword) ?>';
        let url = '?madanhmuc=' + encodeURIComponent(cat) + 
                  '&price_range=' + encodeURIComponent(price) + 
                  '&keyword=' + encodeURIComponent(keyword);
        window.location.href = url;
    });
</script>
<?php include "../layout/footer_public.php"; ?>