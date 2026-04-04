<?php
session_start();
require_once '../config.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['nguoidung'])) {
    header('Location: trang-chu.php');
    exit;
}

$step = 'enter_code'; // mặc định là bước nhập mã
$user_info = null;
$token_input = '';

// Xử lý khi submit form nhập mã
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token_code'])) {
    $token_input = trim($_POST['token_code']);
    if (empty($token_input)) {
        $_SESSION['error'] = 'Vui lòng nhập mã xác nhận.';
        header('Location: dat-lai-mat-khau.php');
        exit;
    }

    // Kiểm tra token trong database
    $stmt = $conn->prepare("SELECT idUser, HoTen FROM nguoidung WHERE token_quen_mk = ? AND thoi_gian_quen_mk > NOW()");
    $stmt->execute([$token_input]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_info) {
        // Token hợp lệ → chuyển sang bước đặt mật khẩu
        $step = 'reset_password';
    } else {
        $_SESSION['error'] = 'Mã xác nhận không hợp lệ hoặc đã hết hạn.';
        header('Location: dat-lai-mat-khau.php');
        exit;
    }
}

// Xử lý khi submit form đặt lại mật khẩu (gửi sang xu-ly-tai-khoan.php)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Aura Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/public.css">
</head>
<body class="auth-page">
    <div class="back-home">
        <a href="trang-chu.php">
            <i class="bi bi-arrow-left-circle"></i> Quay lại trang chủ
        </a>
    </div>

    <div class="auth-box">
        <div class="auth-logo">
            <img src="../img/logo.jpg" alt="Aura Jewelry">
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if ($step === 'enter_code'): ?>
            <!-- Bước 1: Nhập mã xác nhận -->
            <h4 class="text-center mb-3 fw-semibold">Nhập mã xác nhận</h4>
            <p class="text-center text-muted small">Mã đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư (kể cả spam).</p>

            <form action="dat-lai-mat-khau.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mã xác nhận</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-key"></i></span>
                        <input type="text" name="token_code" class="form-control border-start-0 ps-0" required autofocus>
                    </div>
                </div>
                <button type="submit" class="btn btn-main w-100 py-2">Xác nhận</button>
            </form>

        <?php else: ?>
            <!-- Bước 2: Đặt lại mật khẩu (token hợp lệ) -->
            <h4 class="text-center mb-3 fw-semibold">Đặt lại mật khẩu</h4>
            <p class="text-center text-muted small">Xin chào <strong><?= htmlspecialchars($user_info['HoTen']) ?></strong>, vui lòng nhập mật khẩu mới.</p>

            <form action="xu-ly-tai-khoan.php" method="POST" id="resetForm">
                <input type="hidden" name="action" value="reset-password">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token_input) ?>">

                <div class="mb-2">
                    <label class="form-label fw-semibold">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" id="newPass" name="matkhau" class="form-control border-start-0 ps-0" required minlength="6">
                        <span class="input-group-text toggle-pass" data-target="newPass">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="confirmPass" name="xacnhan_matkhau" class="form-control border-start-0 ps-0" required>
                        <span class="input-group-text toggle-pass" data-target="confirmPass">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                    <div id="passError" class="text-danger small mt-1" style="display:none;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Mật khẩu không khớp
                    </div>
                </div>

                <button type="submit" class="btn btn-main w-100 py-2">Cập nhật mật khẩu</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const pass = document.getElementById('newPass').value;
            const confirm = document.getElementById('confirmPass').value;
            const errorDiv = document.getElementById('passError');
            if (pass !== confirm) {
                e.preventDefault();
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        });

        document.querySelectorAll('.toggle-pass').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>