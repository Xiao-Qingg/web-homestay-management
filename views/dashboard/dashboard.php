<?php
// session_start();
$current_page = 'dashboard';
$page_title = 'Dashboard';

// Load functions
require_once __DIR__ . '/../../handles/homestay_process.php';
require_once __DIR__ . '/../../handles/user_process.php';
require_once __DIR__ . '/../../handles/booking_process.php';

// Lấy số liệu thống kê
$homestays = handleGetAllHomestays();
$total_homestays = count($homestays);

$users = handleGetAllUsers();
$total_users = count($users);

$bookings = handleGetAllBookings();
$total_bookings = count($bookings);

$total = 0;
foreach ($bookings as $booking) {
    $total += $booking['total_price'];
}

// Thống kê theo tháng (6 tháng gần nhất)
$monthly_revenue = [];
$monthly_bookings = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthly_revenue[$month] = 0;
    $monthly_bookings[$month] = 0;
}

foreach ($bookings as $booking) {
    $booking_month = date('Y-m', strtotime($booking['created_at']));
    if (isset($monthly_revenue[$booking_month])) {
        $monthly_revenue[$booking_month] += $booking['total_price'];
        $monthly_bookings[$booking_month]++;
    }
}

// Thống kê trạng thái đặt phòng
// Thống kê trạng thái đặt phòng
$booking_status = ['pending' => 0, 'confirmed' => 0, 'cancelled' => 0, 'completed' => 0];
foreach ($bookings as $booking) {
    $status = isset($booking['status']) ? strtolower($booking['status']) : 'pending';
    
    // Xử lý các trường hợp status khác nhau
    if ($status == 'pending' || $status == 'cho_xu_ly' || $status == '0') {
        $booking_status['pending']++;
    } elseif ($status == 'confirmed' || $status == 'da_xac_nhan' || $status == '1') {
        $booking_status['confirmed']++;
    } elseif ($status == 'completed' || $status == 'hoan_thanh' || $status == '2') {
        $booking_status['completed']++;
    } elseif ($status == 'cancelled' || $status == 'da_huy' || $status == '3') {
        $booking_status['cancelled']++;
    } else {
        $booking_status['pending']++;
    }
}
// Tính tỷ lệ lấp đầy (giả sử mỗi homestay có 30 ngày/tháng)
$occupancy_rate = $total_homestays > 0 ? round(($total_bookings / ($total_homestays * 30)) * 100, 1) : 0;
// Lấy booking gần nhất
$recent_bookings = array_slice(array_reverse($bookings), 0, 5);

// include './header.php';
include './menu.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?= $page_title ?></title>
    <style>
       .stat-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            padding: 25px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .stat-card.blue::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.green::before { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card.orange::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .stat-card.pink::before { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.purple::before { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

        .stat-icon {
            font-size: 36px;
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .stat-card.blue .stat-icon { color: #667eea; }
        .stat-card.green .stat-icon { color: #43e97b; }
        .stat-card.orange .stat-icon { color: #fa709a; }
        .stat-card.pink .stat-icon { color: #f5576c; }
        .stat-card.purple .stat-icon { color: #4facfe; }

        .stat-card h3 {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-card .trend {
            font-size: 13px;
            color: #27ae60;
            font-weight: 500;
        }

        .stat-card .trend.down {
            color: #e74c3c;
        }

        /* Chart Cards */
        .chart-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .chart-card h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        /* Recent Activity Table */
        .activity-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .activity-card h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
        }

        .activity-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activity-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        .activity-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .chart-container {
                grid-template-columns: 1fr;
            }

            .stat-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<main class="main-content">
    <div class="header">
        <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
        
    </div>

    <!-- Stats Cards -->
    <div class="stat-container">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-home"></i></div>
            <h3>Tổng Homestay</h3>
            <div class="value"><?= $total_homestays ?></div>
            <div class="trend"><i class="fas fa-arrow-up"></i> Đang hoạt động</div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <h3>Đặt phòng</h3>
            <div class="value"><?= $total_bookings ?></div>
            <div class="trend"><i class="fas fa-arrow-up"></i> + Đã có nhiều hơn so với tháng trước</div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <h3>Người dùng</h3>
            <div class="value"><?= $total_users ?></div>
            <div class="trend"><i class="fas fa-arrow-up"></i> + Thêm người dùng mới</div>
        </div>

        <div class="stat-card pink">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <h3>Doanh thu</h3>
            <div class="value"><?= number_format($total) ?> đ</div>
            <div class="trend"><i class="fas fa-arrow-up"></i> + Thêm doanh thu so với tháng trước</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="chart-container">
        <div class="chart-card">
            <h3><i class="fas fa-chart-line"></i> Doanh thu 6 tháng gần nhất</h3>
            <div class="chart-wrapper">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-chart-pie"></i> Trạng thái đặt phòng</h3>
            <div class="chart-wrapper">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="activity-card">
        <h3><i class="fas fa-history"></i> Đặt phòng gần đây</h3>
        <table class="activity-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Homestay</th>
                    <th>Ngày đặt</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_bookings)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                            Chưa có đặt phòng nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td>#<?= $booking['booking_id'] ?></td>
                            <td><?= htmlspecialchars($booking['user_fullname'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($booking['homestay_name'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y', strtotime($booking['created_at'])) ?></td>
                            <td><?= number_format($booking['total_price']) ?> đ</td>
                            <td>
                                <span class="status-badge <?= $booking['status'] ?>">
                                    <?php
                                    $status_text = [
                                        'pending' => 'Chờ xử lý',
                                        'confirmed' => 'Đã xác nhận',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    echo $status_text[$booking['status']] ?? $booking['status'];
                                    ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(function($date) {
                return date('m/Y', strtotime($date . '-01'));
            }, array_keys($monthly_revenue))) ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode(array_values($monthly_revenue)) ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7
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

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đang chờ xử lý', 'Đã xác nhận', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: [
                    <?= $booking_status['pending'] ?>,
                    <?= $booking_status['confirmed'] ?>,
                    <?= $booking_status['completed'] ?>,
                    <?= $booking_status['cancelled'] ?>
                ],
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#28a745',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#fff'
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
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>