<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['nguoidung'])) {
    header("Location: dang-nhap.php");
    exit;
}

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    header("Location: trang-chu.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM donhang WHERE MaDonHang = ? AND idUser = ?");
$stmt->execute([$order_id, $_SESSION['nguoidung']['idUser']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    header("Location: trang-chu.php");
    exit;
}

$total = (int)$order['TongTien']; // Đã bao gồm phí ship

$endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
$partnerCode = 'MOMO';
$accessKey = 'F8BBA842ECF85';
$secretKey = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';

$fakeOrderId = 'TEST_' . uniqid() . '_' . time();
$requestId = $fakeOrderId . '_' . rand(10000, 99999);
$amount = (string)$total;
$orderInfo = "Thanh toan don hang #" . $order_id;
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . "/shoptrangsuc/public";
$redirectUrl = $baseUrl . "/dat-hang-thanh-cong.php?id=" . $order_id;
$ipnUrl = $baseUrl . "/xu-ly-thanh-toan.php";
$extraData = "";
$requestType = "captureWallet";

$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $fakeOrderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);

$data = [
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $fakeOrderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$result = curl_exec($ch);
curl_close($ch);

$jsonResult = json_decode($result, true);

if (isset($jsonResult['payUrl'])) {
    $_SESSION['momo_fake_order_' . $fakeOrderId] = $order_id;
    header('Location: ' . $jsonResult['payUrl']);
    exit;
} else {
    $_SESSION['error'] = "Không thể kết nối MoMo: " . ($jsonResult['message'] ?? 'Lỗi không xác định');
    header("Location: tai-khoan.php");
    exit;
}
?>