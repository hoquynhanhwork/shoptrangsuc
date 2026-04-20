<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$adminId = $_SESSION['nguoidung']['idUser'];
$action = $_POST['action'] ?? '';

if ($action === 'update_profile') {
    $hoten = trim($_POST['hoten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sodienthoai = trim($_POST['sodienthoai'] ?? '');

    $errors = [];
    if (empty($hoten)) $errors[] = 'Họ tên không được để trống.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE nguoidung SET HoTen = ?, Email = ?, SoDienThoai = ? WHERE idUser = ?");
        if ($stmt->execute([$hoten, $email, $sodienthoai, $adminId])) {
            $_SESSION['nguoidung']['HoTen'] = $hoten;
            $_SESSION['nguoidung']['Email'] = $email;
            $_SESSION['nguoidung']['SoDienThoai'] = $sodienthoai;
            $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Cập nhật thông tin thành công.'];
        } else {
            $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Lỗi cập nhật dữ liệu.'];
        }
    } else {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => implode('<br>', $errors)];
    }
} 
elseif ($action === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Vui lòng nhập đầy đủ thông tin.'];
    } elseif (strlen($new_password) < 6) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Mật khẩu mới phải có ít nhất 6 ký tự.'];
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Mật khẩu xác nhận không khớp.'];
    } else {
        $stmt = $conn->prepare("SELECT MatKhau FROM nguoidung WHERE idUser = ?");
        $stmt->execute([$adminId]);
        $user = $stmt->fetch();
        if (!password_verify($current_password, $user['MatKhau'])) {
            $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Mật khẩu hiện tại không đúng.'];
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE nguoidung SET MatKhau = ? WHERE idUser = ?");
            if ($stmt->execute([$hashed, $adminId])) {
                $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Đổi mật khẩu thành công.'];
            } else {
                $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Lỗi cập nhật mật khẩu.'];
            }
        }
    }
} 
else {
    $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Hành động không hợp lệ.'];
}

header('Location: cai-dat.php');
exit;
?>