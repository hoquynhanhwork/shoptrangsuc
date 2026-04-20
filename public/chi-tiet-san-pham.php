<?php
session_start();
require_once "../config.php"; 

$bodyClass = 'product-page';
$product_id = intval($_GET['id'] ?? 0);

$reviewSuccess = isset($_GET['review']) && $_GET['review'] === 'success';
$reviewMessage = isset($_SESSION['review_error']) ? $_SESSION['review_error'] : '';
unset($_SESSION['review_error']);

$stmt = $conn->prepare("
    SELECT * FROM sanpham 
    WHERE MaSanPham = :id AND DelAt = 0
");
$stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<p>Sản phẩm không tồn tại hoặc đã bị xóa.</p>";
    exit;
}

$categoryName = '';
if ($product['MaDanhMuc']) {
    $stmtCat = $conn->prepare("SELECT TenDanhMuc FROM danhmuc WHERE MaDanhMuc = :id");
    $stmtCat->bindValue(':id', $product['MaDanhMuc'], PDO::PARAM_INT);
    $stmtCat->execute();
    $cat = $stmtCat->fetch(PDO::FETCH_ASSOC);
    $categoryName = $cat ? $cat['TenDanhMuc'] : '';
}

$sizes = [];
$stmtSize = $conn->prepare("SELECT id, size, SoLuongTon FROM size WHERE MaSanPham = :id ORDER BY size");
$stmtSize->bindValue(':id', $product_id, PDO::PARAM_INT);
$stmtSize->execute();
$sizes = $stmtSize->fetchAll(PDO::FETCH_ASSOC);

$hasRealSize = false;
$stockForNoSize = 0;
foreach ($sizes as $sz) {
    if ($sz['size'] != '0') {
        $hasRealSize = true;
    } else {
        $stockForNoSize = $sz['SoLuongTon'];
    }
}
$hasSize = $hasRealSize;

$hasStock = false;
if ($hasRealSize) {
    foreach ($sizes as $sz) {
        if ($sz['SoLuongTon'] > 0 && $sz['size'] != '0') {
            $hasStock = true;
            break;
        }
    }
} else {
    $hasStock = ($stockForNoSize > 0);
}

$product_price = floatval($product['Gia'] ?? 0);

$limit_review = 4;
$page_review = max(1, intval($_GET['page_review'] ?? 1));
$offset_review = ($page_review - 1) * $limit_review;
$stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM danhgia WHERE MaSanPham = :id");
$stmtCount->execute([':id' => $product_id]);
$totalReviews = $stmtCount->fetch()['total'];
$total_page_review = ceil($totalReviews / $limit_review);

$stmtReview = $conn->prepare("
    SELECT d.*, u.HoTen 
    FROM danhgia d
    JOIN nguoidung u ON d.idUser = u.idUser
    WHERE d.MaSanPham = :id
    ORDER BY d.NgayDanhGia DESC
    LIMIT :offset, :limit
");
$stmtReview->bindValue(':id', $product_id, PDO::PARAM_INT);
$stmtReview->bindValue(':offset', $offset_review, PDO::PARAM_INT);
$stmtReview->bindValue(':limit', $limit_review, PDO::PARAM_INT);
$stmtReview->execute();
$reviews = $stmtReview->fetchAll(PDO::FETCH_ASSOC);

include "../layout/header_public.php";

$stmtRelated = $conn->prepare("
    SELECT MaSanPham, TenSanPham, HinhAnh, Gia as gia 
    FROM sanpham 
    WHERE MaDanhMuc = :madm AND MaSanPham != :id AND DelAt = 0
    ORDER BY MaSanPham DESC
");
$stmtRelated->execute([
    ':madm' => $product['MaDanhMuc'],
    ':id' => $product_id
]);
$related_products = $stmtRelated->fetchAll();

if (!isset($_SESSION['viewed_products'])) {
    $_SESSION['viewed_products'] = [];
}
if (($key = array_search($product_id, $_SESSION['viewed_products'])) !== false) {
    unset($_SESSION['viewed_products'][$key]);
}
array_unshift($_SESSION['viewed_products'], $product_id);
$viewed_products = [];
if (!empty($_SESSION['viewed_products'])) {
    $ids = $_SESSION['viewed_products'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmtViewed = $conn->prepare("
        SELECT MaSanPham, TenSanPham, HinhAnh, Gia as gia
        FROM sanpham 
        WHERE MaSanPham IN ($placeholders) AND DelAt = 0
        ORDER BY FIELD(MaSanPham, $placeholders)
    ");
    $stmtViewed->execute(array_merge($ids, $ids));
    $viewed_products = $stmtViewed->fetchAll();
}
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="san-pham.php">Sản phẩm</a></li>
            <?php if ($categoryName): ?>
            <li class="breadcrumb-item"><a href="san-pham.php?madanhmuc=<?= $product['MaDanhMuc'] ?>"><?= htmlspecialchars($categoryName) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['TenSanPham']) ?></li>
        </ol>
    </nav>

    <?php if ($reviewMessage): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($reviewMessage) ?></div>
    <?php endif; ?>
    <?php if ($reviewSuccess): ?>
        <div class="alert alert-success">Cảm ơn bạn đã đánh giá sản phẩm!</div>
    <?php endif; ?>

    <div class="row g-4 align-items-start">
        <div class="col-md-6 text-center">
            <img src="../img/<?= htmlspecialchars($product['HinhAnh'] ?? '') ?>" 
                 alt="<?= htmlspecialchars($product['TenSanPham'] ?? '') ?>" 
                 class="img-fluid product-main-img rounded">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['TenSanPham'] ?? '') ?></h2>
            <div class="product-short-desc mt-2">
                <?= nl2br(htmlspecialchars($product['MoTa'] ?? '')) ?>
            </div>
            <p class="price product-detail-price"><?= number_format($product_price, 0, ',', '.') ?> đ</p>            

            <?php if ($hasSize): ?>
            <div class="mb-3">
                <label class="form-label">Vui lòng chọn size :</label>
                <div class="d-flex flex-wrap gap-2" id="size-buttons">
                    <?php foreach ($sizes as $sz): ?>
                        <?php if ($sz['size'] != '0'): ?>
                            <?php if ($sz['SoLuongTon'] > 0): ?>
                            <button type="button" class="btn btn-outline-secondary size-option" 
                                    data-size="<?= $sz['size'] ?>" data-stock="<?= $sz['SoLuongTon'] ?>" data-id-size="<?= $sz['id'] ?>">
                                <?= $sz['size'] ?>
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-outline-secondary size-option out-of-stock" disabled>
                                <?= $sz['size'] ?>
                            </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="selected-size" value="">
                <input type="hidden" id="selected-id-size" value="">
                <a href="huong-dan-do-kich-thuoc.php" class="size-guide-link mt-2 d-block">
                    📏 Hướng dẫn chọn size
                </a>
            </div>
            <?php else: ?>
            <?php 
                $id_size_0 = 0;
                foreach ($sizes as $sz) {
                    if ($sz['size'] == '0') {
                        $id_size_0 = $sz['id'];
                        break;
                    }
                }
            ?>
            <input type="hidden" id="selected-size" value="0">
            <input type="hidden" id="selected-id-size" value="<?= $id_size_0 ?>">
            <?php endif; ?>
                
            <div class="quantity-box">
                <span>Số lượng:</span>
                <div class="qty-control">
                    <button type="button" class="qty-btn minus">−</button>
                    <input type="text" id="quantity" value="1">
                    <button type="button" class="qty-btn plus">+</button>
                </div>
                <span id="stock-status-text" class="stock-text"></span>
            </div>

            <div class="d-flex gap-3 mb-3 align-items-stretch">
                <?php if ($hasStock): ?>
                <button class="btn btn-success btn-buy-now flex-fill" onclick="buyNow()">
                    <span class="buy-now-title">MUA NGAY</span>
                    <small class="buy-now-desc d-block">Giao nhanh từ 2 giờ hoặc nhận tại cửa hàng</small>
                </button>
                <button class="btn btn-warning btn-add-cart flex-fill" onclick="addToCart()">
                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                </button>
                <?php else: ?>
                <button class="btn btn-secondary flex-fill" disabled>
                    <i class="bi bi-x-circle"></i> Hết hàng
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="product-description mt-4">
        <h4>📄 Chi tiết sản phẩm</h4>
        <div class="desc-content">
            <?= nl2br(htmlspecialchars($product['ChiTietSanPham'] ?? '')) ?>
        </div>
    </div>

    <div id="reviews-section" class="product-review mt-4 mb-5">
        <h4>Đánh giá sản phẩm (<?= $totalReviews ?>)</h4>
        <div id="reviews-list">
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="single-review border-bottom mb-2 pb-2">
                        <p><strong><?= htmlspecialchars($r['HoTen'] ?? 'Khách') ?></strong> - <?= date('d/m/Y H:i', strtotime($r['NgayDanhGia'] ?? 'now')) ?></p>
                        <p class="rating">
                            <?= str_repeat('<span class="star filled">★</span>', $r['SoSao'] ?? 0) ?>
                            <?= str_repeat('<span class="star">★</span>', 5 - ($r['SoSao'] ?? 0)) ?>
                        </p>
                        <p><?= nl2br(htmlspecialchars($r['NoiDung'] ?? '')) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có đánh giá nào.</p>
            <?php endif; ?>
        </div>

        <?php if ($total_page_review > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page_review <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?id=<?= $product_id ?>&page_review=<?= $page_review-1 ?>#reviews-section">❮</a>
                    </li>
                    <?php for ($i=1; $i<=$total_page_review; $i++): ?>
                        <li class="page-item <?= ($i==$page_review)?'active':'' ?>">
                            <a class="page-link" href="?id=<?= $product_id ?>&page_review=<?= $i ?>#reviews-section"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page_review>=$total_page_review)?'disabled':'' ?>">
                        <a class="page-link" href="?id=<?= $product_id ?>&page_review=<?= $page_review+1 ?>#reviews-section">❯</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <?php if (isset($_SESSION['nguoidung'])): ?>
            <button id="write-review-btn" class="btn btn-gradient mt-3">✍️ Viết đánh giá</button>
            <div id="review-form-overlay">
                <div id="review-form">
                    <form method="post" action="xu-ly-danh-gia.php">
                        <input type="hidden" name="product_id" value="<?= $product_id ?>">
                        <h4 class="mb-3 text-center">Đánh giá sản phẩm</h4>
                        <div class="mb-3">
                            <label>Số sao</label>
                            <select name="sosao" class="form-select input-custom w-50">
                                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                <option value="4">⭐⭐⭐⭐ (4)</option>
                                <option value="3">⭐⭐⭐ (3)</option>
                                <option value="2">⭐⭐ (2)</option>
                                <option value="1">⭐ (1)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nội dung</label>
                            <textarea name="noidung" class="form-control input-custom" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="close-review" class="btn btn-light">Đóng</button>
                            <button type="submit" class="btn btn-success px-4">Gửi</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-danger mt-3">Bạn cần <a href="dang-nhap.php">đăng nhập</a> để đánh giá sản phẩm.</p>
        <?php endif; ?>
    </div>

    <?php if ($related_products): ?>
        <div class="mt-5">
            <h4>Sản phẩm tương tự</h4>
            <div class="slider-wrapper">
                <?php if (count($related_products) > 4): ?>
                    <button class="slide-btn left" onclick="slideLeft('related')">❮</button>
                <?php endif; ?>
                <div class="slider" id="related-slider">
                    <?php foreach ($related_products as $sp): ?>
                        <div class="product-card">
                            <a href="chi-tiet-san-pham.php?id=<?= $sp['MaSanPham'] ?>" class="text-decoration-none text-dark">
                                <img src="../img/<?= htmlspecialchars($sp['HinhAnh'] ?? '') ?>" class="product-img" alt="<?= htmlspecialchars($sp['TenSanPham'] ?? '') ?>">
                                <div class="product-info">
                                    <h6><?= htmlspecialchars($sp['TenSanPham'] ?? '') ?></h6>
                                    <p class="price"><?= number_format($sp['gia'] ?? 0, 0, ',', '.') ?> đ</p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($related_products) > 4): ?>
                    <button class="slide-btn right" onclick="slideRight('related')">❯</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($viewed_products): ?>
        <div class="mt-5">
            <h4>Sản phẩm đã xem</h4>
            <div class="slider-wrapper">
                <?php if (count($viewed_products) > 4): ?>
                    <button class="slide-btn left" onclick="slideLeft('viewed')">❮</button>
                <?php endif; ?>
                <div class="slider" id="viewed-slider">
                    <?php foreach ($viewed_products as $sp): ?>
                        <div class="product-card">
                            <a href="chi-tiet-san-pham.php?id=<?= $sp['MaSanPham'] ?>" class="text-decoration-none text-dark">
                                <img src="../img/<?= htmlspecialchars($sp['HinhAnh'] ?? '') ?>" class="product-img" alt="<?= htmlspecialchars($sp['TenSanPham'] ?? '') ?>">
                                <div class="product-info">
                                    <h6><?= htmlspecialchars($sp['TenSanPham'] ?? '') ?></h6>
                                    <p class="price"><?= number_format($sp['gia'] ?? 0, 0, ',', '.') ?> đ</p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($viewed_products) > 4): ?>
                    <button class="slide-btn right" onclick="slideRight('viewed')">❯</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
const openBtn = document.getElementById("write-review-btn");
const overlay = document.getElementById("review-form-overlay");
const closeBtn = document.getElementById("close-review");
if (openBtn) openBtn.onclick = () => overlay.style.display = "flex";
if (closeBtn) closeBtn.onclick = () => overlay.style.display = "none";
if (overlay) overlay.onclick = (e) => { if (e.target === overlay) overlay.style.display = "none"; };

const sizeButtons = document.querySelectorAll('.size-option:not(.disabled)');
const selectedSizeInput = document.getElementById('selected-size');
const selectedIdSizeInput = document.getElementById('selected-id-size');
const qtyInput = document.getElementById('quantity');
let currentMaxStock = 0;
let currentIdSize = 0;

function updateStockDisplay(sizeVal, idSize) {
    const selectedBtn = Array.from(sizeButtons).find(btn => btn.getAttribute('data-size') == sizeVal);
    const stockSpan = document.getElementById('stock-status-text');
    if (!stockSpan) return;

    if (selectedBtn) {
        let stock = parseInt(selectedBtn.getAttribute('data-stock')) || 0;
        if (stock <= 0) {
            stockSpan.innerHTML = 'Hết hàng';
            stockSpan.className = 'stock-text text-danger';
        } else if (stock <= 5) {
            stockSpan.innerHTML = 'Sắp hết hàng (còn ' + stock + ' sản phẩm)';
            stockSpan.className = 'stock-text text-warning';
        } else {
            stockSpan.innerHTML = 'Còn hàng';
            stockSpan.className = 'stock-text text-success';
        }
    } else {
        stockSpan.innerHTML = '';
    }
}

function updateMaxStockForSize(sizeVal, idSize) {
    const selectedBtn = Array.from(sizeButtons).find(btn => btn.getAttribute('data-size') == sizeVal);
    if (selectedBtn) {
        currentMaxStock = parseInt(selectedBtn.getAttribute('data-stock')) || 0;
        currentIdSize = idSize;
        qtyInput.max = currentMaxStock;
        let currentQty = parseInt(qtyInput.value);
        if (currentQty > currentMaxStock) qtyInput.value = currentMaxStock;
        const minusBtn = document.querySelector('.minus');
        const plusBtn = document.querySelector('.plus');
        if (plusBtn) plusBtn.disabled = (currentMaxStock <= 0);
        if (minusBtn) minusBtn.disabled = false;
        updateStockDisplay(sizeVal, idSize);
    }
}

if (sizeButtons.length > 0) {
    sizeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            sizeButtons.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');
            const size = this.getAttribute('data-size');
            const idSize = this.getAttribute('data-id-size');
            if (selectedSizeInput) selectedSizeInput.value = size;
            if (selectedIdSizeInput) selectedIdSizeInput.value = idSize;
            updateMaxStockForSize(size, idSize);
        });
    });
    if (sizeButtons[0]) sizeButtons[0].click();
} else {
    const idSize0 = document.getElementById('selected-id-size').value;
    currentMaxStock = <?= $stockForNoSize ?>;
    currentIdSize = idSize0;
    qtyInput.max = currentMaxStock;
    const stockSpan = document.getElementById('stock-status-text');
    if (stockSpan) {
        let stock = currentMaxStock;
        if (stock <= 0) {
            stockSpan.innerHTML = 'Hết hàng';
            stockSpan.className = 'stock-text text-danger';
        } else if (stock <= 5) {
            stockSpan.innerHTML = 'Sắp hết hàng (còn ' + stock + ' sản phẩm)';
            stockSpan.className = 'stock-text text-warning';
        } else {
            stockSpan.innerHTML = 'Còn hàng';
            stockSpan.className = 'stock-text text-success';
        }
    }
}

const minusBtn = document.querySelector('.minus');
const plusBtn = document.querySelector('.plus');
if (minusBtn && plusBtn && qtyInput) {
    minusBtn.onclick = () => {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) qtyInput.value = val - 1;
    };
    plusBtn.onclick = () => {
        let val = parseInt(qtyInput.value) || 1;
        if (val < currentMaxStock) qtyInput.value = val + 1;
    };
    qtyInput.oninput = () => {
        let val = parseInt(qtyInput.value) || 1;
        if (val < 1) val = 1;
        if (val > currentMaxStock) val = currentMaxStock;
        qtyInput.value = val;
    };
}

function addToCart() {
    let quantity = document.getElementById('quantity').value;
    let idSize = document.getElementById('selected-id-size').value;
    if (!idSize || idSize == '0') {
        if (sizeButtons.length > 0) {
            alert('Vui lòng chọn size.');
            return;
        }
    }
    $.ajax({
        url: 'xu-ly-gio-hang.php',
        method: 'POST',
        data: {
            action: 'add',
            id_size: idSize,
            quantity: quantity
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert('Đã thêm vào giỏ hàng!');
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        }
    });
}

function buyNow() {
    let quantity = document.getElementById('quantity').value;
    let idSize = document.getElementById('selected-id-size').value;
    if (!idSize || idSize == '0') {
        if (sizeButtons.length > 0) {
            alert('Vui lòng chọn size.');
            return;
        }
    }
    window.location.href = `thanh-toan.php?buy_now=1&id_size=${idSize}&qty=${quantity}`;
}

let index = { related: 0, viewed: 0 };
function slideRight(type) {
    const slider = document.getElementById(type + '-slider');
    if (!slider) return;
    const items = slider.children.length;
    if (items <= 4) return;
    if (index[type] < items - 4) {
        index[type]++;
        updateSlide(type);
    }
}
function slideLeft(type) {
    const slider = document.getElementById(type + '-slider');
    if (!slider) return;
    const items = slider.children.length;
    if (items <= 4) return;
    if (index[type] > 0) {
        index[type]--;
        updateSlide(type);
    }
}
function updateSlide(type) {
    const slider = document.getElementById(type + '-slider');
    if (!slider || slider.children.length === 0) return;
    const itemWidth = slider.children[0].offsetWidth + 15;
    slider.style.transform = `translateX(-${index[type] * itemWidth}px)`;
}
window.addEventListener('resize', () => {
    updateSlide('related');
    updateSlide('viewed');
});
</script>

<style>
.size-guide-link {
    color: #888;
    font-size: 20px;
    transition: 0.3s;
}
.size-guide-link:hover {
    color: #f4b400;
}
.stock-text {
    font-size: 14px;
    margin-left: 15px;
    font-weight: 500;
}
.text-danger { color: #dc3545; }
.text-warning { color: #ffc107; }
.text-success { color: #28a745; }
</style>

<?php include "../layout/footer_public.php"; ?>