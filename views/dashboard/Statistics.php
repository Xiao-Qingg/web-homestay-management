<?php
// session_start();
$current_page = 'statistics';
$page_title = 'Thống kê';

// Load functions
require_once __DIR__ . '/../../handles/homestay_process.php';
require_once __DIR__ . '/../../handles/user_process.php';
require_once __DIR__ . '/../../handles/booking_process.php';

// Lấy dữ liệu
$homestays = handleGetAllHomestays();
$users = handleGetAllUsers();
$bookings = handleGetAllBookings();

// Thống kê chi tiết theo năm
$current_year = date('Y');
$yearly_revenue = [];
$yearly_bookings = [];

for ($month = 1; $month <= 12; $month++) {
    $key = $current_year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
    $yearly_revenue[$key] = 0;
    $yearly_bookings[$key] = 0;
}

foreach ($bookings as $booking) {
    $date = $booking['created_at'] ?? $booking['booking_date'] ?? date('Y-m-d');
    $month_key = date('Y-m', strtotime($date));
    
    if (isset($yearly_revenue[$month_key])) {
        $yearly_revenue[$month_key] += floatval($booking['total_price']);
        $yearly_bookings[$month_key]++;
    }
}

// Top homestays
$homestay_bookings = [];
foreach ($bookings as $booking) {
    $homestay_id = $booking['homestay_id'] ?? 0;
    if (!isset($homestay_bookings[$homestay_id])) {
        $homestay_bookings[$homestay_id] = [
            'count' => 0,
            'revenue' => 0,
            'name' => $booking['homestay_name'] ?? 'Homestay ' . $homestay_id
        ];
    }
    $homestay_bookings[$homestay_id]['count']++;
    $homestay_bookings[$homestay_id]['revenue'] += floatval($booking['total_price']);
}

// Sort top homestays
usort($homestay_bookings, function($a, $b) {
    return $b['revenue'] - $a['revenue'];
});
$top_homestays = array_slice($homestay_bookings, 0, 5);

// Thống kê theo người dùng
$user_bookings = [];
foreach ($bookings as $booking) {
    $user_id = $booking['user_id'] ?? 0;
    if (!isset($user_bookings[$user_id])) {
        $user_bookings[$user_id] = [
            'count' => 0,
            'total' => 0,
            'name' => $booking['user_fullname'] ?? $booking['fullname'] ?? 'User ' . $user_id
        ];
    }
    $user_bookings[$user_id]['count']++;
    $user_bookings[$user_id]['total'] += floatval($booking['total_price']);
}

usort($user_bookings, function($a, $b) {
    return $b['total'] - $a['total'];
});
$top_users = array_slice($user_bookings, 0, 5);

// Thống kê tăng trưởng
$current_month_revenue = 0;
$last_month_revenue = 0;
$current_month_bookings = 0;
$last_month_bookings = 0;

$current_month = date('Y-m');
$last_month = date('Y-m', strtotime('-1 month'));

foreach ($bookings as $booking) {
    $date = $booking['created_at'] ?? $booking['booking_date'] ?? date('Y-m-d');
    $month = date('Y-m', strtotime($date));
    
    if ($month == $current_month) {
        $current_month_revenue += floatval($booking['total_price']);
        $current_month_bookings++;
    } elseif ($month == $last_month) {
        $last_month_revenue += floatval($booking['total_price']);
        $last_month_bookings++;
    }
}

$revenue_growth = $last_month_revenue > 0 ? 
    (($current_month_revenue - $last_month_revenue) / $last_month_revenue) * 100 : 0;
$booking_growth = $last_month_bookings > 0 ? 
    (($current_month_bookings - $last_month_bookings) / $last_month_bookings) * 100 : 0;

include './menu.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/statistic.css">
    <title><?= $page_title ?></title>
    
</head>
<body>

<main class="main-content">
    <div class="header">
        <h1><i class="fas fa-chart-bar"></i> Thống kê</h1>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <select id="yearFilter">
            <option value="<?= $current_year ?>"><?= $current_year ?></option>
            <option value="<?= $current_year - 1 ?>"><?= $current_year - 1 ?></option>
            <option value="<?= $current_year - 2 ?>"><?= $current_year - 2 ?></option>
        </select>
        <select id="periodFilter">
            <option value="month">Theo tháng</option>
            <option value="quarter">Theo quý</option>
            <option value="year">Theo năm</option>
        </select>
        <button onclick="exportReport()">
            <i class="fas fa-download"></i> Xuất báo cáo
        </button>
    </div>

    <!-- Growth Stats -->
    <div class="stats-grid">
        <div class="stat-box blue">
            <h3><i class="fas fa-chart-line"></i> Tăng trưởng doanh thu</h3>
            <div class="value"><?= number_format(abs($revenue_growth), 1) ?>%</div>
            <div class="change <?= $revenue_growth >= 0 ? 'positive' : 'negative' ?>">
                <i class="fas fa-arrow-<?= $revenue_growth >= 0 ? 'up' : 'down' ?>"></i>
                So với tháng trước
            </div>
        </div>

        <div class="stat-box green">
            <h3><i class="fas fa-calendar-check"></i> Tăng trưởng booking</h3>
            <div class="value"><?= number_format(abs($booking_growth), 1) ?>%</div>
            <div class="change <?= $booking_growth >= 0 ? 'positive' : 'negative' ?>">
                <i class="fas fa-arrow-<?= $booking_growth >= 0 ? 'up' : 'down' ?>"></i>
                So với tháng trước
            </div>
        </div>

        <div class="stat-box orange">
            <h3><i class="fas fa-money-bill-wave"></i> Doanh thu tháng này</h3>
            <div class="value"><?= number_format($current_month_revenue) ?> đ</div>
            <div class="change positive">
                <i class="fas fa-calendar"></i>
                Tháng <?= date('m/Y') ?>
            </div>
        </div>

        <div class="stat-box purple">
            <h3><i class="fas fa-receipt"></i> Booking tháng này</h3>
            <div class="value"><?= $current_month_bookings ?></div>
            <div class="change positive">
                <i class="fas fa-calendar"></i>
                Tháng <?= date('m/Y') ?>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-row">
        <div class="chart-card">
            <h3><i class="fas fa-chart-area"></i> Biểu đồ doanh thu năm <?= $current_year ?></h3>
            <div class="chart-wrapper">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-chart-pie"></i> Phân bố booking</h3>
            <div class="chart-wrapper">
                <canvas id="bookingDistribution"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Rankings -->
    <div class="tables-row">
        <div class="table-card">
            <h3><i class="fas fa-trophy"></i> Top 5 Homestay doanh thu cao nhất</h3>
            <div class="rank-table">
                <?php foreach ($top_homestays as $index => $hs): ?>
                    <div class="rank-item">
                        <div class="rank-number <?= $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'other')) ?>">
                            <?= $index + 1 ?>
                        </div>
                        <div class="rank-info">
                            <div class="rank-name"><?= htmlspecialchars($hs['name']) ?></div>
                            <div class="rank-detail"><?= $hs['count'] ?> booking</div>
                        </div>
                        <div class="rank-value"><?= number_format($hs['revenue']) ?> đ</div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($top_homestays)): ?>
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                        Chưa có dữ liệu
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-card">
            <h3><i class="fas fa-users"></i> Top 5 Khách hàng thân thiết</h3>
            <div class="rank-table">
                <?php foreach ($top_users as $index => $user): ?>
                    <div class="rank-item">
                        <div class="rank-number <?= $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'other')) ?>">
                            <?= $index + 1 ?>
                        </div>
                        <div class="rank-info">
                            <div class="rank-name"><?= htmlspecialchars($user['name']) ?></div>
                            <div class="rank-detail"><?= $user['count'] ?> booking</div>
                        </div>
                        <div class="rank-value"><?= number_format($user['total']) ?> đ</div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($top_users)): ?>
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                        Chưa có dữ liệu
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode(array_values($yearly_revenue)) ?>,
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' đ';
                        }
                    }
                }
            }
        }
    });

    // Booking Distribution Chart
    const distributionCtx = document.getElementById('bookingDistribution').getContext('2d');
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($top_homestays, 'name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($top_homestays, 'count')) ?>,
                backgroundColor: [
                    '#667eea',
                    '#43e97b',
                    '#fa709a',
                    '#4facfe',
                    '#ffd93d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    function exportReport() {
        alert('Chức năng xuất báo cáo đang được phát triển!');
        // Thêm code xuất PDF/Excel ở đây
    }
</script>

</body>
</html>