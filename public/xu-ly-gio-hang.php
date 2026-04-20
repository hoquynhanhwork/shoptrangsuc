<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['nguoidung'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$idUser = $_SESSION['nguoidung']['idUser'];
$action = $_POST['action'] ?? '';

if ($action === 'get_cart') {
    $stmt = $conn->prepare("
        SELECT gh.idSize as id_size, gh.SoLuong, sp.Gia as gia, sp.MaSanPham, sp.TenSanPham, sp.HinhAnh, s.size
        FROM giohang gh
        JOIN size s ON gh.idSize = s.id
        JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
        WHERE gh.idUser = ?
        ORDER BY gh.MaGioHang DESC
    ");
    $stmt->execute([$idUser]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'cartItems' => $cartItems]);
    exit;
}

if ($action === 'add') {
    $id_size = intval($_POST['id_size'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($quantity < 1) $quantity = 1;

    if ($id_size <= 0) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin size.']);
        exit;
    }

    $stmtStock = $conn->prepare("SELECT SoLuongTon FROM size WHERE id = ?");
    $stmtStock->execute([$id_size]);
    $stock = $stmtStock->fetchColumn();
    if ($stock === false) {
        echo json_encode(['success' => false, 'message' => 'Size không tồn tại.']);
        exit;
    }
    if ($stock < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Số lượng tồn kho không đủ.']);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO giohang (idUser, idSize, SoLuong) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE SoLuong = SoLuong + ?
    ");
    $stmt->execute([$idUser, $id_size, $quantity, $quantity]);

    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'update') {
    $id_size = intval($_POST['id_size']);
    $quantity = intval($_POST['quantity']);
    if ($quantity < 1) $quantity = 1;

    $stmtStock = $conn->prepare("SELECT SoLuongTon FROM size WHERE id = ?");
    $stmtStock->execute([$id_size]);
    $stock = $stmtStock->fetchColumn();
    if ($stock < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Số lượng yêu cầu vượt quá tồn kho.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE giohang SET SoLuong = ? WHERE idUser = ? AND idSize = ?");
    $stmt->execute([$quantity, $idUser, $id_size]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'remove') {
    $id_size = intval($_POST['id_size']);
    $stmt = $conn->prepare("DELETE FROM giohang WHERE idUser = ? AND idSize = ?");
    $stmt->execute([$idUser, $id_size]);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM giohang WHERE idUser = ?");
    $stmt->execute([$idUser]);
    $cart_count = $stmt->fetchColumn();
    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
    exit;
}

if ($action === 'get_selected_total') {
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
    if (!is_array($ids) || empty($ids)) {
        echo json_encode(['success' => false, 'total' => 0]);
        exit;
    }
    $ids = array_map('intval', $ids);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("
        SELECT SUM(sp.Gia * gh.SoLuong) as total
        FROM giohang gh
        JOIN size s ON gh.idSize = s.id
        JOIN sanpham sp ON s.MaSanPham = sp.MaSanPham
        WHERE gh.idUser = ? AND gh.idSize IN ($placeholders)
    ");
    $params = array_merge([$idUser], $ids);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    echo json_encode(['success' => true, 'total' => (float)$total]);
    exit;
}

if ($action === 'prepare_checkout') {
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
    if (!is_array($ids) || empty($ids)) {
        echo json_encode(['success' => false]);
        exit;
    }
    $ids = array_map('intval', $ids);
    $_SESSION['checkout_items'] = $ids;
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
?>