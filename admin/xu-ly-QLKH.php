<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($id > 0 && ($action === 'lock' || $action === 'unlock')) {
    $status = ($action === 'lock') ? 0 : 1;
    try {
        $stmt = $conn->prepare("UPDATE nguoidung SET TrangThai = ? WHERE idUser = ? AND VaiTro = 'khachhang'");
        $stmt->execute([$status, $id]);
        $_SESSION['admin_message'] = [
            'type' => 'success',
            'text' => ($action === 'lock' ? 'Tài khoản đã bị khóa.' : 'Tài khoản đã được mở khóa.')
        ];
    } catch (Exception $e) {
        $_SESSION['admin_message'] = [
            'type' => 'danger',
            'text' => 'Lỗi: ' . $e->getMessage()
        ];
    }
} else {
    $_SESSION['admin_message'] = [
        'type' => 'danger',
        'text' => 'Hành động không hợp lệ.'
    ];
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'quan-ly-khach-hang.php';
header("Location: $redirect");
exit;
?>