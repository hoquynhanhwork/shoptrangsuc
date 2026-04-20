<?php
$bodyClass = 'checkout-page';
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$buyNow = isset($_GET['buy_now']) && $_GET['buy_now'] == 1;
if (!$buyNow && (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items']))) {
    header("Location: gio-hang.php");
    exit;
}

$user = $_SESSION['nguoidung'];
$selectedIds = $_SESSION['checkout_items'] ?? [];

$id_size = 0;
$qty = 1;
if ($buyNow) {
    $id_size = intval($_GET['id_size'] ?? 0);
    $qty = intval($_GET['qty'] ?? 1);
    if ($id_size <= 0 || $qty < 1) {
        $_SESSION['error'] = "Thông tin sản phẩm không hợp lệ.";
        header("Location: san-pham.php");
        exit;
    }
}

include "../layout/header_public.php";
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-7">
            <div class="card-box d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle me-3"><?= strtoupper(substr($user['HoTen'], 0, 1)) ?></div>
                    <div>
                        <strong><?= htmlspecialchars($user['HoTen']) ?></strong><br>
                        <small><?= htmlspecialchars($user['Email']) ?> | <?= htmlspecialchars($user['SoDienThoai']) ?></small>
                    </div>
                </div>
            </div>

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
                                <select id="province" class="form-select" required><option value="">-- Chọn tỉnh/thành --</option></select>
                            </div>
                            <div class="col-md-6">
                                <select id="ward" class="form-select" required disabled><option value="">-- Chọn phường/xã --</option></select>
                            </div>
                            <div class="col-12 mt-2">
                                <input type="text" id="street" class="form-control" placeholder="Số nhà, tên đường, khu phố...">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-box">
                <h5>Phương thức giao hàng</h5>
                <div class="payment-item active ship-method">
                    <div><input type="radio" name="shipping" value="standard" checked disabled> Giao hàng tiêu chuẩn</div>
                    <span id="ship-fee-display">0đ</span>
                </div>
            </div>

            <div class="card-box">
                <h5>Phương thức thanh toán</h5>
                <label class="payment-item active" data-method="cod"><input type="radio" name="payment" value="cod" checked> Thanh toán khi nhận hàng (COD)</label>
                <label class="payment-item" data-method="bank"><input type="radio" name="payment" value="bank"> Chuyển khoản QR</label>
                <label class="payment-item" data-method="momo"><input type="radio" name="payment" value="momo"> Ví MoMo</label>
            </div>

            <div class="card-box">
                <textarea id="notes" class="form-control" rows="2" placeholder="Ghi chú đơn hàng (không bắt buộc)"></textarea>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card-box">
                <h5>Giỏ hàng</h5>
                <div id="cart-items-container">Đang tải...</div>
            </div>

            <div class="card-box">
                <h5>Tóm tắt đơn hàng</h5>
                <div class="d-flex justify-content-between"><span>Tổng tiền</span><span id="subtotal">0đ</span></div>
                <div class="d-flex justify-content-between"><span>Phí ship</span><span id="ship-fee-display2">0đ</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Tổng thanh toán</span><span id="total-display">0đ</span></div>
                <input type="hidden" id="ship_fee_hidden" value="0">
                <button class="btn btn-dark w-100 mt-3" id="orderBtn">Đặt hàng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var shipFeesByCode = <?= json_encode([
    '79' => 20000, '01' => 25000, '48' => 30000,
    '31' => 25000, '92' => 30000, '74' => 22000,
    '75' => 22000, 'default' => 35000
]) ?>;
var tongTien = 0;
var buyNow = <?= $buyNow ? 'true' : 'false' ?>;
var selectedIds = <?= json_encode($selectedIds) ?>;
var buyNowIdSize = <?= $buyNow ? $id_size : 0 ?>;
var buyNowQty = <?= $buyNow ? $qty : 0 ?>;

function updateShipFee(provinceCode) {
    var shipFee = (provinceCode && provinceCode !== '') ? (shipFeesByCode[provinceCode] || shipFeesByCode['default']) : 0;
    $('#ship-fee-display, #ship-fee-display2').text(shipFee.toLocaleString() + 'đ');
    $('#ship_fee_hidden').val(shipFee);
    var total = tongTien + shipFee;
    $('#total-display').text(total.toLocaleString() + 'đ');
}

function loadCart() {
    var postData = { action: 'get_cart' };
    if (!buyNow) {
        postData.ids = selectedIds;
    } else {
        postData.buy_now = 1;
        postData.id_size = buyNowIdSize;
        postData.qty = buyNowQty;
    }
    $.ajax({
        url: 'xu-ly-thanh-toan.php',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                var cartItems = res.cartItems;
                tongTien = res.tongTien;
                var itemsHtml = '';
                if (cartItems.length === 0) {
                    itemsHtml = '<p class="text-muted">Giỏ hàng trống.</p>';
                } else {
                    cartItems.forEach(function(item) {
                        itemsHtml += `
                            <div class="d-flex mb-3">
                                <img src="../img/${item.HinhAnh}" width="60" class="me-3 rounded">
                                <div>
                                    ${item.TenSanPham}<br>
                                    ${item.size && item.size != '0' ? `<span class="text-muted small">Size: ${item.size}</span><br>` : ''}
                                    <strong>${new Intl.NumberFormat('vi-VN').format(item.gia)}đ</strong> x ${item.SoLuong}
                                </div>
                            </div>
                        `;
                    });
                }
                $('#cart-items-container').html(itemsHtml);
                $('#subtotal').text(new Intl.NumberFormat('vi-VN').format(tongTien) + 'đ');
                updateShipFee($('#province').val());
            } else {
                alert('Không thể tải giỏ hàng: ' + (res.message || 'Lỗi không xác định'));
            }
        },
        error: function() { alert('Lỗi kết nối khi tải giỏ hàng'); }
    });
}

$(document).ready(function() {
    loadCart();

    $.getJSON('../js/dia-chi.json', function(data) {
        var provinces = data;
        var $province = $('#province');
        var $ward = $('#ward');
        $.each(provinces, function(i, p) {
            $province.append($('<option>', { value: p.Code, text: p.FullName }));
        });
        $province.change(function() {
            var provinceCode = $(this).val();
            $ward.empty().append('<option value="">-- Chọn phường/xã --</option>').prop('disabled', true);
            if (provinceCode) {
                var selectedProvince = provinces.find(p => p.Code == provinceCode);
                if (selectedProvince && selectedProvince.Wards) {
                    $.each(selectedProvince.Wards, function(i, w) {
                        $ward.append($('<option>', { value: w.Code, text: w.FullName }));
                    });
                    $ward.prop('disabled', false);
                }
            }
            updateShipFee(provinceCode);
        });
    });

    $('.payment-item').click(function() {
        $('.payment-item').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type=radio]').prop('checked', true);
    });

    $('#orderBtn').click(function() {
        var fullname = $('#fullname').val().trim();
        var phone = $('#phone').val().trim();
        var provinceCode = $('#province').val();
        var provinceName = $('#province option:selected').text();
        var wardName = $('#ward option:selected').text();
        var street = $('#street').val().trim();
        
        if (!fullname) {
            alert('Vui lòng nhập họ tên.');
            return;
        }
        if (!phone) {
            alert('Vui lòng nhập số điện thoại.');
            return;
        }
        if (!provinceCode) {
            alert('Vui lòng chọn tỉnh/thành.');
            return;
        }
        if (!wardName || wardName === '-- Chọn phường/xã --') {
            alert('Vui lòng chọn phường/xã.');
            return;
        }
        var address = street + (street ? ', ' : '') + wardName + ', ' + provinceName;
        if (address.trim() === '') {
            alert('Vui lòng nhập địa chỉ cụ thể.');
            return;
        }
        var notes = $('#notes').val();
        var payment = $('input[name="payment"]:checked').val();
        if (!payment) {
            alert('Vui lòng chọn phương thức thanh toán.');
            return;
        }
        var shipFee = $('#ship_fee_hidden').val();

        var postData = {
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
        };
        if (buyNow) {
            postData.buy_now = 1;
            postData.id_size = buyNowIdSize;
            postData.quantity = buyNowQty;
        } else {
            postData.ids = selectedIds;
        }

        $.ajax({
            url: 'xu-ly-thanh-toan.php',
            method: 'POST',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    if (payment === 'momo') {
                        window.location.href = 'thanh-toan-momo.php?order_id=' + res.order_id;
                    } else if (payment === 'bank') {
                        window.location.href = 'thanh-toan-ngan-hang.php?order_id=' + res.order_id;
                    } else {
                        window.location.href = 'dat-hang-thanh-cong.php?id=' + res.order_id;
                    }
                } else {
                    alert(res.message || 'Có lỗi xảy ra');
                }
            },
            error: function() { alert('Có lỗi xảy ra, vui lòng thử lại.'); }
        });
    });
});
</script>
<?php include "../layout/footer_public.php"; ?>