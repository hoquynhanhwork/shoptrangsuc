<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$user = $_SESSION['nguoidung'];
$idUser = $user['idUser'];

// Lấy giỏ hàng
$stmt = $conn->prepare("
    SELECT gh.SoLuong, s.gia, sp.TenSanPham, sp.HinhAnh
    FROM giohang gh
    JOIN size s ON gh.id_size = s.id
    JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
    WHERE gh.idUser = ?
");
$stmt->execute([$idUser]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền hàng
$tongTien = 0;
foreach ($cart as $item) {
    $tongTien += $item['gia'] * $item['SoLuong'];
}

// ========== MẢNG PHÍ SHIP THEO MÃ TỈNH ==========
$ship_fees_by_code = [
    '79' => 20000,   // Hồ Chí Minh
    '01' => 25000,   // Hà Nội
    '48' => 30000,   // Đà Nẵng
    '31' => 25000,   // Hải Phòng
    '92' => 30000,   // Cần Thơ
    '74' => 22000,   // Bình Dương
    '75' => 22000,   // Đồng Nai
    'default' => 35000  // các tỉnh còn lại
];

$bodyClass = 'checkout-page';
include "../layout/header_public.php";
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="container mt-5">
    <div class="row">

        <!-- LEFT COLUMN -->
        <div class="col-md-7">

            <!-- TÀI KHOẢN -->
            <div class="card-box d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle me-3">
                        <?= strtoupper(substr($user['HoTen'], 0, 1)) ?>
                    </div>
                    <div>
                        <strong><?= htmlspecialchars($user['HoTen']) ?></strong><br>
                        <small><?= htmlspecialchars($user['Email']) ?> | <?= htmlspecialchars($user['SoDienThoai']) ?></small>
                    </div>
                </div>
                <a href="xu-ly-tai-khoan.php?action=dangxuat" class="text-danger">Đăng xuất</a>
            </div>

            <!-- THÔNG TIN GIAO HÀNG -->
            <div class="card-box">
                <h5>Thông tin giao hàng</h5>
                <form id="shipping-form">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ tên</label>
                        <input type="text" id="fullname" class="form-control" value="<?= htmlspecialchars($user['HoTen']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số điện thoại</label>
                        <input type="tel" id="phone" class="form-control" value="<?= htmlspecialchars($user['SoDienThoai']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select id="province" class="form-select" required>
                                    <option value="">-- Chọn tỉnh/thành --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select id="ward" class="form-select" required disabled>
                                    <option value="">-- Chọn phường/xã --</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <input type="text" id="street" class="form-control" placeholder="Số nhà, tên đường, khu phố...">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- PHƯƠNG THỨC GIAO HÀNG (hiển thị phí ship động) -->
            <div class="card-box">
                <h5>Phương thức giao hàng</h5>
                <div class="payment-item active ship-method">
                    <div>
                        <input type="radio" name="shipping" value="standard" checked disabled style="margin-right: 10px;">
                        Giao hàng tiêu chuẩn
                    </div>
                    <span id="ship-fee-display">0đ</span>
                </div>
            </div>

            <!-- PHƯƠNG THỨC THANH TOÁN -->
            <div class="card-box">
                <h5>Phương thức thanh toán</h5>
                <label class="payment-item active" data-method="cod">
                    <input type="radio" name="payment" value="cod" checked> Thanh toán khi nhận hàng (COD)
                </label>
                <label class="payment-item" data-method="qr">
                    <input type="radio" name="payment" value="qr"> Chuyển khoản QR
                </label>
                <label class="payment-item" data-method="momo">
                    <input type="radio" name="payment" value="momo"> Ví MoMo
                </label>
            </div>

            <!-- GHI CHÚ -->
            <div class="card-box">
                <textarea id="notes" class="form-control" rows="2" placeholder="Ghi chú đơn hàng (không bắt buộc)"></textarea>
            </div>

        </div>

        <!-- RIGHT COLUMN (GIỎ HÀNG + TỔNG) -->
        <div class="col-md-5">
            <div class="card-box">
                <h5>Giỏ hàng</h5>
                <?php if (empty($cart)): ?>
                    <p class="text-muted">Giỏ hàng trống.</p>
                <?php else: ?>
                    <?php foreach ($cart as $item): ?>
                        <div class="d-flex mb-3">
                            <img src="<?= htmlspecialchars($item['HinhAnh']) ?>" width="60" class="me-3 rounded">
                            <div>
                                <?= htmlspecialchars($item['TenSanPham']) ?><br>
                                <strong><?= number_format($item['gia']) ?>đ</strong> x <?= $item['SoLuong'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="card-box">
                <h5>Tóm tắt đơn hàng</h5>
                <div class="d-flex justify-content-between">
                    <span>Tổng tiền</span>
                    <span><?= number_format($tongTien) ?>đ</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Phí ship</span>
                    <span id="ship-fee-display2">0đ</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Tổng thanh toán</span>
                    <span id="total-display"><?= number_format($tongTien) ?>đ</span>
                </div>
                <input type="hidden" id="ship_fee_hidden" value="0">
                <button class="btn btn-dark w-100 mt-3" id="orderBtn">Đặt hàng</button>
            </div>
        </div>

    </div>
</div>

<script>
// Truyền mảng phí ship (key = mã tỉnh) từ PHP sang JS
var shipFeesByCode = <?= json_encode($ship_fees_by_code) ?>;
var tongTien = <?= $tongTien ?>;

// Hàm cập nhật phí ship dựa trên mã tỉnh
function updateShipFee(provinceCode) {
    var shipFee = 0;
    if (provinceCode && provinceCode !== '') {
        shipFee = shipFeesByCode[provinceCode] || shipFeesByCode['default'];
    } else {
        shipFee = 0; // chưa chọn tỉnh
    }
    $('#ship-fee-display, #ship-fee-display2').text(shipFee.toLocaleString() + 'đ');
    $('#ship_fee_hidden').val(shipFee);
    var total = tongTien + shipFee;
    $('#total-display').text(total.toLocaleString() + 'đ');
}

// 1. Load tỉnh/thành từ JSON và tạo dropdown
$(document).ready(function() {
    $.getJSON('../js/dia-chi.json', function(data) {
        var provinces = data;
        var $province = $('#province');
        var $ward = $('#ward');

        // Thêm các tỉnh vào select, value là mã tỉnh
        $.each(provinces, function(i, p) {
            $province.append($('<option>', {
                value: p.Code,
                text: p.FullName
            }));
        });

        // Sự kiện khi chọn tỉnh
        $province.change(function() {
            var provinceCode = $(this).val();
            // Reset phường/xã
            $ward.empty().append('<option value="">-- Chọn phường/xã --</option>').prop('disabled', true);
            if (provinceCode) {
                var selectedProvince = provinces.find(p => p.Code == provinceCode);
                if (selectedProvince && selectedProvince.Wards) {
                    $.each(selectedProvince.Wards, function(i, w) {
                        $ward.append($('<option>', {
                            value: w.Code,
                            text: w.FullName
                        }));
                    });
                    $ward.prop('disabled', false);
                }
            }
            // Cập nhật phí ship theo mã tỉnh
            updateShipFee(provinceCode);
        });
        
        // Nếu có tỉnh mặc định (ví dụ từ session), có thể set và trigger
        // Ở đây chưa có, bỏ qua
    });
});

// 2. Chọn phương thức thanh toán (highlight)
$('.payment-item').click(function() {
    $('.payment-item').removeClass('active');
    $(this).addClass('active');
    $(this).find('input[type=radio]').prop('checked', true);
});

// 3. Xử lý đặt hàng AJAX
$('#orderBtn').click(function() {
    var fullname = $('#fullname').val();
    var phone = $('#phone').val();
    var provinceCode = $('#province').val();
    var provinceName = $('#province option:selected').text();
    var wardName = $('#ward option:selected').text();
    var street = $('#street').val();
    var address = street + (street ? ', ' : '') + wardName + (wardName ? ', ' : '') + provinceName;
    if (!provinceCode || !wardName) {
        alert('Vui lòng chọn đầy đủ tỉnh/thành và phường/xã.');
        return;
    }
    var notes = $('#notes').val();
    var payment = $('input[name="payment"]:checked').val();
    var shipFee = $('#ship_fee_hidden').val();

    $.ajax({
        url: 'xu-ly-thanh-toan.php',
        method: 'POST',
        data: {
            action: 'create_order',
            fullname: fullname,
            phone: phone,
            address: address,
            province_code: provinceCode,
            province_name: provinceName,
            ward_name: wardName,
            street: street,
            notes: notes,
            payment: payment,
            ship_fee: shipFee
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                if (payment === 'momo') {
                    window.location.href = res.momo_pay_url;
                } else {
                    alert('Đặt hàng thành công!');
                    window.location.href = 'don-hang-cua-toi.php';
                }
            } else {
                alert('Lỗi: ' + res.message);
            }
        },
        error: function() {
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        }
    });
});
</script>

<?php
include "../layout/footer_public.php";
?>