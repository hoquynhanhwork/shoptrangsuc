<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    header("Location: trang-chu.php");
    exit;
}

$bank_id = 'TCB';
$account_no = '01234567891112';
$account_name = 'CUA HANG TRANG SUC - AURA JEWELRY';
$template = 'compact2';
$description = "DH{$order_id}";

$bodyClass = 'qr-payment-page';
include "../layout/header_public.php";
?>

<div class="container my-5">
<div class="row justify-content-center">
<div class="col-lg-10">

    <div id="payment-content">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-2">Đang tải thông tin đơn hàng...</p>
        </div>
    </div>

</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $.getJSON('xu-ly-thanh-toan.php?action=get_order_details&order_id=<?= $order_id ?>', function(res) {
        if (!res.success) {
            alert(res.message);
            window.location.href = 'tai-khoan.php';
            return;
        }
        var order = res.order;
        var items = res.items;
        
        var total = parseFloat(order.TongTien);
        var shipFee = parseFloat(order.PhiShip) || 0;
        var subtotal = total - shipFee; 
        
        var orderDate = new Date(order.NgayDatHang);
        var timeDiff = Math.floor((new Date() - orderDate) / 60000);
        var timeText = timeDiff < 60 ? timeDiff + ' phút trước' : Math.floor(timeDiff/60) + ' giờ trước';

        var fullAddress = order.DiaChiGiaoHang || 'Chưa cập nhật';
        var receiverName = order.TenKhachHang || '';
        var receiverPhone = order.SoDienThoai || '';
        var receiverEmail = order.Email || '';

        var bank_id = '<?= $bank_id ?>';
        var account_no = '<?= $account_no ?>';
        var account_name = '<?= $account_name ?>';
        var template = 'compact2';
        var description = '<?= $description ?>';
        var qr_url = `https://img.vietqr.io/image/${bank_id}-${account_no}-${template}.png?amount=${total}&addInfo=${encodeURIComponent(description)}&accountName=${encodeURIComponent(account_name)}`;

        var itemsHtml = '';
        items.forEach(function(item) {
            itemsHtml += `
                <div class="d-flex mb-3">
                    <img src="../img/${item.HinhAnh}" width="60" class="rounded me-3" style="object-fit:cover;">
                    <div>
                        <div class="fw-semibold">${item.TenSanPham}</div>
                        <div class="small text-muted">${item.size ? 'Size: ' + item.size : ''}</div>
                        <div>${new Intl.NumberFormat('vi-VN').format(item.Gia)}đ x ${item.SoLuong}</div>
                    </div>
                </div>
            `;
        });

        var html = `
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card-box">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="status-badge"><i class="bi bi-clock-history"></i> Chờ thanh toán</span>
                                <div class="mt-2">
                                    <h5><strong>Đơn hàng #${order.MaDonHang}</strong></h5>
                                    <span class="text-muted small">Đặt hàng thành công • ${timeText}</span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center mt-2">
                            <img src="${qr_url}" class="qr-img" style="max-width:200px;">
                        </div>
                        <hr>
                        
                        <h5 class="mt-2 fw-semibold"><i class="bi bi-credit-card me-2"></i> Nội dung chuyển khoản</h5>
                        <ul class="bank-info-list">
                            <li><strong>Tài khoản:</strong> ${account_name} (VN)</li>
                            <li><strong>Ngân hàng:</strong> Techcombank</li>
                            <li><strong>Số tài khoản:</strong> ${account_no}</li>
                            <li><strong>Nội dung:</strong> <code>${description}</code></li>
                            <li><strong>Số tiền:</strong> <span class="text-danger fw-bold">${new Intl.NumberFormat('vi-VN').format(total)}đ</span></li>
                        </ul>
                        
                        <div class="mt-3">
                            <label class="form-label fw-semibold"> Hình ảnh chuyển khoản</label>
                            <button type="button" class="btn btn-outline-secondary w-100 mb-2" id="uploadBtn" style="background:#fff; border:1px solid #ccc;">
                                <i class="bi bi-cloud-upload"></i> Tải lên ảnh chụp màn hình
                            </button>
                            <input type="file" id="proofImage" accept="image/*" style="display:none;">
                            <div id="previewImage" class="mt-2 text-center" style="display:none;">
                                <img id="previewImg" style="max-width:100%; max-height:120px; border-radius:8px;">
                                <button class="btn btn-sm btn-link" id="removeImage">Xóa</button>
                            </div>
                            <p class="small text-muted mt-1">Tải ảnh chụp màn hình chuyển khoản để xác minh giao dịch.</p>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            <button id="confirmBtn" class="btn btn-success flex-fill"> Đã thanh toán</button>
                        </div>
                    </div>
                   
                </div>

                <div class="col-md-5">
                    <div class="card-box">
                        <h5 class="fw-semibold"><i class="bi bi-geo-alt"></i> Địa chỉ nhận hàng</h5>
                        <div><strong>${receiverName}</strong>  ${receiverPhone}</div>
                        <div>${receiverEmail}</div>
                        <div class="mt-1">${fullAddress}</div>
                    </div>
                    <div class="card-box">
                        <h5 class="fw-semibold"><i class="bi bi-cart me-2"></i> Giỏ hàng</h5>
                        ${itemsHtml}
                    </div>
                    <div class="card-box">
                        <h5 class="fw-semibold"><i class="bi bi-receipt me-2"></i> Tóm tắt đơn hàng</h5>
                        <div class="info-row">
                            <span>Tổng tiền hàng</span>
                            <span>${new Intl.NumberFormat('vi-VN').format(subtotal)}đ</span>
                        </div>
                        <div class="info-row">
                            <span>Phí vận chuyển</span>
                            <span>${new Intl.NumberFormat('vi-VN').format(shipFee)}đ</span>
                        </div>
                        <div class="info-row fw-bold">
                            <span>Tổng thanh toán</span>
                            <span>${new Intl.NumberFormat('vi-VN').format(total)}đ</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#payment-content').html(html);

        $('#uploadBtn').click(function() {
            $('#proofImage').click();
        });
        $('#proofImage').change(function(e) {
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    $('#previewImg').attr('src', ev.target.result);
                    $('#previewImage').show();
                    $('#uploadBtn').hide();
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        $('#removeImage').click(function() {
            $('#proofImage').val('');
            $('#previewImage').hide();
            $('#uploadBtn').show();
        });

        function confirmPayment() {
            var fileInput = $('#proofImage')[0].files[0];
            if (!fileInput) {
                if (confirm('Bạn chưa tải lên ảnh chụp màn hình. Xác nhận đã chuyển khoản?')) {
                    sendConfirm();
                }
                return;
            }
            var formData = new FormData();
            formData.append('order_id', <?= $order_id ?>);
            formData.append('proof_image', fileInput);
            $.ajax({
                url: 'upload.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        sendConfirm();
                    } else {
                        alert('Lỗi upload ảnh: ' + res.message);
                    }
                },
                error: function() { alert('Lỗi upload ảnh'); }
            });
        }

        function sendConfirm() {
            $.getJSON('xu-ly-thanh-toan.php?action=confirm_bank&order_id=<?= $order_id ?>', function(resp) {
                if (resp.success) {
                    alert(resp.message);
                    window.location.href = 'dat-hang-thanh-cong.php?id=<?= $order_id ?>';
                } else {
                    alert('Lỗi xác nhận thanh toán: ' + resp.message);
                }
            }).fail(function() {
                alert('Không thể kết nối đến server để xác nhận thanh toán.');
            });
        }

        $('#confirmBtn').click(function() {
            if (confirm('Xác nhận bạn đã chuyển khoản thành công?')) {
                confirmPayment();
            }
        });
    }).fail(function() {
        alert('Không thể tải thông tin đơn hàng. Vui lòng thử lại.');
        window.location.href = 'tai-khoan.php';
    });
});
</script>

<?php include "../layout/footer_public.php"; ?>