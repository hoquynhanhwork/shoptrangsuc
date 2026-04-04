<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['nguoidung'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$user = $_SESSION['nguoidung'];
$idUser = $user['idUser'];

// Nhận dữ liệu từ POST
$fullname = $_POST['fullname'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$province_code = $_POST['province_code'] ?? '';
$province_name = $_POST['province_name'] ?? '';
$ward_name = $_POST['ward_name'] ?? '';
$street = $_POST['street'] ?? '';
$notes = $_POST['notes'] ?? '';
$payment = $_POST['payment'] ?? '';
$ship_fee = $_POST['ship_fee'] ?? 0;

// Kiểm tra dữ liệu
if (empty($fullname) || empty($phone) || empty($address) || empty($payment)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
    exit;
}

// Lấy giỏ hàng của user
$stmt = $conn->prepare("
    SELECT gh.id_size, gh.SoLuong, s.gia, s.MaSanPham
    FROM giohang gh
    JOIN size s ON gh.id_size = s.id
    WHERE gh.idUser = ?
");
$stmt->execute([$idUser]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
    exit;
}

// Tính tổng tiền hàng
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['gia'] * $item['SoLuong'];
}
$total = $subtotal + $ship_fee;

// Tạo mã đơn hàng (có thể dùng UNIQUE hoặc tự tăng)
$maDonHang = 'DH' . time() . rand(100, 999);

// Bắt đầu transaction
$conn->beginTransaction();
try {
    // Insert vào bảng donhang
    $stmt = $conn->prepare("
        INSERT INTO donhang (MaDonHang, idUser, TenKhachHang, SoDienThoai, DiaChiGiaoHang, TongTien, PhiShip, PhuongThucThanhToan, GhiChu, TrangThai, NgayDat)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([$maDonHang, $idUser, $fullname, $phone, $address, $total, $ship_fee, $payment, $notes]);
    $orderId = $conn->lastInsertId(); // lấy id tự tăng (nếu có)

    // Insert chi tiết đơn hàng
    $stmt = $conn->prepare("
        INSERT INTO chitietdonhang (MaDonHang, id_size, SoLuong, DonGia)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cart as $item) {
        $stmt->execute([$orderId, $item['id_size'], $item['SoLuong'], $item['gia']]);
    }

    // Xóa giỏ hàng sau khi đặt thành công
    $stmt = $conn->prepare("DELETE FROM giohang WHERE idUser = ?");
    $stmt->execute([$idUser]);

    $conn->commit();

    echo json_encode(['success' => true, 'order_id' => $orderId, 'order_code' => $maDonHang]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}