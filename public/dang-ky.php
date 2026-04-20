<?php
session_start();
if (isset($_SESSION['nguoidung'])) {
    header('Location: trang-chu.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Jewelry Store</title>
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
        <h4 class="text-center mb-3 fw-semibold">Đăng ký tài khoản</h4>

        <?php if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php foreach ($_SESSION['errors'] as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <form action="xu-ly-tai-khoan.php" method="POST" id="registerForm">
            <input type="hidden" name="action" value="dangky">

            <div class="mb-2">
                <label class="form-label fw-semibold">Họ và tên</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="hoten" class="form-control border-start-0 ps-0" 
                           value="<?= htmlspecialchars($_SESSION['old']['hoten'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person-badge"></i></span>
                    <input type="text" name="tendangnhap" class="form-control border-start-0 ps-0" 
                           value="<?= htmlspecialchars($_SESSION['old']['tendangnhap'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 ps-0" 
                           value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">Số điện thoại</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-phone"></i></span>
                    <input type="tel" name="sodienthoai" class="form-control border-start-0 ps-0" 
                           value="<?= htmlspecialchars($_SESSION['old']['sodienthoai'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" id="regPass" name="matkhau" class="form-control border-start-0 ps-0" required>
                    <span class="input-group-text toggle-pass" data-target="regPass">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" id="regConfirmPass" name="xacnhan_matkhau" class="form-control border-start-0 ps-0" required>
                    <span class="input-group-text toggle-pass" data-target="regConfirmPass">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
                <div id="passError" class="text-danger small mt-1" style="display:none;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Mật khẩu không khớp
                </div>
            </div>

            <button type="submit" class="btn btn-main w-100 py-2">Đăng ký ngay</button>

            <div class="hr-text">
                <span>Hoặc đăng ký với</span>
            </div>

            <a href="google-login.php" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" alt="Google">
                Đăng nhập bằng Google
            </a>

            <p class="text-center mt-3 mb-0">
                <span style="color: #e4d4d6;">Đã có tài khoản?</span> 
                <a href="dang-nhap.php" class="text-decoration-none fw-semibold" style="color:#e4d4d6;">Đăng nhập ngay</a>
            </p>
        </form>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const pass = document.getElementById('regPass').value;
            const confirm = document.getElementById('regConfirmPass').value;
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

<?php
unset($_SESSION['old']);
?>