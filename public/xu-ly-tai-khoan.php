<?php
session_start();
require_once '../config.php';

require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($conn)) {
    die('Lỗi: Không tìm thấy kết nối CSDL. Vui lòng kiểm tra file config.php');
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ==================== XỬ LÝ ĐĂNG KÝ ====================
if ($action === 'dangky') {
    $hoten       = trim($_POST['hoten'] ?? '');
    $tendangnhap = trim($_POST['tendangnhap'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $sodienthoai = trim($_POST['sodienthoai'] ?? '');
    $diachi      = trim($_POST['diachi'] ?? '');
    $matkhau     = $_POST['matkhau'] ?? '';
    $xacnhan     = $_POST['xacnhan_matkhau'] ?? '';

    $errors = [];
    if (empty($hoten)) $errors[] = 'Họ tên không được để trống';
    if (empty($tendangnhap)) $errors[] = 'Tên đăng nhập không được để trống';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
    if (empty($sodienthoai)) $errors[] = 'Số điện thoại không được để trống';
    if (strlen($matkhau) < 6) $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    if ($matkhau !== $xacnhan) $errors[] = 'Xác nhận mật khẩu không khớp';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT idUser FROM nguoidung WHERE TenDangNhap = ? OR Email = ? OR SoDienThoai = ?");
        $stmt->execute([$tendangnhap, $email, $sodienthoai]);
        if ($stmt->fetch()) {
            $errors[] = 'Tên đăng nhập, email hoặc số điện thoại đã tồn tại';
        } else {
            $hashed = password_hash($matkhau, PASSWORD_DEFAULT);
            $sql = "INSERT INTO nguoidung (HoTen, TenDangNhap, MatKhau, Email, SoDienThoai, DiaChi, VaiTro, TrangThai, NgayTao) 
                    VALUES (?, ?, ?, ?, ?, ?, 'khachhang', 1, NOW())";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$hoten, $tendangnhap, $hashed, $email, $sodienthoai, $diachi])) {
                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: dang-nhap.php');
                exit;
            } else {
                $errors[] = 'Lỗi hệ thống, vui lòng thử lại sau.';
            }
        }
    }
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: dang-ky.php');
    exit;
}

// ==================== XỬ LÝ ĐĂNG NHẬP ====================
elseif ($action === 'dangnhap') {
    $login_input = trim($_POST['login_input'] ?? '');
    $password    = $_POST['password'] ?? '';

    if (empty($login_input) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
        header('Location: dang-nhap.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE (Email = ? OR SoDienThoai = ? OR TenDangNhap = ?) AND DelAt = 0 LIMIT 1");
    $stmt->execute([$login_input, $login_input, $login_input]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['MatKhau'])) {
        if ($user['TrangThai'] == 1) {
            $_SESSION['nguoidung'] = [
                'idUser'      => $user['idUser'],
                'HoTen'       => $user['HoTen'],
                'TenDangNhap' => $user['TenDangNhap'],
                'Email'       => $user['Email'],
                'VaiTro'      => $user['VaiTro'],
                'SoDienThoai' => $user['SoDienThoai'],
                'DiaChi'      => $user['DiaChi']
            ];

            if ($user['VaiTro'] === 'admin') {
                header('Location: ../admin/tong-quan.php');
                exit;
            } else {
                $redirect = $_SESSION['redirect_after_login'] ?? 'trang-chu.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $_SESSION['error'] = 'Tài khoản đã bị khóa. Vui lòng liên hệ hỗ trợ.';
        }
    } else {
        $_SESSION['error'] = 'Sai tên đăng nhập / email / số điện thoại hoặc mật khẩu.';
        $_SESSION['old_login'] = ['login_input' => $login_input];
    }
    header('Location: dang-nhap.php');
    exit;
}

// ==================== ĐĂNG XUẤT ====================
elseif ($action === 'dangxuat') {
    $_SESSION = [];
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_start();
    $_SESSION['success'] = 'Bạn đã đăng xuất thành công.';
    header('Location: dang-nhap.php');
    exit;
}

// ==================== ĐỔI MẬT KHẨU ====================
elseif ($action === 'doi-mat-khau') {
    if (!isset($_SESSION['nguoidung'])) {
        $_SESSION['error'] = 'Vui lòng đăng nhập để thực hiện chức năng này.';
        header('Location: dang-nhap.php');
        exit;
    }

    $idUser = $_SESSION['nguoidung']['idUser'];
    $matkhau_moi = $_POST['matkhau_moi'] ?? '';
    $xacnhan_moi = $_POST['xacnhan_moi'] ?? '';
    $isGoogleAccount = isset($_POST['google_account']) && $_POST['google_account'] == '1';

    $errors = [];
    if (strlen($matkhau_moi) < 6) $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
    if ($matkhau_moi !== $xacnhan_moi) $errors[] = 'Xác nhận mật khẩu mới không khớp.';

    if (!$isGoogleAccount) {
        $matkhau_cu = $_POST['matkhau_cu'] ?? '';
        if (empty($matkhau_cu)) $errors[] = 'Vui lòng nhập mật khẩu cũ.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT MatKhau FROM nguoidung WHERE idUser = ?");
        $stmt->execute([$idUser]);
        $user = $stmt->fetch();

        if (!$isGoogleAccount) {
            if ($user && password_verify($matkhau_cu, $user['MatKhau'])) {
                $hashed_new = password_hash($matkhau_moi, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE nguoidung SET MatKhau = ? WHERE idUser = ?");
                if ($update->execute([$hashed_new, $idUser])) {
                    $_SESSION['success'] = 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.';
                    session_destroy();
                    session_start();
                    $_SESSION['success'] = 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.';
                    header('Location: dang-nhap.php');
                    exit;
                } else {
                    $errors[] = 'Lỗi hệ thống, không thể cập nhật mật khẩu.';
                }
            } else {
                $errors[] = 'Mật khẩu cũ không đúng.';
            }
        } else {
            $hashed_new = password_hash($matkhau_moi, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE nguoidung SET MatKhau = ? WHERE idUser = ?");
            if ($update->execute([$hashed_new, $idUser])) {
                $_SESSION['success'] = 'Đặt mật khẩu thành công. Bạn có thể đăng nhập bằng tài khoản thông thường.';
                header('Location: tai-khoan.php');
                exit;
            } else {
                $errors[] = 'Lỗi hệ thống, không thể cập nhật mật khẩu.';
            }
        }
    }

    $_SESSION['errors'] = $errors;
    header('Location: doi-mat-khau.php');
    exit;
}

// ==================== CẬP NHẬT THÔNG TIN ====================
elseif ($action === 'sua-thong-tin') {
    if (!isset($_SESSION['nguoidung'])) {
        $_SESSION['error'] = 'Vui lòng đăng nhập để thực hiện chức năng này.';
        header('Location: dang-nhap.php');
        exit;
    }

    $idUser = $_SESSION['nguoidung']['idUser'];
    $hoten = trim($_POST['hoten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sodienthoai = trim($_POST['sodienthoai'] ?? '');
    $provinceCode = trim($_POST['province'] ?? '');
    $wardCode = trim($_POST['ward'] ?? '');
    $street = trim($_POST['street'] ?? '');

    $errors = [];
    if (empty($hoten)) $errors[] = 'Họ tên không được để trống.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
    if (empty($sodienthoai)) $errors[] = 'Số điện thoại không được để trống.';
    if (empty($provinceCode)) $errors[] = 'Vui lòng chọn tỉnh/thành.';
    if (empty($wardCode)) $errors[] = 'Vui lòng chọn phường/xã.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT idUser FROM nguoidung WHERE Email = ? AND idUser != ?");
        $stmt->execute([$email, $idUser]);
        if ($stmt->fetch()) {
            $errors[] = 'Email đã được sử dụng bởi tài khoản khác.';
        } else {
            $json = @file_get_contents('../js/dia-chi.json');
            if ($json === false) {
                $errors[] = 'Không thể tải dữ liệu địa chỉ.';
            } else {
                $provinces = json_decode($json, true);
                $provinceName = $wardName = '';
                foreach ($provinces as $province) {
                    if ($province['Code'] == $provinceCode) {
                        $provinceName = $province['FullName'];
                        foreach ($province['Wards'] as $ward) {
                            if ($ward['Code'] == $wardCode) {
                                $wardName = $ward['FullName'];
                                break;
                            }
                        }
                        break;
                    }
                }
                if (empty($provinceName) || empty($wardName)) {
                    $errors[] = 'Dữ liệu địa chỉ không hợp lệ.';
                } else {
                    $fullAddress = '';
                    if (!empty($street)) $fullAddress .= $street;
                    if (!empty($wardName)) $fullAddress .= ($fullAddress ? ', ' : '') . $wardName;
                    if (!empty($provinceName)) $fullAddress .= ($fullAddress ? ', ' : '') . $provinceName;
                    $update = $conn->prepare("UPDATE nguoidung SET HoTen = ?, Email = ?, SoDienThoai = ?, DiaChi = ? WHERE idUser = ?");
                    if ($update->execute([$hoten, $email, $sodienthoai, $fullAddress, $idUser])) {
                        $_SESSION['nguoidung']['HoTen'] = $hoten;
                        $_SESSION['nguoidung']['Email'] = $email;
                        $_SESSION['nguoidung']['SoDienThoai'] = $sodienthoai;
                        $_SESSION['nguoidung']['DiaChi'] = $fullAddress;
                        $_SESSION['old'] = [
                            'province_code' => $provinceCode,
                            'ward_code' => $wardCode,
                            'street' => $street
                        ];

                        $_SESSION['success'] = 'Cập nhật thông tin thành công.';
                        header('Location: tai-khoan.php');
                        exit;
                    } else {
                        $errors[] = 'Lỗi hệ thống, không thể cập nhật thông tin.';
                    }
                }
            }
        }
    }

    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = array_merge($_SESSION['old'] ?? [], [
        'hoten' => $hoten,
        'email' => $email,
        'sodienthoai' => $sodienthoai,
        'province_code' => $provinceCode,
        'ward_code' => $wardCode,
        'street' => $street
    ]);
    header('Location: sua-tai-khoan.php');
    exit;
}
// ==================== QUÊN MẬT KHẨU ====================
elseif ($action === 'quen-mat-khau') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $_SESSION['error'] = 'Vui lòng nhập email.';
        header('Location: quen-mat-khau.php');
        exit;
    }
    $stmt = $conn->prepare("SELECT idUser, HoTen FROM nguoidung WHERE Email = ? AND DelAt = 0");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $_SESSION['error'] = 'Email không tồn tại trong hệ thống.';
        header('Location: quen-mat-khau.php');
        exit;
    }

    do {
        $token = (string) random_int(100000, 999999);
        $check = $conn->prepare("SELECT idUser FROM tokens WHERE token = ? AND HetHan > NOW()");
        $check->execute([$token]);
    } while ($check->fetch());

    $insertToken = $conn->prepare("INSERT INTO tokens (idUser, token, HetHan) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
    $insertToken->execute([$user['idUser'], $token]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nhom8.aurajewelry@gmail.com';
        $mail->Password   = 'dpun rgkg obes hmun';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('nhom8.aurajewelry@gmail.com', 'Aura Jewelry');
        $mail->addAddress($email, $user['HoTen']);

        $mail->isHTML(false);
        $mail->Subject = "Aura Jewelry";
        $mail->Body    = "Xin chào " . $user['HoTen'] . ",\n\n"
                        . "Bạn vừa yêu cầu đặt lại mật khẩu.\n"
                        . "Mã xác nhận của bạn là: " . $token . "\n\n"
                        . "Vui lòng nhập mã này vào trang đặt lại mật khẩu để tiếp tục.\n"
                        . "Mã có hiệu lực trong 5 phút.\n\n"
                        . "Nếu bạn không yêu cầu, vui lòng bỏ qua email này.\n\n"
                        . "Trân trọng,\nAura Jewelry";

        $mail->send();
        $_SESSION['success'] = 'Mã xác nhận đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư (kể cả thư rác).';
        header('Location: dat-lai-mat-khau.php');
        exit;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        $_SESSION['error'] = 'Không thể gửi email. Vui lòng thử lại sau.';
        header('Location: quen-mat-khau.php');
        exit;
    }
}

// ==================== ĐẶT LẠI MẬT KHẨU ====================
elseif ($action === 'reset-password') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['matkhau'] ?? '';
    $confirm = $_POST['xacnhan_matkhau'] ?? '';

    if (empty($token) || empty($password) || $password !== $confirm) {
        $_SESSION['error'] = 'Dữ liệu không hợp lệ hoặc mật khẩu không khớp.';
        header('Location: quen-mat-khau.php');
        exit;
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        header("Location: dat-lai-mat-khau.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT idUser FROM tokens WHERE token = ? AND HetHan > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $_SESSION['error'] = 'Mã xác nhận không hợp lệ hoặc đã hết hạn.';
        header('Location: quen-mat-khau.php');
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE nguoidung SET MatKhau = ? WHERE idUser = ?");
    if ($update->execute([$hashed, $user['idUser']])) {
        $delToken = $conn->prepare("DELETE FROM tokens WHERE token = ?");
        $delToken->execute([$token]);
        $_SESSION['success'] = 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.';
        header('Location: dang-nhap.php');
        exit;
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
        header("Location: dat-lai-mat-khau.php");
        exit;
    }
}
// ==================== MẶC ĐỊNH ====================
else {
    header('Location: trang-chu.php');
    exit;
}
?>