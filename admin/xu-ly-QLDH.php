<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$maDonHang = isset($_POST['ma_don_hang']) ? intval($_POST['ma_don_hang']) : 0;
$newStatus = isset($_POST['trang_thai']) ? intval($_POST['trang_thai']) : 0;

if ($maDonHang > 0 && $newStatus > 0) {
    try {
        $stmt = $conn->prepare("UPDATE donhang SET MaTrangThai = ? WHERE MaDonHang = ?");
        $stmt->execute([$newStatus, $maDonHang]);
        $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Cập nhật trạng thái thành công.'];
    } catch (PDOException $e) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Lỗi: ' . $e->getMessage()];
    }
} else {
    $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Dữ liệu không hợp lệ.'];
}

header("Location: chi-tiet-don-hang.php?id=$maDonHang");
exit;
?>