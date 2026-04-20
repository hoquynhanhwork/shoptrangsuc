<?php
$bodyClass = 'checkout-success-page';
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$orderId = $_GET['id'] ?? $_SESSION['last_order_id'] ?? null;
if (!$orderId) {
    header("Location: index.php");
    exit;
}
unset($_SESSION['last_order_id']);

include "../layout/header_public.php";
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div id="order-content">
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
    $.getJSON('xu-ly-thanh-toan.php?action=get_order_details&order_id=<?= $orderId ?>', function(res) {
        if (!res.success) {
            alert(res.message);
            window.location.href = 'tai-khoan.php';
            return;
        }
        var order = res.order;
        var items = res.items;
        
        // Tổng thanh toán (đã bao gồm phí ship) lấy từ order.TongTien
        var total = parseFloat(order.TongTien);
        var shipFee = parseFloat(order.PhiShip) || 0;
        var subtotal = total - shipFee; // tiền hàng

        var itemsHtml = '';
        items.forEach(function(item) {
            itemsHtml += `
                <div class="d-flex mb-3 align-items-center">
                    <img src="../img/${item.HinhAnh}" width="60" class="rounded me-3" style="object-fit: cover;">
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${item.TenSanPham}</div>
                        <div class="small text-muted">
                            Số lượng: ${item.SoLuong}
                            ${item.size ? `| Size: ${item.size}` : ''}
                        </div>
                        <div>${new Intl.NumberFormat('vi-VN').format(item.Gia)}đ</div>
                    </div>
                </div>
            `;
        });

        var html = `
            <div class="card-box d-flex align-items-center border-0 mb-4" style="background: #f8f9fa;">
                <div class="success-icon me-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-bold" style="color: #28a745;">ĐẶT HÀNG THÀNH CÔNG!</h3>
                    <p class="mb-0 text-muted">Cảm ơn bạn đã mua sắm tại Aura Jewelry</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card-box d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <span class="text-muted">Mã đơn hàng</span>
                            <div class="order-code">#${order.MaDonHang}</div>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-truck"></i> Đang chờ xác nhận
                            </span>
                        </div>
                    </div>

                    <div class="card-box">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-credit-card me-2"></i> Phương thức thanh toán</h5>
                        <div class="info-row">
                            <span class="info-label">Hình thức:</span>
                            <span class="info-value text-uppercase">
                                ${order.PhuongThucThanhToan === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 
                                  (order.PhuongThucThanhToan === 'momo' ? 'Ví MoMo' : 'Chuyển khoản ngân hàng')}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tổng tiền hàng:</span>
                            <span class="info-value">${new Intl.NumberFormat('vi-VN').format(subtotal)}đ</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phí vận chuyển:</span>
                            <span class="info-value">${new Intl.NumberFormat('vi-VN').format(shipFee)}đ</span>
                        </div>
                        <div class="info-row fw-bold">
                            <span class="info-label">Cần thanh toán:</span>
                            <span class="info-value" style="color: #e89aa9;">${new Intl.NumberFormat('vi-VN').format(total)}đ</span>
                        </div>
                    </div>

                    <div class="card-box">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-geo-alt me-2"></i> Địa chỉ giao hàng</h5>
                        <div class="info-row">
                            <span class="info-label">Người nhận:</span>
                            <span class="info-value">${order.TenKhachHang}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Số điện thoại:</span>
                            <span class="info-value">${order.SoDienThoai}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Địa chỉ:</span>
                            <span class="info-value">${order.DiaChiGiaoHang}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Ghi chú:</span>
                            <span class="info-value">${order.GhiChu || 'Không có'}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card-box">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-bag-check me-2"></i> Đơn hàng của bạn</h5>
                        ${itemsHtml}
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Tổng cộng:</span>
                            <span>${new Intl.NumberFormat('vi-VN').format(total)}đ</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between flex-wrap gap-3 mt-4">
                <a href="trang-chu.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại trang chủ
                </a>
                <a href="tai-khoan.php" class="btn btn-dark rounded-pill px-4">
                    Xem đơn hàng của tôi <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        `;

        $('#order-content').html(html);
    }).fail(function() {
        alert('Không thể tải thông tin đơn hàng.');
        window.location.href = 'tai-khoan.php';
    });
});
</script>

<?php include "../layout/footer_public.php"; ?>