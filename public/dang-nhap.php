<?php
session_start();
if (isset($_SESSION['nguoidung'])) {
    if ($_SESSION['nguoidung']['VaiTro'] === 'admin') {
        header('Location: ../admin/tong-quan.php');
    } else {
        header('Location: trang-chu.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Jewelry Store</title>
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
            <img src="../img/logo.jpg" alt="Jewelry Store">
        </div>
        <h4 class="text-center mb-3 fw-semibold">Đăng nhập</h4>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="xu-ly-tai-khoan.php?action=dangnhap" method="POST">
            <div class="mb-2">
                <label class="form-label fw-semibold">Số điện thoại hoặc Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="login_input" class="form-control border-start-0 ps-0" 
                           placeholder="Nhập số điện thoại hoặc tên đăng nhập" 
                           value="<?= htmlspecialchars($_SESSION['old_login']['login_input'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" id="loginPass" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                    <span class="input-group-text toggle-pass" data-target="loginPass">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
                <div class="text-end mt-1">
                    <a href="quen-mat-khau.php" class="small text-decoration-none" style="color: #e4d4d6;">Quên mật khẩu?</a>
                </div>
            </div>

            <button type="submit" class="btn btn-main w-100 py-2">Đăng nhập</button>

            <div class="hr-text">
                <span>Hoặc</span>
            </div>

            <a href="google.php" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" alt="Google">
                Đăng nhập bằng Google
            </a>

            <p class="text-center mt-3 mb-0">
                <span style="color: #e4d4d6;">Chưa có tài khoản?</span> 
                <a href="dang-ky.php" class="text-decoration-none fw-semibold" style="color:#e4d4d6;">Đăng ký ngay</a>
            </p>
        </form>
    </div>

    <script>
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
<?php
unset($_SESSION['old_login']);
?>