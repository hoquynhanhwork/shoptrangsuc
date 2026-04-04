<?php
session_start();
require_once '../config.php';

if (!isset($conn) || $conn === null) {
    die('Lỗi: Không thể kết nối cơ sở dữ liệu.');
}

if (!isset($_SESSION['nguoidung'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để chỉnh sửa thông tin.';
    header('Location: dang-nhap.php');
    exit;
}

$user = $_SESSION['nguoidung'];
$idUser = $user['idUser'];

$stmt = $conn->prepare("SELECT SoDienThoai, DiaChi FROM nguoidung WHERE idUser = ?");
$stmt->execute([$idUser]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if ($info) {
    $user['SoDienThoai'] = $info['SoDienThoai'] ?? '';
    $user['DiaChi']      = $info['DiaChi'] ?? '';
} else {
    $user['SoDienThoai'] = '';
    $user['DiaChi']      = '';
}

$bodyClass = 'account-page form-page';
include "../layout/header_public.php";
?>

<div class="account-container" style="max-width: 600px;">
    <div class="account-card" style="border-radius: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); background: #fff; border: 1px solid #f0f0f0;">
        <div class="card-header-custom" style="padding: 20px 24px; border-bottom: 1px solid #e4d4d6; background: #faf8f8; border-radius: 24px 24px 0 0;">
            <h5 class="fw-bold text-center m-0" style="color: #212737;">✏️ CHỈNH SỬA THÔNG TIN</h5>
        </div>
        <div class="card-body-custom" style="padding: 24px;">
            <?php if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach ($_SESSION['errors'] as $err): ?>
                        <div><?= htmlspecialchars($err) ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form action="xu-ly-tai-khoan.php?action=sua-thong-tin" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Họ tên</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="hoten" class="form-control border-start-0" 
                               value="<?= htmlspecialchars($_SESSION['old']['hoten'] ?? $user['HoTen']) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" 
                               value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $user['Email']) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Số điện thoại</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-phone"></i></span>
                        <input type="tel" name="sodienthoai" class="form-control border-start-0" 
                               value="<?= htmlspecialchars($_SESSION['old']['sodienthoai'] ?? $user['SoDienThoai']) ?>" required>
                    </div>
                </div>
               <div class="mb-3">
                    <label class="form-label fw-semibold">Địa chỉ</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <select name="province" id="province" class="form-select" required>
                                <option value="">-- Chọn tỉnh/thành --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="ward" id="ward" class="form-select" required disabled>
                                <option value="">-- Chọn phường/xã --</option>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="text" name="street" class="form-control" placeholder="Số nhà, tên đường, khu phố..." 
                                value="<?= htmlspecialchars($_SESSION['old']['street'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary w-100" style="background: #212737; border-color: #212737;">Cập nhật</button>
                    <a href="tai-khoan.php" class="btn btn-outline-secondary w-100">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $.getJSON('../js/dia-chi.json', function(data) {
        var provinces = data;
        var $province = $('#province');
        var $ward = $('#ward');

        $.each(provinces, function(i, p) {
            $province.append($('<option>', {
                value: p.Code,
                text: p.FullName
            }));
        });

        $province.change(function() {
            var provinceCode = $(this).val();
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
        });

        <?php if (!empty($_SESSION['old']['province_code']) && !empty($_SESSION['old']['ward_code'])): ?>
        var oldProvince = "<?= $_SESSION['old']['province_code'] ?>";
        var oldWard = "<?= $_SESSION['old']['ward_code'] ?>";
        $province.val(oldProvince).trigger('change');
        setTimeout(function() { $ward.val(oldWard); }, 100);
        <?php endif; ?>
    });
});
</script>
<?php include "../layout/footer_public.php"; ?>