<?php
session_start();
// Nếu đã đăng nhập thì chuyển về trang chủ
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
    <title>Quên mật khẩu - Jewelry Store</title>
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
        <h4 class="text-center mb-3 fw-semibold">Quên mật khẩu</h4>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="xu-ly-tai-khoan.php" method="POST">
            <input type="hidden" name="action" value="quen-mat-khau">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email đăng ký</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 ps-0" 
                           value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-main w-100 py-2">Gửi yêu cầu</button>

            <div class="hr-text mt-4">
                <span>Hoặc</span>
            </div>

            <p class="text-center mt-3 mb-0">
                <a href="dang-nhap.php" class="text-decoration-none fw-semibold" style="color:#e4d4d6;">← Quay lại đăng nhập</a>
            </p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
unset($_SESSION['old']);
?>