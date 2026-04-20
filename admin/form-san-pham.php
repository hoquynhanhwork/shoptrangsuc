<?php
session_start();
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-product-form-page';
$current_page = 'quan-ly-san-pham.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isEdit = $id > 0;
$product = null;
$title = $isEdit ? 'SỬA SẢN PHẨM' : 'THÊM SẢN PHẨM';
$buttonText = $isEdit ? 'Cập nhật' : 'Thêm mới';

if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM sanpham WHERE MaSanPham = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        $_SESSION['error'] = 'Sản phẩm không tồn tại.';
        header('Location: quan-ly-san-pham.php');
        exit;
    }
}

$productSizes = [];
$totalStockFromSize = 0;
if ($isEdit) {
    $stmtSize = $conn->prepare("SELECT size, SoLuongTon FROM size WHERE MaSanPham = ?");
    $stmtSize->execute([$id]);
    $productSizes = $stmtSize->fetchAll(PDO::FETCH_ASSOC);
    foreach ($productSizes as $ps) {
        $totalStockFromSize += $ps['SoLuongTon'];
    }
}
$sizeQuantities = [];
foreach ($productSizes as $ps) {
    $sizeQuantities[$ps['size']] = $ps['SoLuongTon'];
}

$isNoSize = false;
if ($isEdit) {
    $hasOnlyZeroSize = (count($productSizes) == 1 && isset($sizeQuantities['0']));
    $isNoSize = $hasOnlyZeroSize;
}

$stmtCat = $conn->query("SELECT MaDanhMuc, TenDanhMuc FROM danhmuc WHERE TrangThai = 1 ORDER BY TenDanhMuc");
$categories = $stmtCat->fetchAll();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <div class="page-header">
            <h1><i class="fa-solid fa-box"></i> <?= $title ?></h1>
            <a href="quan-ly-san-pham.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="xu-ly-QLSP.php" method="post" enctype="multipart/form-data" class="two-col-form">
                    <input type="hidden" name="action" value="<?= $isEdit ? 'edit' : 'add' ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $id ?>">
                    <?php endif; ?>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Danh mục <span class="required-star">*</span></label>
                                <select name="ma_danh_muc" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['MaDanhMuc'] ?>" <?= ($isEdit && $product['MaDanhMuc'] == $cat['MaDanhMuc']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['TenDanhMuc']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tên sản phẩm <span class="required-star">*</span></label>
                                <input type="text" name="ten_san_pham" class="form-control" required
                                       value="<?= $isEdit ? htmlspecialchars($product['TenSanPham']) : '' ?>">
                            </div>

                            <div class="form-group">
                                <label>Đường dẫn (slug)</label>
                                <input type="text" name="duong_dan" class="form-control" 
                                       value="<?= $isEdit ? htmlspecialchars($product['DuongDan']) : '' ?>">
                                <small class="text-muted">Để trống → tự động tạo từ tên sản phẩm.</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Giá (VNĐ) <span class="required-star">*</span></label>
                                        <input type="number" name="gia" class="form-control" step="1000" required
                                               value="<?= $isEdit ? htmlspecialchars($product['Gia']) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tồn kho tổng (tự động)</label>
                                        <input type="text" id="total_stock_display" class="form-control" readonly disabled value="0">
                                        <small class="text-muted">Tổng số lượng từ các size (hoặc nhập trực tiếp khi không có size).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Mô tả ngắn <span class="required-star">*</span></label>
                                <textarea name="mo_ta" class="form-control" rows="3"><?= $isEdit ? htmlspecialchars($product['MoTa']) : '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Hình ảnh <span class="required-star">*</span></label>
                                <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                                <?php if ($isEdit && !empty($product['HinhAnh'])): ?>
                                    <div class="image-preview">
                                        <img src="../img/<?= htmlspecialchars($product['HinhAnh']) ?>" class="img-fluid rounded" alt="preview">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="xoa_hinh" value="1" id="xoaHinh">
                                            <label class="form-check-label" for="xoaHinh">Xóa ảnh hiện tại</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Chi tiết sản phẩm <span class="required-star">*</span></label>
                                <textarea name="chi_tiet" class="form-control" rows="8"><?= $isEdit ? htmlspecialchars($product['ChiTietSanPham']) : '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Nổi bật</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="noi_bat" value="1" id="noiBat" 
                                           <?= ($isEdit && $product['NoiBat'] == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="noiBat">Hiển thị ở trang chủ (sản phẩm nổi bật)</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Trạng thái <span class="required-star">*</span></label>
                                <select name="trang_thai" class="form-select">
                                    <option value="1" <?= ($isEdit && $product['TrangThai'] == 1) ? 'selected' : '' ?>>Đang hoạt động</option>
                                    <option value="0" <?= ($isEdit && $product['TrangThai'] == 0) ? 'selected' : '' ?>>Ngừng hoạt động</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Quản lý size và tồn kho</label>
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="no_size" name="no_size" value="1" <?= ($isEdit && $isNoSize) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="no_size">
                                            Sản phẩm không có size (hoa tai, mặt dây chuyền, dây chuyền)
                                        </label>
                                    </div>
                                </div>

                                <div id="total-stock-section" style="<?= ($isEdit && $isNoSize) ? 'display:block;' : 'display:none;' ?>">
                                    <div class="form-group">
                                        <label>Tồn kho tổng <span class="required-star">*</span></label>
                                        <input type="number" name="total_stock" id="total_stock_input" class="form-control" 
                                               value="<?= ($isEdit && $isNoSize) ? ($sizeQuantities['0'] ?? 0) : 0 ?>" min="0">
                                        <small class="text-muted">Nhập số lượng sản phẩm có sẵn (áp dụng cho sản phẩm không có size).</small>
                                    </div>
                                </div>

                                <div id="size-selection" style="<?= ($isEdit && $isNoSize) ? 'display:none;' : 'display:block;' ?>">
                                    <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                                        <table class="table table-sm table-bordered size-manager-table">
                                            <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Số lượng tồn</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="size-table-body">
                                                <?php
                                                $sizeRange = range(5, 20);
                                                foreach ($sizeQuantities as $sz => $qty):
                                                    if ($sz == '0') continue;
                                                ?>
                                                <tr class="size-row" data-size="<?= $sz ?>">
                                                    <td><label><?= $sz ?></label></td>
                                                    <td><input type="number" name="size_qty[<?= $sz ?>]" class="form-control form-control-sm size-qty" value="<?= $qty ?>" min="0" style="width:100px"></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-size" title="Xóa size">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-2">
                                        <select id="add-size-select" class="form-select w-auto d-inline-block" style="max-width: 100px;">
                                            <option value=""> Thêm size </option>
                                            <?php foreach ($sizeRange as $s): ?>
                                                <option value="<?= $s ?>"><?= $s ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" id="add-size-btn" class="btn btn-sm btn-secondary">Thêm</button>
                                    </div>
                                    <small class="text-muted mt-2 d-block">Thêm size và nhập số lượng tồn kho cho từng size. Số lượng có thể để 0.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 d-flex gap-3 justify-content-start">
                            <button type="submit" class="btn btn-primary px-5 py-2"><?= $buttonText ?></button>
                            <a href="quan-ly-san-pham.php" class="btn btn-secondary px-5 py-2">Hủy</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('input[name="ten_san_pham"]').addEventListener('blur', function() {
        let slugInput = document.querySelector('input[name="duong_dan"]');
        if (slugInput.value.trim() === '') {
            let slug = this.value.trim()
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugInput.value = slug;
        }
    });

    const noSizeCheckbox = document.getElementById('no_size');
    const sizeSelectionDiv = document.getElementById('size-selection');
    const totalStockSection = document.getElementById('total-stock-section');
    const totalStockDisplay = document.getElementById('total_stock_display');
    const totalStockInput = document.getElementById('total_stock_input');

    function updateTotalStock() {
        let total = 0;
        if (noSizeCheckbox && noSizeCheckbox.checked) {
            let val = parseInt(totalStockInput.value) || 0;
            totalStockDisplay.value = val;
        } else {
            document.querySelectorAll('.size-qty').forEach(input => {
                let val = parseInt(input.value) || 0;
                total += val;
            });
            totalStockDisplay.value = total;
        }
    }

    function toggleStockSection() {
        if (noSizeCheckbox.checked) {
            sizeSelectionDiv.style.display = 'none';
            totalStockSection.style.display = 'block';
            updateTotalStock();
        } else {
            sizeSelectionDiv.style.display = 'block';
            totalStockSection.style.display = 'none';
            updateTotalStock();
        }
    }

    if (noSizeCheckbox) {
        noSizeCheckbox.addEventListener('change', toggleStockSection);
        toggleStockSection();
    }

    function attachEventsToRow(row) {
        let qtyInput = row.querySelector('.size-qty');
        if (qtyInput) {
            qtyInput.addEventListener('input', updateTotalStock);
        }
        let removeBtn = row.querySelector('.remove-size');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                row.remove();
                updateTotalStock();
            });
        }
    }

    document.getElementById('add-size-btn').addEventListener('click', function() {
        let select = document.getElementById('add-size-select');
        let sizeVal = select.value;
        if (!sizeVal) return;
        if (document.querySelector(`.size-row[data-size="${sizeVal}"]`)) {
            alert('Size này đã có');
            return;
        }
        let tbody = document.getElementById('size-table-body');
        let newRow = document.createElement('tr');
        newRow.className = 'size-row';
        newRow.setAttribute('data-size', sizeVal);
        newRow.innerHTML = `
            <td><label>${sizeVal}</label></td>
            <td><input type="number" name="size_qty[${sizeVal}]" class="form-control form-control-sm size-qty" value="0" min="0" style="width:100px"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-size" title="Xóa size">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        select.value = '';
        attachEventsToRow(newRow);
        updateTotalStock();
    });

    document.querySelectorAll('.size-row').forEach(row => attachEventsToRow(row));

    if (totalStockInput) {
        totalStockInput.addEventListener('input', updateTotalStock);
    }

    updateTotalStock();
</script>

<?php include "../layout/footer_admin.php"; ?>