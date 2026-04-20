<?php
session_start();
if (!isset($_SESSION['nguoidung']) || $_SESSION['nguoidung']['VaiTro'] !== 'admin') {
    header('Location: ../public/dang-nhap.php');
    exit;
}
require_once '../config.php';

$bodyClass = 'admin-dashboard-page';
$current_page = 'tong-quan.php';

$stmt = $conn->query("SELECT SUM(TongTien) as total FROM donhang WHERE MaTrangThai IN (4,5)");
$totalRevenue = $stmt->fetch()['total'] ?? 0;

$stmt = $conn->query("SELECT COUNT(*) as total FROM donhang");
$totalOrders = $stmt->fetch()['total'] ?? 0;

$stmt = $conn->query("SELECT COUNT(*) as total FROM sanpham WHERE DelAt = 0");
$totalProducts = $stmt->fetch()['total'] ?? 0;

$stmt = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE VaiTro = 'khachhang' AND DelAt = 0");
$totalCustomers = $stmt->fetch()['total'] ?? 0;

$stmt = $conn->query("SELECT COUNT(*) as total FROM danhgia");
$totalReviews = $stmt->fetch()['total'] ?? 0;

$stmt = $conn->query("
    SELECT tt.TenTrangThai, COUNT(dh.MaDonHang) as count
    FROM trangthaidonhang tt
    LEFT JOIN donhang dh ON tt.MaTrangThai = dh.MaTrangThai
    GROUP BY tt.MaTrangThai
");
$orderStatusCounts = $stmt->fetchAll();

$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthName = date('m/Y', strtotime("-$i months"));
    $stmt = $conn->prepare("SELECT SUM(TongTien) as total FROM donhang WHERE MaTrangThai IN (4,5) AND DATE_FORMAT(NgayDatHang, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $revenue = $stmt->fetch()['total'] ?? 0;
    $monthlyRevenue[] = ['month' => $monthName, 'revenue' => (float)$revenue];
}

$stmt = $conn->query("
    SELECT dh.MaDonHang, dh.NgayDatHang, dh.TongTien, dh.MaTrangThai, nd.HoTen, tt.TenTrangThai
    FROM donhang dh
    LEFT JOIN nguoidung nd ON dh.idUser = nd.idUser
    LEFT JOIN trangthaidonhang tt ON dh.MaTrangThai = tt.MaTrangThai
    ORDER BY dh.NgayDatHang DESC LIMIT 5
");
$recentOrders = $stmt->fetchAll();

include "../layout/header_admin.php";
?>
<div class="content">
    <div class="container-fluid">
        <div class="admin-header-section">
            <div class="title-area">
                <h1><i class="fa-solid fa-chart-line"></i> TỔNG QUAN</h1>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="stat-box">
                <i class="fa-solid fa-coins"></i>
                <div class="stat-number"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</div>
                <div class="stat-label">Doanh thu</div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-truck"></i>
                <div class="stat-number"><?= $totalOrders ?></div>
                <div class="stat-label">Đơn hàng</div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-box"></i>
                <div class="stat-number"><?= $totalProducts ?></div>
                <div class="stat-label">Sản phẩm</div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-users"></i>
                <div class="stat-number"><?= $totalCustomers ?></div>
                <div class="stat-label">Khách hàng</div>
            </div>
            <div class="stat-box">
                <i class="fa-regular fa-star"></i>
                <div class="stat-number"><?= $totalReviews ?></div>
                <div class="stat-label">Đánh giá</div>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-card">
                <h5><i class="fa-solid fa-chart-column"></i> Doanh thu theo tháng</h5>
                <canvas id="revenueChart" style="width:100%; max-height: 300px;"></canvas>
            </div>
            <div class="chart-card">
                <h5><i class="fa-solid fa-chart-pie"></i> Trạng thái đơn hàng</h5>
                <canvas id="statusChart" style="width:100%; max-height: 300px;"></canvas>
            </div>
        </div>

        <div class="recent-orders-card">
            <h5><i class="fa-solid fa-clock-rotate-left"></i> Đơn hàng gần đây</h5>
            <div class="table-responsive">
                <table class="recent-orders-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="6" class="text-center">Chưa có đơn hàng nào.<?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($order['MaDonHang']) ?></strong></td>
                                <td><?= htmlspecialchars($order['HoTen'] ?? 'Khách vãng lai') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['NgayDatHang'])) ?></td>
                                <td><?= number_format($order['TongTien'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php
                                    $status = $order['MaTrangThai'] ?? 0;
                                    $statusText = $order['TenTrangThai'] ?? 'Đang xử lý';
                                    $statusClass = match($status) {
                                        4, 5 => 'success',
                                        6 => 'danger',
                                        default => 'warning'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars($statusText) ?></span>
                                </td>
                                <td>
                                    <a href="chi-tiet-don-hang.php?id=<?= $order['MaDonHang'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = <?= json_encode($monthlyRevenue) ?>;
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(item => item.month),
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: revenueData.map(item => item.revenue),
            borderColor: '#212737',
            backgroundColor: 'rgba(33, 39, 55, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let value = context.raw;
                        return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                    }
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                    }
                }
            }
        }
    }
});

const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = <?= json_encode($orderStatusCounts) ?>;
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.TenTrangThai),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: ['#212737', '#e4d4d6', '#c2a15b', '#a57f3e', '#64748b', '#dc2626'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include "../layout/footer_admin.php"; ?>