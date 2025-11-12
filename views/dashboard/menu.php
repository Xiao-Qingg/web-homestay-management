<?php
// Sử dụng __DIR__ để tính toán đường dẫn chính xác từ vị trí file hiện tại
require_once __DIR__ . '/../../functions/auth_functions.php';
checkLogin('../../views/login.php');

$current_page = $current_page ?? 'dashboard';

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Homestay Paradise</title>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/menu.css">
</head>
<body>
     <aside class="sidebar">
    <div class="sidebar-header">
        <h2>Group 22</h2>
        <small>Admin Panel</small>
    </div>
    <ul style="padding-left:0 !important;" class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= ($current_page === 'dashboard') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-square-poll-vertical"></i></span> Dashboard
            </a>
        </li>
        <li>
            <a href="homestay.php" class="menu-item <?= ($current_page == 'homestays') ? 'active' : '' ?>">
                <span><i class="fa-solid fa-house"></i></span> Quản lý Homestay
            </a>
        </li>
         <li>
            <a href="user.php" class="<?= ($current_page === 'users') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-user"></i></span> Quản lý người dùng
            </a>
        </li>
        <li>
            <a href="booking.php" class="<?= ($current_page === 'bookings') ? 'active' : ''; ?>">
                <span><i class="fa-solid fa-calendar-days"></i></span> Quản lý Booking
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
