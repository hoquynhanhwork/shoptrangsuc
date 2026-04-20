<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
$sosao = intval($_POST['sosao'] ?? 0);
$noidung = trim($_POST['noidung'] ?? '');

if ($product_id <= 0 || $sosao < 1 || $sosao > 5 || empty($noidung)) {
    $_SESSION['review_error'] = 'Vui lòng nhập đầy đủ thông tin đánh giá.';
    header("Location: chi-tiet-san-pham.php?id=$product_id#reviews-section");
    exit;
}

$idUser = $_SESSION['nguoidung']['idUser'];

try {
    $stmt = $conn->prepare("INSERT INTO danhgia (MaSanPham, idUser, SoSao, NoiDung) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, $idUser, $sosao, $noidung]);
    header("Location: chi-tiet-san-pham.php?id=$product_id&review=success#reviews-section");
    exit;
} catch (PDOException $e) {
    $_SESSION['review_error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
    header("Location: chi-tiet-san-pham.php?id=$product_id#reviews-section");
    exit;
}
?>