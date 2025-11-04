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

// include './header.php';
include './menu.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"> -->
    <title>Document</title>
</head>
<body>
<style>
    .stat-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }
    .stat-card {
        flex: 1;
        min-width: 220px;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
</style>

<main class="main-content" style="margin-left: 260px; padding-left: 20px;">
    <div class="header d-flex justify-content-between align-items-center mb-3">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin User') ?></span>
            <a href="../../handles/logout_process.php" class="btn btn-outline-secondary btn-sm ms-2"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="content-card">
        <h2>Tổng quan hệ thống</h2>
        <div class="stat-container">
            <div class="stat-card" style="background:#e3f2fd;">
                <h3 style="color:#1976d2;">Tổng Homestay</h3>
                <p style="font-size:36px;font-weight:bold;color:#1565c0;"><?= $total_homestays ?></p>
            </div>
            <div class="stat-card" style="background:#e8f5e9;">
                <h3 style="color:#388e3c;">Đặt phòng</h3>
                <p style="font-size:36px;font-weight:bold;color:#2e7d32;"><?= $total_bookings?></p>
            </div>
            <div class="stat-card" style="background:#fff3e0;">
                <h3 style="color:#f57c00;">Người dùng</h3>
                <p style="font-size:36px;font-weight:bold;color:#ef6c00;"><?= $total_users?></p>
            </div>
            <div class="stat-card" style="background:#fce4ec;">
                <h3 style="color:#c2185b;">Doanh thu</h3>
                <p style="font-size:36px;font-weight:bold;color:#ad1457;"><?= $total?> đ</p>
            </div>
        </div>

            </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>