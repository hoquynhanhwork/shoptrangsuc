<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

require_once "../connect.php";

$aboutStats = [
	'products' => 0,
	'categories' => 0,
	'orders' => 0,
	'customers' => 0,
];

if ($conn && $GLOBALS['db_ready']) {
	$aboutStats['products'] = db_count($conn, 'sanpham');
	$aboutStats['categories'] = db_count($conn, 'danhmuc');
	$aboutStats['orders'] = db_count($conn, 'donhang');
	$aboutStats['customers'] = db_count($conn, 'nguoidung', "VaiTro = 'khachhang'");
}

include "../layout/header_public.php";
?>

<main class="about-page">
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
						<a href="bo-suu-tap.php" class="btn btn-about-primary">Khám phá bộ sưu tập</a>
						<a href="lien-he.php" class="btn btn-about-outline">Liên hệ tư vấn</a>
					</div>
				</div>

				<div class="col-lg-6">
					<div id="aboutCarousel" class="carousel slide carousel-fade about-carousel" data-bs-ride="carousel" data-bs-interval="3200" data-bs-pause="false">
						<div class="carousel-indicators">
							<button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
							<button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
							<button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
						</div>
						<div class="carousel-inner">
							<div class="carousel-item active">
								<img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=1400&q=80" class="d-block w-100" alt="Trang sức cao cấp Aura Jewelry">
								<div class="about-slide-overlay">
									<p>Bộ sưu tập Signature</p>
									<h3>Thiết kế sang trọng, cảm hứng đương đại</h3>
								</div>
							</div>
							<div class="carousel-item">
								<img src="https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&w=1400&q=80" class="d-block w-100" alt="Cận cảnh chi tiết trang sức">
								<div class="about-slide-overlay">
									<p>Tiêu chuẩn hoàn thiện</p>
									<h3>Đường nét sắc sảo, bền đẹp theo năm tháng</h3>
								</div>
							</div>
							<div class="carousel-item">
								<img src="https://images.unsplash.com/photo-1611241893603-3c359704e0ee?auto=format&fit=crop&w=1400&q=80" class="d-block w-100" alt="Trang sức phong cách thanh lịch">
								<div class="about-slide-overlay">
									<p>Phong cách ứng dụng</p>
									<h3>Tỏa sáng từ công sở đến những dịp đặc biệt</h3>
								</div>
							</div>
						</div>
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

	<section class="about-story py-5">
		<div class="container">
			<div class="about-craft-feature">
				<div class="about-craft-image">
					<img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1200&q=80" alt="Nghệ nhân chế tác trang sức">
				</div>

				<div class="about-craft-content">
					<p class="about-section-label text-start">Chuyên gia chế tác trang sức</p>
					<h2>Cam kết trao tay sản phẩm có thẩm mỹ chuẩn mực và độ hoàn thiện cao</h2>
					<p>
						Từ khâu chọn chất liệu đến kiểm soát hoàn thiện, Aura luôn duy trì tiêu chuẩn
						nghiêm ngặt để đảm bảo độ bền, độ sáng và cảm giác đeo thoải mái. Chúng tôi
						theo đuổi vẻ đẹp tinh giản, sang trọng và bền vững theo thời gian.
					</p>
					<div class="about-craft-meta">
						<div>
							<span>Địa chỉ</span>
							<strong>123 Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh</strong>
						</div>
						<div>
							<span>Phong cách</span>
							<strong>Thanh lịch - Tinh giản - Sang trọng</strong>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="about-highlight pb-5">
		<div class="container">
			<div class="row g-3 text-center">
				<div class="col-6 col-md-3">
					<div class="about-metric">
						<span><?= number_format($aboutStats['products']) ?></span>
						<p>Sản phẩm đang hiển thị</p>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="about-metric">
						<span><?= number_format($aboutStats['categories']) ?></span>
						<p>Danh mục sản phẩm</p>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="about-metric">
						<span><?= number_format($aboutStats['orders']) ?></span>
						<p>Đơn hàng trong hệ thống</p>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="about-metric">
						<span><?= number_format($aboutStats['customers']) ?></span>
						<p>Khách hàng đã đăng ký</p>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
include "../layout/footer_public.php";
?>