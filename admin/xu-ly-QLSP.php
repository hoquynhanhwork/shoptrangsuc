<?php
session_start();
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ========== XỬ LÝ THÊM / SỬA ==========
if ($action === 'add' || $action === 'edit') {
    $ma_danh_muc = intval($_POST['ma_danh_muc'] ?? 0);
    $ten_san_pham = trim($_POST['ten_san_pham'] ?? '');
    $duong_dan = trim($_POST['duong_dan'] ?? '');
    $mo_ta = trim($_POST['mo_ta'] ?? '');
    $chi_tiet = trim($_POST['chi_tiet'] ?? '');
    $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;
    $trang_thai = intval($_POST['trang_thai'] ?? 1);
    $gia = floatval($_POST['gia'] ?? 0);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    $errors = [];

    if (empty($ma_danh_muc)) $errors[] = 'Danh mục không được để trống.';
    if (empty($ten_san_pham)) $errors[] = 'Tên sản phẩm không được để trống.';
    if ($gia <= 0) $errors[] = 'Giá sản phẩm phải lớn hơn 0.';
    if (empty($mo_ta)) $errors[] = 'Mô tả ngắn không được để trống.';
    if (empty($chi_tiet)) $errors[] = 'Chi tiết sản phẩm không được để trống.';

    // Xử lý slug
    if (empty($duong_dan)) {
        $duong_dan = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s-]/', '', $ten_san_pham)));
        $duong_dan = preg_replace('/\s+/', '-', $duong_dan);
        $duong_dan = preg_replace('/-+/', '-', $duong_dan);
    }

    // Kiểm tra slug unique
    if ($action === 'add') {
        $stmtCheck = $conn->prepare("SELECT MaSanPham FROM sanpham WHERE DuongDan = ?");
        $stmtCheck->execute([$duong_dan]);
    } else {
        $stmtCheck = $conn->prepare("SELECT MaSanPham FROM sanpham WHERE DuongDan = ? AND MaSanPham != ?");
        $stmtCheck->execute([$duong_dan, $id]);
    }
    if ($stmtCheck->fetch()) {
        $errors[] = 'Đường dẫn (slug) đã tồn tại, vui lòng nhập slug khác.';
    }

    $hinh_anh = null;
    $xoa_hinh = isset($_POST['xoa_hinh']) && $_POST['xoa_hinh'] == 1;
    $old_img = '';

    if ($action === 'edit' && $id > 0) {
        $stmt = $conn->prepare("SELECT HinhAnh FROM sanpham WHERE MaSanPham = ?");
        $stmt->execute([$id]);
        $old_img = $stmt->fetchColumn();
        if ($xoa_hinh && $old_img) {
            $file_path = '../img/' . $old_img;
            if (file_exists($file_path)) unlink($file_path);
            $hinh_anh = '';
        }
    }

    $hasNewImage = isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0;
    if ($hasNewImage) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['hinh_anh']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Chỉ chấp nhận file ảnh (jpg, png, gif, webp).';
        } else {
            $new_name = time() . '_' . uniqid() . '.' . $ext;
            $upload_dir = '../img/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $upload_dir . $new_name)) {
                $hinh_anh = $new_name;
                if ($action === 'edit' && !$xoa_hinh && !empty($old_img) && file_exists($upload_dir . $old_img)) {
                    unlink($upload_dir . $old_img);
                }
            } else {
                $errors[] = 'Lỗi upload ảnh.';
            }
        }
    } else {
        if ($action === 'edit' && !$xoa_hinh && !empty($old_img)) {
            $hinh_anh = $old_img;
        } elseif ($action === 'add') {
            $errors[] = 'Vui lòng chọn ảnh sản phẩm.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => implode('<br>', $errors)];
        $redirectUrl = 'form-san-pham.php';
        if ($action === 'edit' && $id > 0) $redirectUrl .= '?id=' . $id;
        header('Location: ' . $redirectUrl);
        exit;
    }

    try {
        $no_size = isset($_POST['no_size']) ? 1 : 0;

        if ($action === 'add') {
            $stmt = $conn->prepare("
                INSERT INTO sanpham (MaDanhMuc, TenSanPham, DuongDan, MoTa, ChiTietSanPham, NoiBat, TrangThai, HinhAnh, Gia, NgayTao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$ma_danh_muc, $ten_san_pham, $duong_dan, $mo_ta, $chi_tiet, $noi_bat, $trang_thai, $hinh_anh, $gia]);
            $productId = $conn->lastInsertId();
            $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Thêm sản phẩm thành công.'];
        } else {
            $stmt = $conn->prepare("
                UPDATE sanpham 
                SET MaDanhMuc = ?, TenSanPham = ?, DuongDan = ?, MoTa = ?, ChiTietSanPham = ?, NoiBat = ?, TrangThai = ?, HinhAnh = ?, Gia = ?
                WHERE MaSanPham = ?
            ");
            $stmt->execute([$ma_danh_muc, $ten_san_pham, $duong_dan, $mo_ta, $chi_tiet, $noi_bat, $trang_thai, $hinh_anh, $gia, $id]);
            $productId = $id;
            $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Cập nhật sản phẩm thành công.'];
        }

        $stmtDel = $conn->prepare("DELETE FROM size WHERE MaSanPham = ?");
        $stmtDel->execute([$productId]);

        if ($no_size) {
            $totalStock = intval($_POST['total_stock'] ?? 0);
            if ($totalStock < 0) $totalStock = 0;
            $stmtIns = $conn->prepare("INSERT INTO size (MaSanPham, size, SoLuongTon) VALUES (?, ?, ?)");
            $stmtIns->execute([$productId, '0', $totalStock]);
        } else {
            if (isset($_POST['size_qty']) && is_array($_POST['size_qty'])) {
                $stmtIns = $conn->prepare("INSERT INTO size (MaSanPham, size, SoLuongTon) VALUES (?, ?, ?)");
                foreach ($_POST['size_qty'] as $sizeVal => $qty) {
                    $qty = intval($qty);
                    if ($qty >= 0 && $sizeVal != '0') { 
                        $stmtIns->execute([$productId, $sizeVal, $qty]);
                    }
                }
            }
        }

        header('Location: quan-ly-san-pham.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Lỗi CSDL: ' . $e->getMessage()];
        $redirectUrl = 'form-san-pham.php';
        if ($action === 'edit' && $id > 0) $redirectUrl .= '?id=' . $id;
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// ========== XỬ LÝ XÓA ==========
elseif ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT HinhAnh FROM sanpham WHERE MaSanPham = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    
    $stmtDelSize = $conn->prepare("DELETE FROM size WHERE MaSanPham = ?");
    $stmtDelSize->execute([$id]);
    
    $stmt = $conn->prepare("DELETE FROM sanpham WHERE MaSanPham = ?");
    if ($stmt->execute([$id])) {
        if ($img && file_exists('../img/' . $img)) {
            unlink('../img/' . $img);
        }
        $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Xóa sản phẩm thành công.'];
    } else {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Không thể xóa sản phẩm.'];
    }
    header('Location: quan-ly-san-pham.php');
    exit;
}

// ========== XỬ LÝ XÓA NHIỀU ==========
elseif ($action === 'delete_multi' && isset($_POST['ids'])) {
    $ids = $_POST['ids'];
    if (!is_array($ids)) {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Không có sản phẩm nào được chọn.'];
        header('Location: quan-ly-san-pham.php');
        exit;
    }
    $ids = array_map('intval', $ids);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $conn->prepare("SELECT HinhAnh FROM sanpham WHERE MaSanPham IN ($placeholders)");
    $stmt->execute($ids);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmtDelSize = $conn->prepare("DELETE FROM size WHERE MaSanPham IN ($placeholders)");
    $stmtDelSize->execute($ids);
    
    $stmt = $conn->prepare("DELETE FROM sanpham WHERE MaSanPham IN ($placeholders)");
    if ($stmt->execute($ids)) {
        foreach ($images as $img) {
            if (!empty($img) && file_exists('../img/' . $img)) {
                unlink('../img/' . $img);
            }
        }
        $_SESSION['admin_message'] = ['type' => 'success', 'text' => 'Đã xóa ' . count($ids) . ' sản phẩm.'];
    } else {
        $_SESSION['admin_message'] = ['type' => 'danger', 'text' => 'Có lỗi khi xóa sản phẩm.'];
    }
    header('Location: quan-ly-san-pham.php');
    exit;
}

else {
    header('Location: quan-ly-san-pham.php');
    exit;
}
?>