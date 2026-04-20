<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once "../connect.php";

$token = trim($_POST['contact_token'] ?? '');
$website = trim($_POST['website'] ?? '');
$now = time();

if (!isset($_SESSION['contact_form_token']) || $token === '' || !hash_equals($_SESSION['contact_form_token'], $token)) {
    $_SESSION['contact_error'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
    header('Location: lien-he.php');
    exit;
}

if ($website !== '') {
    $_SESSION['contact_error'] = 'Yêu cầu không hợp lệ.';
    header('Location: lien-he.php');
    exit;
}

$formLoadedAt = (int) ($_SESSION['contact_form_loaded_at'] ?? 0);
if ($formLoadedAt > 0 && ($now - $formLoadedAt) < 3) {
    $_SESSION['contact_error'] = 'Bạn thao tác quá nhanh, vui lòng thử lại sau vài giây.';
    header('Location: lien-he.php');
    exit;
}

$lastSubmit = (int) ($_SESSION['contact_last_submit_at'] ?? 0);
if ($lastSubmit > 0 && ($now - $lastSubmit) < 30) {
    $waitSeconds = 30 - ($now - $lastSubmit);
    $_SESSION['contact_error'] = "Bạn vừa gửi liên hệ. Vui lòng thử lại sau {$waitSeconds} giây.";
    header('Location: lien-he.php');
    exit;
}

$hoTen = trim($_POST['ho_ten'] ?? '');
$soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
$email = trim($_POST['email'] ?? '');
$chuDe = trim($_POST['chu_de'] ?? '');
$noiDung = trim($_POST['noi_dung'] ?? '');

if ($hoTen === '' || $email === '' || $chuDe === '' || $noiDung === '') {
    $_SESSION['contact_error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    header('Location: lien-he.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Email không hợp lệ.';
    header('Location: lien-he.php');
    exit;
}

$stmt = $conn->prepare('INSERT INTO lienhe (HoTen, SoDienThoai, Email, ChuDe, NoiDung) VALUES (?, ?, ?, ?, ?)');

if ($stmt) {
    $stmt->bind_param('sssss', $hoTen, $soDienThoai, $email, $chuDe, $noiDung);
    if ($stmt->execute()) {
        $_SESSION['contact_last_submit_at'] = $now;
        $_SESSION['contact_success'] = 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất.';
    } else {
        $_SESSION['contact_error'] = 'Không thể lưu yêu cầu liên hệ, vui lòng thử lại.';
    }
    $stmt->close();
} else {
    $_SESSION['contact_error'] = 'Hệ thống đang bận, vui lòng thử lại sau.';
}

header('Location: lien-he.php');
exit;
?>