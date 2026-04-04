<?php
session_start();
if (!isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
}
$action = $_GET['action'] ?? '';

if ($action == 'add') {

    $id = $_GET['id'] ?? null;
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

    if (!$id || $qty <= 0) {
        header("Location: trang-chu.php");
        exit();
    }

    if (isset($_SESSION['giohang'][$id])) {
        $_SESSION['giohang'][$id] += $qty;
    } else {
        $_SESSION['giohang'][$id] = $qty;
    }

    header("Location: gio-hang.php");
    exit();
}

if ($action == 'update') {

    if (isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $qty = (int)$qty;

            if ($qty <= 0) {
                unset($_SESSION['giohang'][$id]);
            } else {
                $_SESSION['giohang'][$id] = $qty;
            }
        }
    }

    header("Location: gio-hang.php");
    exit();
}

if ($action == 'delete') {

    $id = $_GET['id'] ?? null;

    if ($id && isset($_SESSION['giohang'][$id])) {
        unset($_SESSION['giohang'][$id]);
    }

    header("Location: gio-hang.php");
    exit();
}

if ($action == 'clear') {

    unset($_SESSION['giohang']);

    header("Location: gio-hang.php");
    exit();
}
?>