<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action && $id) {
    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM danhgia WHERE MaDanhGia = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Đã xóa đánh giá.'];
        } else {
            $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Xóa thất bại.'];
        }
    } 
    else {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Hành động không hợp lệ.'];
    }
} else {
    $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Thiếu thông tin xử lý.'];
}

header('Location: quan-ly-danh-gia.php');
exit;
?>