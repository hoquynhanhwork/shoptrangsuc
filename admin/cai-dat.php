<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-settings-page';
$current_page = 'cai-dat.php';

$message = '';
$error = '';

if (isset($_SESSION['admin_message'])) {
    $msg = $_SESSION['admin_message'];
    if ($msg['type'] === 'success') $message = $msg['text'];
    else $error = $msg['text'];
    unset($_SESSION['admin_message']);
}

$adminId = $_SESSION['nguoidung']['idUser'];
$stmt = $conn->prepare("SELECT * FROM nguoidung WHERE idUser = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

include "../layout/header_admin.php";
?>

<div class="content">
    <div class="container-fluid">
        <div class="admin-header-section">
            <div class="title-area">
                <h1><i class="fa-solid fa-sliders"></i> CÀI ĐẶT</h1>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa-solid fa-user-gear"></i> Thông tin tài khoản</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="xu-ly-cai-dat.php">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="mb-3">
                                <label class="form-label">Họ tên</label>
                                <input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($admin['HoTen']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($admin['TenDangNhap']) ?>" disabled>
                                <small class="text-muted">Không thể thay đổi tên đăng nhập.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['Email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="sodienthoai" class="form-control" value="<?= htmlspecialchars($admin['SoDienThoai'] ?? '') ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa-solid fa-key"></i> Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="xu-ly-cai-dat.php">
                            <input type="hidden" name="action" value="change_password">
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../layout/footer_admin.php"; ?>