<?php
// Sử dụng __DIR__ để tính toán đường dẫn chính xác từ vị trí file hiện tại
require_once __DIR__ . '/../../functions/auth_functions.php';
checkLogin('../../views/login.php');

$current_page = $current_page ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Homestay Paradise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
     <aside class="sidebar">
    <div class="sidebar-header">
        <h2>Homestay Paradise</h2>
        <small>Admin Panel</small>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= ($current_page === 'dashboard') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-square-poll-vertical"></i></span> Dashboard
            </a>
        </li>
        <li>
            <a href="./homestay.php" class="<?= ($current_page === 'homestays') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-house-chimney"></i></span> Quản lý Homestay
            </a>
        </li>
        <li>
            <a href="booking.php" class="<?= ($current_page === 'bookings') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-calendar-days"></i></span> Đặt phòng
            </a>
        </li>
        <li>
            <a href="user.php" class="<?= ($current_page === 'users') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-user"></i></span> Người dùng
            </a>
        </li>
        <li>
            <a href="statistic.php" class="<?= ($current_page === 'statistics') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-chart-line"></i></span> Thống kê
            </a>
        </li>
        <li>
            <a href="setting.php" class="<?= ($current_page === 'settings') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-gear"></i></span> Cài đặt
            </a>
        </li>
    </ul>
</aside>
  
</body>
</html>