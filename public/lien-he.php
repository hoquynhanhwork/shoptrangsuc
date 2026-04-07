<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

require_once "../connect.php";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$token = trim($_POST['contact_token'] ?? '');
	$website = trim($_POST['website'] ?? '');
	$now = time();
	$lastSubmit = (int) ($_SESSION['contact_last_submit_at'] ?? 0);
	$formLoadedAt = (int) ($_SESSION['contact_form_loaded_at'] ?? 0);

	if ($token === '' || !hash_equals($contactFormToken, $token)) {
		$_SESSION['contact_error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
		header('Location: lien-he.php');
		exit;
	}

	if ($website !== '') {
		$_SESSION['contact_error'] = 'Yêu cầu không hợp lệ.';
		header('Location: lien-he.php');
		exit;
	}

	if ($formLoadedAt > 0 && ($now - $formLoadedAt) < 3) {
		$_SESSION['contact_error'] = 'Bạn thao tác quá nhanh, vui lòng thử lại sau vài giây.';
		header('Location: lien-he.php');
		exit;
	}

	if ($lastSubmit > 0 && ($now - $lastSubmit) < 30) {
		$waitSeconds = 30 - ($now - $lastSubmit);
		$_SESSION['contact_error'] = "Bạn vừa gửi liên hệ. Vui lòng thử lại sau {$waitSeconds} giây.";
		header('Location: lien-he.php');
		exit;
	}

	$hoTen = trim($_POST['ho_ten'] ?? '');
	$soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$chuDe = trim($_POST['chu_de'] ?? '');
	$noiDung = trim($_POST['noi_dung'] ?? '');

	if ($hoTen === '' || $email === '' || $chuDe === '' || $noiDung === '') {
		$_SESSION['contact_error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
		header('Location: lien-he.php');
		exit;
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$_SESSION['contact_error'] = 'Email không hợp lệ.';
		header('Location: lien-he.php');
		exit;
	}

	$stmt = $conn->prepare('INSERT INTO lienhe (HoTen, SoDienThoai, Email, ChuDe, NoiDung) VALUES (?, ?, ?, ?, ?)');

	if ($stmt) {
		$stmt->bind_param('sssss', $hoTen, $soDienThoai, $email, $chuDe, $noiDung);

		if ($stmt->execute()) {
			$_SESSION['contact_last_submit_at'] = $now;
			$_SESSION['contact_success'] = 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất.';
		} else {
			$_SESSION['contact_error'] = 'Không thể lưu yêu cầu liên hệ, vui lòng thử lại.';
		}

		$stmt->close();
	} else {
		$_SESSION['contact_error'] = 'Hệ thống đang bận, vui lòng thử lại sau.';
	}

	header('Location: lien-he.php');
	exit;
}

$_SESSION['contact_form_loaded_at'] = time();

include "../layout/header_public.php";
?>

<main class="contact-page">
	<section class="contact-hero">
		<div class="container">
			<div class="row align-items-center g-4">
				<div class="col-lg-7 contact-hero-copy">
					<p class="contact-kicker">Liên hệ Aura Jewelry</p>
					<h1>Chúng tôi luôn sẵn sàng hỗ trợ bạn</h1>
					<p>
						Cần tư vấn chọn sản phẩm, hỗ trợ đơn hàng hay thông tin bảo hành?
						Aura luôn ưu tiên phản hồi nhanh, rõ ràng và tận tâm để bạn yên tâm
						trong suốt quá trình mua sắm.
					</p>
				</div>
				<div class="col-lg-5">
					<div class="contact-highlight-card">
						<div class="section-accent">
							<span class="section-accent-icon"><i class="bi bi-chat-dots"></i></span>
						</div>
						<p class="contact-highlight-label">Tư vấn nhanh</p>
						<h3>Liên hệ trực tiếp với Aura</h3>
						<div class="contact-highlight-list">
							<div class="contact-highlight-item">
								<i class="bi bi-telephone"></i>
								<div>
									<span>Hotline</span>
									<strong>0123 456 789</strong>
								</div>
							</div>
							<div class="contact-highlight-item">
								<i class="bi bi-envelope"></i>
								<div>
									<span>Email</span>
									<strong>contact@gmail.com</strong>
								</div>
							</div>
							<div class="contact-highlight-item">
								<i class="bi bi-geo-alt"></i>
								<div>
									<span>Showroom</span>
									<strong>TP. HCM</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="contact-content py-5">
		<div class="container">
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
			<div class="row g-4">
				<div class="col-lg-7 contact-panel contact-panel-form">
					<div class="contact-panel-head">
						<span class="contact-panel-tag">Gửi yêu cầu</span>
						<h2>Hãy để Aura hỗ trợ bạn</h2>
						<p>Điền thông tin bên dưới, chúng tôi sẽ phản hồi trong thời gian sớm nhất.</p>
					</div>
					<form class="contact-form contact-form-elevated" action="lien-he.php" method="post">
						<input type="hidden" name="contact_token" value="<?= htmlspecialchars($contactFormToken) ?>">
						<input type="text" name="website" value="" autocomplete="off" tabindex="-1" style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">Họ và tên</label>
								<input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" required>
							</div>
							<div class="col-md-6">
								<label class="form-label">Số điện thoại</label>
								<input type="text" name="so_dien_thoai" class="form-control" placeholder="09xx xxx xxx" required>
							</div>
							<div class="col-12">
								<label class="form-label">Email</label>
								<input type="email" name="email" class="form-control" placeholder="you@example.com" required>
							</div>
							<div class="col-12">
								<label class="form-label">Chủ đề</label>
								<input type="text" name="chu_de" class="form-control" placeholder="Ví dụ: Tư vấn sản phẩm" required>
							</div>
							<div class="col-12">
								<label class="form-label">Nội dung</label>
								<textarea name="noi_dung" rows="5" class="form-control" placeholder="Hãy cho chúng tôi biết bạn cần hỗ trợ điều gì..." required></textarea>
							</div>
							<div class="col-12">
								<div class="contact-note">
									<i class="bi bi-shield-check"></i>
									<span>Thông tin của bạn được bảo mật và chỉ dùng để phản hồi yêu cầu.</span>
								</div>
							</div>
							<div class="col-12 contact-submit-row">
								<button type="submit" class="btn btn-contact-submit">Gửi liên hệ</button>
							</div>
						</div>
					</form>
				</div>

				<div class="col-lg-5 contact-panel contact-panel-info">
					<div class="contact-info-card contact-info-block mb-3">
						<div class="section-accent">
							<span class="section-accent-icon"><i class="bi bi-clock"></i></span>
						</div>
						<span class="contact-panel-tag">Thông tin showroom</span>
						<h4>Giờ làm việc</h4>
						<div class="contact-info-row">
							<span>Thứ 2 - Thứ 6</span>
							<strong>08:30 - 21:00</strong>
						</div>
						<div class="contact-info-row">
							<span>Thứ 7 - Chủ nhật</span>
							<strong>09:00 - 17:00</strong>
						</div>
						<div class="contact-info-row">
							<span>Khu vực</span>
							<strong>TP. Hồ Chí Minh</strong>
						</div>
					</div>

					<div class="contact-info-card map-box">
						<div class="section-accent">
							<span class="section-accent-icon"><i class="bi bi-geo-alt"></i></span>
						</div>
						<span class="contact-panel-tag">Bản đồ</span>
						<h4>Vị trí cửa hàng</h4>
						<div class="map-frame">
							<iframe
								src="https://www.google.com/maps?q=Ho%20Chi%20Minh%20City&z=13&output=embed"
								width="100%"
								height="320"
								style="border:0;"
								allowfullscreen=""
								loading="lazy"
								referrerpolicy="no-referrer-when-downgrade">
							</iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
include "../layout/footer_public.php";
?>