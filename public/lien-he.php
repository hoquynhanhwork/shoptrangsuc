<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once "../connect.php";

$bodyClass = 'contact-page';

$contactSuccess = $_SESSION['contact_success'] ?? '';
$contactError = $_SESSION['contact_error'] ?? '';
unset($_SESSION['contact_success'], $_SESSION['contact_error']);

if (empty($_SESSION['contact_form_token'])) {
    $_SESSION['contact_form_token'] = bin2hex(random_bytes(16));
}
$contactFormToken = $_SESSION['contact_form_token'];

if (empty($_SESSION['contact_form_loaded_at'])) {
    $_SESSION['contact_form_loaded_at'] = time();
}

include "../layout/header_public.php";
?>

<main class="contact-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <?php if ($contactSuccess): ?>
                    <div class="alert alert-success contact-alert">
                        <?= htmlspecialchars($contactSuccess) ?>
                    </div>
                <?php endif; ?>
                <?php if ($contactError): ?>
                    <div class="alert alert-danger contact-alert">
                        <?= htmlspecialchars($contactError) ?>
                    </div>
                <?php endif; ?>

                <div class="contact-form-container">
                    <div class="text-center mb-4">
                        <h1 class="h2">Liên hệ với chúng tôi</h1>
                        <p class="text-muted">Vui lòng điền thông tin bên dưới, chúng tôi sẽ phản hồi sớm nhất.</p>
                    </div>

                    <form class="contact-form" action="xu-ly-lien-he.php" method="post">
                        <input type="hidden" name="contact_token" value="<?= htmlspecialchars($contactFormToken) ?>">
                        <input type="text" name="website" value="" autocomplete="off" tabindex="-1" style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" class="form-control" placeholder="09xx xxx xxx">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                                <input type="text" name="chu_de" class="form-control" placeholder="Ví dụ: Tư vấn sản phẩm" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="noi_dung" rows="5" class="form-control" placeholder="Hãy cho chúng tôi biết bạn cần hỗ trợ điều gì..." required></textarea>
                            </div>
                            <div class="col-12">
                                <div class="contact-note">
                                    <i class="bi bi-shield-check"></i>
                                    <span>Thông tin của bạn được bảo mật và chỉ dùng để phản hồi yêu cầu.</span>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-contact-submit px-5">Gửi liên hệ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include "../layout/footer_public.php"; ?>