<?php
$bodyClass = 'about-page';

$aboutStats = [
	'products' => 0,
	'categories' => 0,
	'orders' => 0,
	'customers' => 0,
];

if (isset($conn) && $conn && isset($GLOBALS['db_ready']) && $GLOBALS['db_ready']) {
	$aboutStats['products'] = db_count($conn, 'sanpham');
	$aboutStats['categories'] = db_count($conn, 'danhmuc');
	$aboutStats['orders'] = db_count($conn, 'donhang');
	$aboutStats['customers'] = db_count($conn, 'nguoidung', "VaiTro = 'khachhang'");
}

include "../layout/header_public.php";
?>

<main>
	<section class="about-hero">
		<div class="container">
			<div class="row align-items-center g-4 g-xl-5">
				<div class="col-lg-6">
					<p class="about-kicker">Về Aura Jewelry</p>
					<h1>Tinh thần trang sức hiện đại, giữ trọn nét thanh lịch Á Đông</h1>
					<p>
						Aura Jewelry mang đến những bộ sưu tập có độ hoàn thiện cao, thiết kế tinh giản
						nhưng giàu điểm nhấn. Mỗi sản phẩm đều được chọn lọc kỹ về chất liệu, tỷ lệ
						và cảm giác đeo để bạn luôn tự tin trong mọi khoảnh khắc.
					</p>
					<div class="about-hero-points">
						<span><i class="bi bi-check2-circle"></i> Thiết kế độc quyền theo xu hướng</span>
						<span><i class="bi bi-check2-circle"></i> Quy trình hoàn thiện tỉ mỉ</span>
						<span><i class="bi bi-check2-circle"></i> Tư vấn phong cách cá nhân</span>
					</div>
					<div class="about-hero-actions">
						<a href="lien-he.php" class="btn btn-about-outline">Liên hệ tư vấn</a>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="video-wrapper rounded-4 overflow-hidden shadow-lg">
						<video controls autoplay muted loop class="w-100">
							<source src="../img/vid1.mp4" type="video/mp4">
							Trình duyệt không hỗ trợ video.
						</video>
					</div>
					<div class="text-center mt-3">
						<p class="text-muted small">✨ Xu hướng trang sức mới nhất từ Aura Jewelry</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="about-intro py-5">
		<div class="container">
			<div class="about-intro-card text-center">
				<p class="about-section-label">Về chúng tôi</p>
				<h2>Aura Jewelry là nơi mỗi món trang sức trở thành dấu ấn cá nhân</h2>
				<p>
					Chúng tôi tin rằng trang sức không chỉ là phụ kiện. Đó là cách bạn kể câu chuyện
					về phong cách, sự tự tin và những cột mốc đáng nhớ trong hành trình sống.
				</p>
			</div>
		</div>
	</section>

	<section class="about-features pb-5">
		<div class="container">
			<div class="about-section-label text-center w-100 mb-4">Giá trị thương hiệu</div>
			<div class="row row-cols-2 row-cols-md-4 g-4 text-center about-features-row">
				<div class="col">
					<div class="about-feature-card">
						<div class="about-feature-icon"><i class="bi bi-gem"></i></div>
						<h4>Đẳng cấp</h4>
						<p>Thiết kế sang trọng, tinh tế và dễ ứng dụng.</p>
					</div>
				</div>
				<div class="col">
					<div class="about-feature-card">
						<div class="about-feature-icon"><i class="bi bi-award"></i></div>
						<h4>Chất lượng</h4>
						<p>Nguyên liệu được tuyển chọn cẩn thận, hoàn thiện tỉ mỉ.</p>
					</div>
				</div>
				<div class="col">
					<div class="about-feature-card">
						<div class="about-feature-icon"><i class="bi bi-shield-check"></i></div>
						<h4>Uy tín</h4>
						<p>Minh bạch về sản phẩm, dịch vụ tận tâm.</p>
					</div>
				</div>
				<div class="col">
					<div class="about-feature-card">
						<div class="about-feature-icon"><i class="bi bi-headset"></i></div>
						<h4>Dịch vụ</h4>
						<p>Tư vấn nhanh, hỗ trợ rõ ràng và chuyên nghiệp.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="about-story py-4">
		<div class="container">
			<div class="about-craft-feature">
				<div class="about-craft-image">
					<img src="../img/bgr10.jpg" alt="Nghệ nhân chế tác trang sức">
				</div>
				<div class="about-craft-content">
					<p class="about-section-label text-start">Chuyên gia chế tác trang sức</p>
					<h2>Cam kết trao tay sản phẩm có thẩm mỹ chuẩn mực và độ hoàn thiện cao</h2>
					<p>
						Từ khâu chọn chất liệu đến kiểm soát hoàn thiện, Aura luôn duy trì tiêu chuẩn
						nghiêm ngặt để đảm bảo độ bền, độ sáng và cảm giác đeo thoải mái. Chúng tôi
						theo đuổi vẻ đẹp tinh giản, sang trọng và bền vững theo thời gian.
					</p>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
include "../layout/footer_public.php";
?>