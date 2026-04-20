<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$bodyClass = 'cart-page';
include "../layout/header_public.php";
?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card-box">
                <h5 class="mb-3">Giỏ hàng của bạn</h5>
                <div id="cart-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang tải giỏ hàng...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box">
                <h5>Tóm tắt đơn hàng</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm tính (sản phẩm được chọn)</span>
                    <span id="subtotal">0đ</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí ship</span>
                    <span>0đ</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Tổng cộng</span>
                    <span id="total">0đ</span>
                </div>
                <button class="btn btn-dark w-100 mt-3" id="checkoutBtn">Tiến hành thanh toán</button>
                <a href="san-pham.php" class="btn btn-outline-secondary w-100 mt-2">Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function saveSelectedState(id_size, isChecked) {
        localStorage.setItem('cart_selected_' + id_size, isChecked);
    }
    function getSelectedState(id_size) {
        var val = localStorage.getItem('cart_selected_' + id_size);
        return val === null ? true : (val === 'true'); 
    }

    function loadCart() {
        $.ajax({
            url: 'xu-ly-gio-hang.php',
            method: 'POST',
            data: { action: 'get_cart' },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var cartItems = res.cartItems;
                    var itemsHtml = '';
                    if (cartItems.length === 0) {
                        itemsHtml = '<p class="text-muted">Giỏ hàng trống. <a href="san-pham.php">Tiếp tục mua sắm</a></p>';
                        $('#checkoutBtn').prop('disabled', true);
                        $('#subtotal').text('0đ');
                        $('#total').text('0đ');
                    } else {
                        cartItems.forEach(function(item) {
                            var isChecked = getSelectedState(item.id_size);
                            itemsHtml += `
                                <div class="row align-items-center mb-3 pb-3 border-bottom" data-id_size="${item.id_size}">
                                    <div class="col-1">
                                        <input type="checkbox" class="item-checkbox" data-id_size="${item.id_size}" ${isChecked ? 'checked' : ''}>
                                    </div>
                                    <div class="col-2 col-md-2">
                                        <img src="../img/${item.HinhAnh}" class="cart-item-img w-100">
                                    </div>
                                    <div class="col-4 col-md-4">
                                        <h6 class="mb-1">${item.TenSanPham}</h6>
                                        ${item.size && item.size !== '0' ? `<div class="small text-muted">Size: ${item.size}</div>` : ''}                                        <span class="text-muted small">${new Intl.NumberFormat('vi-VN').format(item.gia)}đ</span>
                                    </div>
                                    <div class="col-3 col-md-3">
                                        <div class="qty-control">
                                            <button class="qty-decr">−</button>
                                            <input type="text" class="item-qty" value="${item.SoLuong}" min="1" data-id_size="${item.id_size}">
                                            <button class="qty-incr">+</button>
                                        </div>
                                    </div>
                                    <div class="col-2 text-end">
                                        <i class="bi bi-trash3 remove-item" data-id_size="${item.id_size}"></i>
                                    </div>
                                </div>
                            `;
                        });
                        $('#checkoutBtn').prop('disabled', false);
                    }
                    $('#cart-content').html(itemsHtml);
                    attachEvents();
                    updateTotalPrice();
                } else {
                    alert('Không thể tải giỏ hàng');
                }
            },
            error: function() { alert('Có lỗi xảy ra khi tải giỏ hàng.'); }
        });
    }

    function updateTotalPrice() {
        var selectedIds = [];
        $('.item-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id_size'));
        });
        if (selectedIds.length === 0) {
            $('#subtotal').text('0đ');
            $('#total').text('0đ');
            return;
        }
        $.ajax({
            url: 'xu-ly-gio-hang.php',
            method: 'POST',
            data: {
                action: 'get_selected_total',
                ids: selectedIds
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#subtotal').text(new Intl.NumberFormat('vi-VN').format(res.total) + 'đ');
                    $('#total').text(new Intl.NumberFormat('vi-VN').format(res.total) + 'đ');
                } else {
                    fallbackTotal();
                }
            },
            error: function() { fallbackTotal(); }
        });
    }

    function fallbackTotal() {
        var total = 0;
        $('.item-checkbox:checked').each(function() {
            var $row = $(this).closest('.row');
            var priceText = $row.find('.text-muted.small').last().text().replace('đ', '').replace(/\./g, '');
            var price = parseInt(priceText);
            var qty = parseInt($row.find('.item-qty').val());
            total += price * qty;
        });
        $('#subtotal').text(new Intl.NumberFormat('vi-VN').format(total) + 'đ');
        $('#total').text(new Intl.NumberFormat('vi-VN').format(total) + 'đ');
    }

    function attachEvents() {
        $('.item-checkbox').off('change').on('change', function() {
            var id_size = $(this).data('id_size');
            saveSelectedState(id_size, $(this).is(':checked'));
            updateTotalPrice();
        });

        $('.qty-incr').off('click').on('click', function() {
            var $row = $(this).closest('.row');
            var $input = $row.find('.item-qty');
            var id_size = $input.data('id_size');
            var newQty = parseInt($input.val()) + 1;
            $input.val(newQty);
            updateCart(id_size, newQty, $row);
        });
        $('.qty-decr').off('click').on('click', function() {
            var $row = $(this).closest('.row');
            var $input = $row.find('.item-qty');
            var id_size = $input.data('id_size');
            var currentQty = parseInt($input.val());
            if (currentQty > 1) {
                var newQty = currentQty - 1;
                $input.val(newQty);
                updateCart(id_size, newQty, $row);
            }
        });
        $('.item-qty').off('change').on('change', function() {
            var $input = $(this);
            var id_size = $input.data('id_size');
            var newQty = parseInt($input.val());
            if (isNaN(newQty) || newQty < 1) newQty = 1;
            $input.val(newQty);
            updateCart(id_size, newQty, $input.closest('.row'));
        });
        $('.remove-item').off('click').on('click', function() {
            var id_size = $(this).data('id_size');
            var $row = $(this).closest('.row');
            if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                removeItem(id_size, $row);
            }
        });
    }

    function updateCart(id_size, newQty, $row) {
        $.ajax({
            url: 'xu-ly-gio-hang.php',
            method: 'POST',
            data: { action: 'update', id_size: id_size, quantity: newQty },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateTotalPrice();
                } else {
                    alert(res.message);
                    loadCart();
                }
            },
            error: function() { alert('Có lỗi xảy ra, vui lòng thử lại.'); }
        });
    }

    function removeItem(id_size, $row) {
        $.ajax({
            url: 'xu-ly-gio-hang.php',
            method: 'POST',
            data: { action: 'remove', id_size: id_size },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $row.remove();
                    localStorage.removeItem('cart_selected_' + id_size);
                    if (res.cart_count == 0) {
                        loadCart();
                    } else {
                        updateTotalPrice();
                    }
                } else {
                    alert(res.message);
                }
            },
            error: function() { alert('Có lỗi xảy ra, vui lòng thử lại.'); }
        });
    }

    $('#checkoutBtn').click(function(e) {
        e.preventDefault();
        var selectedIds = [];
        $('.item-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id_size'));
        });
        if (selectedIds.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
            return;
        }
        $.ajax({
            url: 'xu-ly-gio-hang.php',
            method: 'POST',
            data: { action: 'prepare_checkout', ids: selectedIds },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.location.href = 'thanh-toan.php';
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            },
            error: function() { alert('Lỗi kết nối.'); }
        });
    });

    loadCart();
});
</script>

<?php include "../layout/footer_public.php"; ?>