<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

// ==================== XỬ LÝ CALLBACK TỪ MoMo ====================
if (isset($_GET['orderId']) || isset($_POST['orderId'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $fakeOrderId = $_GET['orderId'] ?? '';
        $resultCode = $_GET['resultCode'] ?? -1;
        $message = $_GET['message'] ?? '';
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        $fakeOrderId = $data['orderId'] ?? '';
        $resultCode = $data['resultCode'] ?? -1;
        $message = $data['message'] ?? '';
        if ($resultCode == 0 && $fakeOrderId && isset($_SESSION['momo_fake_order_' . $fakeOrderId])) {
            $realOrderId = $_SESSION['momo_fake_order_' . $fakeOrderId];
            $stmt = $conn->prepare("UPDATE donhang SET MaTrangThai = 4 WHERE MaDonHang = ?");
            $stmt->execute([$realOrderId]);
            unset($_SESSION['momo_fake_order_' . $fakeOrderId]);
        }
        http_response_code(204);
        exit;
    }
    if ($resultCode == 0 && $fakeOrderId && isset($_SESSION['momo_fake_order_' . $fakeOrderId])) {
        $realOrderId = $_SESSION['momo_fake_order_' . $fakeOrderId];
        $stmt = $conn->prepare("UPDATE donhang SET MaTrangThai = 4 WHERE MaDonHang = ? AND idUser = ?");
        $stmt->execute([$realOrderId, $_SESSION['nguoidung']['idUser']]);
        $_SESSION['success'] = "Thanh toán MoMo thành công! Đơn hàng #$realOrderId đã được xác nhận.";
        unset($_SESSION['momo_fake_order_' . $fakeOrderId]);
    } else {
        $_SESSION['error'] = "Thanh toán MoMo không thành công: $message";
    }
    header("Location: tai-khoan.php");
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ==================== LẤY GIỎ HÀNG (CHO TRANG THANH TOÁN) ====================
if ($action === 'get_cart') {
    if (!isset($_SESSION['nguoidung'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }
    $idUser = $_SESSION['nguoidung']['idUser'];
    $buyNow = isset($_POST['buy_now']) && $_POST['buy_now'] == 1;
    $cartItems = [];
    $tongTien = 0;

    if ($buyNow) {
        $id_size = intval($_POST['id_size'] ?? 0);
        $qty = intval($_POST['qty'] ?? 1);
        if ($qty < 1) $qty = 1;

        if ($id_size <= 0) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin size.']);
            exit;
        }
        $stmt = $conn->prepare("
            SELECT s.id as id_size, sp.Gia as gia, sp.TenSanPham, sp.HinhAnh, sp.MaSanPham, s.size
            FROM size s
            JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
            WHERE s.id = ?
        ");
        $stmt->execute([$id_size]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            exit;
        }
        $stmtStock = $conn->prepare("SELECT SoLuongTon FROM size WHERE id = ?");
        $stmtStock->execute([$id_size]);
        $stock = $stmtStock->fetchColumn();
        if ($stock < $qty) {
            echo json_encode(['success' => false, 'message' => 'Số lượng yêu cầu vượt quá tồn kho.']);
            exit;
        }
        $cartItems[] = [
            'id_size' => $item['id_size'],
            'SoLuong' => $qty,
            'gia' => $item['gia'],
            'TenSanPham' => $item['TenSanPham'],
            'HinhAnh' => $item['HinhAnh'],
            'MaSanPham' => $item['MaSanPham'],
            'size' => $item['size']
        ];
        $tongTien = $item['gia'] * $qty;
    } else {
        $selectedIds = [];
        if (isset($_POST['ids']) && is_array($_POST['ids']) && !empty($_POST['ids'])) {
            $selectedIds = array_map('intval', $_POST['ids']);
        } elseif (isset($_SESSION['checkout_items']) && is_array($_SESSION['checkout_items'])) {
            $selectedIds = array_map('intval', $_SESSION['checkout_items']);
            unset($_SESSION['checkout_items']);
        }
        if (empty($selectedIds)) {
            $stmt = $conn->prepare("
                SELECT gh.idSize as id_size, gh.SoLuong, sp.Gia as gia, sp.TenSanPham, sp.HinhAnh, sp.MaSanPham, s.size
                FROM giohang gh
                JOIN size s ON gh.idSize = s.id
                JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
                WHERE gh.idUser = ?
            ");
            $stmt->execute([$idUser]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
            $sql = "
                SELECT gh.idSize as id_size, gh.SoLuong, sp.Gia as gia, sp.TenSanPham, sp.HinhAnh, sp.MaSanPham, s.size
                FROM giohang gh
                JOIN size s ON gh.idSize = s.id
                JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
                WHERE gh.idUser = ? AND gh.idSize IN ($placeholders)
            ";
            $stmt = $conn->prepare($sql);
            $params = array_merge([$idUser], $selectedIds);
            $stmt->execute($params);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        foreach ($cartItems as $item) {
            $tongTien += $item['gia'] * $item['SoLuong'];
        }
    }
    echo json_encode(['success' => true, 'cartItems' => $cartItems, 'tongTien' => $tongTien]);
    exit;
}

// ==================== LẤY THÔNG TIN ĐƠN HÀNG CHO TRANG QR ====================
if ($action === 'get_order_details') {
    if (!isset($_SESSION['nguoidung'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }
    $order_id = intval($_GET['order_id'] ?? 0);
    $user_id = $_SESSION['nguoidung']['idUser'];
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']);
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM donhang WHERE MaDonHang = ? AND idUser = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
        exit;
    }
    $stmt = $conn->prepare("
        SELECT sp.TenSanPham, sp.HinhAnh, ct.SoLuong, ct.DonGia as Gia, s.size
        FROM chitietdonhang ct
        JOIN size s ON ct.id_size = s.id
        JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
        WHERE ct.MaDonHang = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = 0;
    foreach ($items as $item) {
        $total += $item['Gia'] * $item['SoLuong'];
    }
    
    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items,
        'total' => $total
    ]);
    exit;
}

// ==================== XÁC NHẬN THANH TOÁN NGÂN HÀNG ====================
if ($action === 'confirm_bank') {
    if (!isset($_SESSION['nguoidung'])) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }
    $order_id = intval($_GET['order_id'] ?? 0);
    $user_id = $_SESSION['nguoidung']['idUser'];
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu mã đơn hàng']);
        exit;
    }
    $stmt = $conn->prepare("SELECT MaTrangThai FROM donhang WHERE MaDonHang = ? AND idUser = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE donhang SET MaTrangThai = 4 WHERE MaDonHang = ? AND idUser = ?");
    $stmt->execute([$order_id, $user_id]);
    echo json_encode(['success' => true, 'message' => 'Cảm ơn bạn đã thanh toán. Đơn hàng sẽ được xử lý sớm.']);
    exit;
}

// ==================== XỬ LÝ TẠO ĐƠN HÀNG ====================
if (!isset($_SESSION['nguoidung'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$user = $_SESSION['nguoidung'];
$idUser = $user['idUser'];

$fullname = $_POST['fullname'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$province_code = $_POST['province_code'] ?? '';
$province_name = $_POST['province_name'] ?? '';
$ward_name = $_POST['ward_name'] ?? '';
$street = $_POST['street'] ?? '';
$notes = $_POST['notes'] ?? '';
$payment = $_POST['payment'] ?? '';
$ship_fee = intval($_POST['ship_fee'] ?? 0);
$buyNow = isset($_POST['buy_now']) && $_POST['buy_now'] == 1;

if (empty($fullname) || empty($phone) || empty($address) || empty($payment)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
    exit;
}

$cart = [];
$selectedIds = [];

if ($buyNow) {
    $id_size = intval($_POST['id_size'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($quantity < 1) $quantity = 1;
    if ($id_size <= 0) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin size.']);
        exit;
    }
    $stmt = $conn->prepare("
        SELECT s.id as id_size, s.SoLuongTon, sp.Gia as gia
        FROM size s
        JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
        WHERE s.id = ?
    ");
    $stmt->execute([$id_size]);
    $sizeInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$sizeInfo) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
        exit;
    }
    if ($sizeInfo['SoLuongTon'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Số lượng tồn kho không đủ.']);
        exit;
    }
    $cart[] = [
        'id_size' => $sizeInfo['id_size'],
        'SoLuong' => $quantity,
        'gia' => $sizeInfo['gia']
    ];
    $selectedIds = [$id_size];
} else {
    if (isset($_POST['ids']) && is_array($_POST['ids']) && !empty($_POST['ids'])) {
        $selectedIds = array_map('intval', $_POST['ids']);
    } elseif (isset($_SESSION['checkout_items']) && is_array($_SESSION['checkout_items'])) {
        $selectedIds = array_map('intval', $_SESSION['checkout_items']);
        unset($_SESSION['checkout_items']);
    }
    if (empty($selectedIds)) {
        $stmt = $conn->prepare("
            SELECT gh.idSize as id_size, gh.SoLuong, sp.Gia as gia
            FROM giohang gh
            JOIN size s ON gh.idSize = s.id
            JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
            WHERE gh.idUser = ?
        ");
        $stmt->execute([$idUser]);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $selectedIds = array_column($cart, 'id_size');
    } else {
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $sql = "
            SELECT gh.idSize as id_size, gh.SoLuong, sp.Gia as gia
            FROM giohang gh
            JOIN size s ON gh.idSize = s.id
            JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
            WHERE gh.idUser = ? AND gh.idSize IN ($placeholders)
        ";
        $stmt = $conn->prepare($sql);
        $params = array_merge([$idUser], $selectedIds);
        $stmt->execute($params);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Không có sản phẩm nào được chọn.']);
        exit;
    }
    foreach ($cart as $item) {
        $stmtStock = $conn->prepare("SELECT SoLuongTon FROM size WHERE id = ?");
        $stmtStock->execute([$item['id_size']]);
        $stock = $stmtStock->fetchColumn();
        if ($stock < $item['SoLuong']) {
            echo json_encode(['success' => false, 'message' => 'Số lượng tồn kho không đủ cho một số sản phẩm.']);
            exit;
        }
    }
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['gia'] * $item['SoLuong'];
}
$total = $subtotal + $ship_fee;

$maDonHang = 'DH' . time() . rand(100, 999);

$conn->beginTransaction();
try {
    $stmtTT = $conn->prepare("SELECT MaTrangThai FROM trangthaidonhang WHERE TenTrangThai = 'Đang xử lý' LIMIT 1");
    $stmtTT->execute();
    $rowTT = $stmtTT->fetch(PDO::FETCH_ASSOC);
    $maTrangThai = $rowTT ? $rowTT['MaTrangThai'] : 1;

    $stmt = $conn->prepare("
        INSERT INTO donhang (
            MaDonHang, idUser, TenKhachHang, SoDienThoai, DiaChiGiaoHang,
            TongTien, PhiShip, PhuongThucThanhToan, GhiChu, MaTrangThai, NgayDatHang
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $maDonHang, $idUser, $fullname, $phone, $address,
        $total, $ship_fee, $payment, $notes, $maTrangThai
    ]);
    $orderId = $conn->lastInsertId();

    $stmtDetail = $conn->prepare("INSERT INTO chitietdonhang (MaDonHang, id_size, SoLuong, DonGia) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $stmtDetail->execute([$orderId, $item['id_size'], $item['SoLuong'], $item['gia']]);
    }

    $stmtUpdateStock = $conn->prepare("UPDATE size SET SoLuongTon = SoLuongTon - ? WHERE id = ?");
    foreach ($cart as $item) {
        $stmtUpdateStock->execute([$item['SoLuong'], $item['id_size']]);
    }

    if (!$buyNow && !empty($selectedIds)) {
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $stmtDel = $conn->prepare("DELETE FROM giohang WHERE idUser = ? AND idSize IN ($placeholders)");
        $paramsDel = array_merge([$idUser], $selectedIds);
        $stmtDel->execute($paramsDel);
    }

    $conn->commit();

    $_SESSION['last_order_id'] = $orderId;

    echo json_encode(['success' => true, 'order_id' => $orderId, 'order_code' => $maDonHang]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>