<?php
session_start();
require_once '../config.php';// Kết nối CSDL (biến $conn)

// ==================== CẤU HÌNH GOOGLE OAUTH ====================
$google_client_id     = '812360409698-gj69cbdkqv10gt1kojhmog1jf5jqbldk.apps.googleusercontent.com';
$google_client_secret = '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';
$google_redirect_uri  = 'http://localhost/shoptrangsuc/public/google.php'; // ⚠️ SỬA LẠI CHO ĐÚNG URL CỦA BẠN
$google_app_name      = 'Jewelry Store';

// ==================== XỬ LÝ ĐĂNG NHẬP GOOGLE ====================
if (!isset($_GET['code'])) {
    // 1. Chưa có code -> chuyển hướng đến Google
    $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=email%20profile&access_type=offline&include_granted_scopes=true&response_type=code&redirect_uri=' . urlencode($google_redirect_uri) . '&client_id=' . $google_client_id;
    header('Location: ' . $auth_url);
    exit;
} else {
    // 2. Có code -> xử lý callback
    $code = $_GET['code'];

    // Lấy access token từ code
    $token_url = 'https://oauth2.googleapis.com/token';
    $post_data = [
        'code'          => $code,
        'client_id'     => $google_client_id,
        'client_secret' => $google_client_secret,
        'redirect_uri'  => $google_redirect_uri,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Nếu chạy local SSL có thể cần bỏ qua kiểm tra
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $token_data = json_decode($response, true);
    $access_token = $token_data['access_token'] ?? null;

    if (!$access_token) {
        $_SESSION['error'] = 'Không thể xác thực với Google. Vui lòng thử lại.';
        header('Location: dang-nhap.php');
        exit;
    }

    // Lấy thông tin người dùng từ Google
    $userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $access_token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userinfo_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $userinfo = curl_exec($ch);
    curl_close($ch);
    $user_data = json_decode($userinfo, true);

    if (!isset($user_data['email'])) {
        $_SESSION['error'] = 'Không thể lấy thông tin từ Google.';
        header('Location: dang-nhap.php');
        exit;
    }

    $google_id = $user_data['id'];
    $email     = $user_data['email'];
    $name      = $user_data['name'] ?? '';

    // Kiểm tra người dùng đã tồn tại trong CSDL chưa
    $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE id_google = ? OR Email = ?");
    $stmt->execute([$google_id, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Đã có tài khoản (có thể đăng ký bằng email thường)
        // Nếu user chưa có id_google thì cập nhật
        if (empty($user['id_google'])) {
            $update = $conn->prepare("UPDATE nguoidung SET id_google = ? WHERE idUser = ?");
            $update->execute([$google_id, $user['idUser']]);
        }
        // Kiểm tra trạng thái tài khoản
        if ($user['trang_thai'] != 1) {
            $_SESSION['error'] = 'Tài khoản đã bị khóa. Vui lòng liên hệ hỗ trợ.';
            header('Location: dang-nhap.php');
            exit;
        }
        // Đăng nhập
        $_SESSION['nguoidung'] = [
            'idUser'      => $user['idUser'],
            'HoTen'       => $user['HoTen'],
            'TenDangNhap' => $user['TenDangNhap'],
            'Email'       => $user['Email'],
            'VaiTro'      => $user['VaiTro'],
            'SoDienThoai' => $user['SoDienThoai'],
            'DiaChi'      => $user['DiaChi']
        ];
    } else {
        // Tạo tài khoản mới
        // Tạo tên đăng nhập từ email (bỏ phần @...)
        $username = explode('@', $email)[0];
        $base_username = $username;
        $counter = 1;
        while (true) {
            $stmt = $conn->prepare("SELECT idUser FROM nguoidung WHERE TenDangNhap = ?");
            $stmt->execute([$username]);
            if (!$stmt->fetch()) break;
            $username = $base_username . $counter;
            $counter++;
        }

        $sql = "INSERT INTO nguoidung (HoTen, TenDangNhap, Email, id_google, VaiTro, trang_thai, NgayTao) 
                VALUES (?, ?, ?, ?, 'khachhang', 1, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $username, $email, $google_id]);
        $new_id = $conn->lastInsertId();

        // Lấy user vừa tạo
        $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE idUser = ?");
        $stmt->execute([$new_id]);
        $user = $stmt->fetch();

        $_SESSION['nguoidung'] = [
            'idUser'      => $user['idUser'],
            'HoTen'       => $user['HoTen'],
            'TenDangNhap' => $user['TenDangNhap'],
            'Email'       => $user['Email'],
            'VaiTro'      => $user['VaiTro'],
            'SoDienThoai' => $user['SoDienThoai'],
            'DiaChi'      => $user['DiaChi']
        ];
    }

    // Chuyển hướng sau đăng nhập
    $redirect = $_SESSION['redirect_after_login'] ?? 'trang-chu.php';
    unset($_SESSION['redirect_after_login']);
    header('Location: ' . $redirect);
    exit;
}
?>