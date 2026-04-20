<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['nguoidung'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$order_id = intval($_POST['order_id'] ?? 0);
$user_id = $_SESSION['nguoidung']['idUser'];

if (!$order_id || !isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] != 0) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin hoặc lỗi upload']);
    exit;
}

$stmt = $conn->prepare("SELECT idUser FROM donhang WHERE MaDonHang = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order || $order['idUser'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Đơn hàng không hợp lệ']);
    exit;
}

$uploadDir = '../uploads/transfer_proof/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$file = $_FILES['proof_image'];
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file ảnh']);
    exit;
}

$newName = 'proof_' . $order_id . '_' . time() . '.' . $ext;
$targetFile = $uploadDir . $newName;

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    $stmt = $conn->prepare("UPDATE donhang SET HinhAnhTT = ? WHERE MaDonHang = ?");
    $stmt->execute([$newName, $order_id]);
    echo json_encode(['success' => true, 'message' => 'Tải ảnh thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi upload file']);
}
?>