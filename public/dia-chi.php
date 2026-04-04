<?php
session_start();
require_once '../config.php';
$bodyClass = 'map-page'; // đặt class cho body
include "../layout/header_public.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Địa chỉ cửa hàng - Aura Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/public.css">
    <!-- Không cần Leaflet nữa -->
</head>
<body class="map-page">
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="shop-info">
                    <h3><i class="bi bi-shop"></i> Aura Jewelry</h3>
                    <p><i class="bi bi-geo-alt-fill"></i> Địa chỉ: 02 Võ Oanh, Phường 25, Thạnh Mỹ Tây, Hồ Chí Minh, Việt Nam</p>
                    <p><i class="bi bi-telephone-fill"></i> (028) 1234 5678</p>
                    <p><i class="bi bi-envelope-fill"></i> nhom8.aurajewelry@gmail.com</p>
                    <p><i class="bi bi-clock-fill"></i> 9:00 - 21:00 (Thứ 2 - Chủ nhật)</p>
                    <hr>
                    <p class="small text-muted">Hãy đến với chúng tôi để trải nghiệm những sản phẩm trang sức tinh tế và dịch vụ chuyên nghiệp.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.296550158529!2d106.7121676!3d10.8045178!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175282c59f550dd%3A0xaeebf597f347e724!2zMDIgVsO1IE9hbmgsIFBoxrDhu51uZyAyNSwgUXXhuq1uIDEsIFRow6BuaCBwaOG7kSBI4buTIENow60gTWluaCwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1746437061125!5m2!1svi!2s"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include "../layout/footer_public.php"; ?>