<?php
session_start();
if (!isset($_SESSION['nguoidung'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để đổi mật khẩu.';
    header('Location: dang-nhap.php');
    exit;
}

$idUser = $_SESSION['nguoidung']['idUser'];
require_once '../config.php';

$stmt = $conn->prepare("SELECT MatKhau FROM nguoidung WHERE idUser = ?");
$stmt->execute([$idUser]);
$userData = $stmt->fetch();
$hasPassword = !empty($userData['MatKhau']);

$bodyClass = 'account-page form-page';
include "../layout/header_public.php";
?>

<div class="account-container" style="max-width: 600px;">
    <div class="account-card" style="border-radius: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); background: #fff; border: 1px solid #f0f0f0;">
        <div class="card-header-custom" style="padding: 20px 24px; border-bottom: 1px solid #e4d4d6; background: #faf8f8; border-radius: 24px 24px 0 0;">
            <h5 class="fw-bold text-center m-0" style="color: #212737;">🔑 <?= $hasPassword ? 'ĐỔI MẬT KHẨU' : 'ĐẶT MẬT KHẨU' ?></h5>
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

            <?php if (!$hasPassword): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Bạn đang đăng nhập bằng Google và chưa có mật khẩu. Hãy đặt mật khẩu mới để có thể đăng nhập bằng tài khoản thông thường sau này.
                </div>
            <?php endif; ?>

            <form action="xu-ly-tai-khoan.php?action=doi-mat-khau" method="POST">
                <?php if ($hasPassword): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu cũ</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="matkhau_cu" class="form-control border-start-0" required>
                    </div>
                </div>
                <?php else: ?>
                <input type="hidden" name="google_account" value="1">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-key"></i></span>
                        <input type="password" name="matkhau_moi" class="form-control border-start-0" required>
                    </div>
                    <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-check-circle"></i></span>
                        <input type="password" name="xacnhan_moi" class="form-control border-start-0" required>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary w-100" style="background: #212737; border-color: #212737;"><?= $hasPassword ? 'Đổi mật khẩu' : 'Đặt mật khẩu' ?></button>
                    <a href="tai-khoan.php" class="btn btn-outline-secondary w-100">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../layout/footer_public.php"; ?>