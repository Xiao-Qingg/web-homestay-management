<?php
require_once '../functions/auth_functions.php';
checkLogin('../views/login.php');

$functions_path = __DIR__ . '/../functions/booking_functions.php';
if (!file_exists($functions_path)) {
    http_response_code(500);
    echo 'Lỗi: file functions/booking_functions.php không tìm thấy.';
    exit;
}
require_once $functions_path;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
}
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0);
if ($user_id <= 0) {
    header("Location: ../views/login.php");
    exit();
}

// Lấy danh sách booking của user
$bookings = getBookingsByUserId($user_id);

// Kiểm tra biến $logged từ auth_functions
$logged = isset($_SESSION['user_id']) || isset($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt phòng của tôi - Homestay Paradise</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS riêng -->
    <link rel="stylesheet" href="../css/my_booking.css">
    <link rel="stylesheet" href="../css/homestay_detail.css">
    
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <span><i class="fas fa-phone"></i> 0123-456-789</span>
                    <span class="ms-3"><i class="fas fa-envelope"></i> group22@homestay.vn</span>
                </div>
                <div class="col-md-4 text-center">
                    <span class="discount-badge">
                        <i class="fas fa-gift"></i> Giảm giá 30% cho booking đầu tiên!
                    </span>
                </div>
                <div class="col-md-4 text-end">
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <a href="../index.php" class="logo">
                        <i class="fas fa-home"></i> Group 22
                    </a>
                </div>
                <div class="col-md-5">
                    <form action="../index.php" method="GET" id="searchForm">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Tìm kiếm homestay theo tên hoặc địa điểm...">
                            <button class="btn btn-primary-custom" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($logged): ?>
                        <?php $fullname = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Người dùng'; ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-custom dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Hi, <?= htmlspecialchars($fullname) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="../views/profile.php"><i class="fas fa-user-circle"></i> Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="../views/my_bookings.php"><i class="fas fa-calendar"></i> Đặt phòng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../handles/logout_process.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="../views/login.php" class="btn btn-outline-custom btn-custom me-2">Đăng nhập</a>
                        <a href="../views/register.php" class="btn btn-primary-custom btn-custom">Đăng ký</a>
                    <?php endif; ?>
                    <a href="./favorites.php" class="btn btn-link position-relative me-3">
                        <i class="fa-solid fa-heart" style="font-size:20px; color: #dc3545;"></i>
                        <span class="cart-badge">
                            <?php echo isset($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0; ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-home"></i> Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php#homestays"><i class="fas fa-building"></i> Homestay</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-map-marked-alt"></i> Điểm đến</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-percent"></i> Ưu đãi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-newspaper"></i> Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-phone-alt"></i> Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container" style="margin-top: 30px; min-height: 500px;">
        <div class="container mt-5 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-check"></i> Đặt phòng của tôi</h2>
                <a href="../index.php" class="btn btn-outline-primary">
                    <i class="fas fa-plus"></i> Đặt phòng mới
                </a>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>Bạn chưa có đơn đặt phòng nào</h4>
                    <p>Hãy khám phá và đặt homestay yêu thích của bạn ngay!</p>
                    <a href="../index.php" class="btn btn-primary mt-3">
                        <i class="fas fa-search"></i> Tìm homestay
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle"></i> Bạn có <strong><?= count($bookings) ?></strong> đơn đặt phòng
                </div>
                
                <div class="row">
                    <?php foreach ($bookings as $b): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card booking-card shadow-sm position-relative">
                                <!-- Badge trạng thái -->
                                <?php 
                                    $status = $b['status'] ?? 'Đang chờ xử lý';
                                    $statusClass = 'status-pending';
                                    
                                    if (stripos($status, 'xác nhận') !== false || stripos($status, 'confirmed') !== false) {
                                        $statusClass = 'status-confirmed';
                                        $statusText = 'Đã xác nhận';
                                    } elseif (stripos($status, 'hủy') !== false || stripos($status, 'cancel') !== false) {
                                        $statusClass = 'status-cancelled';
                                        $statusText = 'Đã hủy';
                                    }
                                     else {
                                        $statusText = $status;
                                    }
                                ?>
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= htmlspecialchars($statusText) ?>
                                </span>
                                
                              
                                
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-home text-primary"></i> 
                                        <?= htmlspecialchars($b['homestay_name']) ?>
                                    </h5>
                                    
                                    <div class="card-text">
                                        <p class="mb-2">
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                            <small><?= htmlspecialchars($b['location']) ?></small>
                                        </p>
                                        
                                        <hr>
                                        <img class="card-img-top mb-3" style="border-radius: 8px;" 
                                             src="<?= htmlspecialchars($b['image_url']  ) ?>" />
                                             
                                             
                                        
                                        <div class="row text-center mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Nhận phòng</small>
                                                <div><strong><?= date('d/m/Y', strtotime($b['check_in'])) ?></strong></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Trả phòng</small>
                                                <div><strong><?= date('d/m/Y', strtotime($b['check_out'])) ?></strong></div>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-users"></i> Số khách:</span>
                                            <strong><?= (int)$b['num_people'] ?> người</strong>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span><i class="fas fa-money-bill-wave"></i> Tổng tiền:</span>
                                            <strong class="text-danger">
                                                <?= number_format($b['total_price'], 0, ',', '.') ?>đ
                                            </strong>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <?php
                                                if ($statusClass === 'status-pending') {
                                                    echo '<a href="../handles/booking_process.php?action=deleteFromUser&booking_id=' . $b['booking_id'] . '" 
                                                            class="btn btn-warning"
                                                            onclick="return confirm(\'Bạn có chắc muốn hủy đặt phòng này không?\')">
                                                            <i class="fas fa-hourglass-half"></i> Hủy đặt phòng
                                                        </a>';
                                                }else {
                                                    echo '<button class="btn btn-secondary" disabled>
                                                            <i class="fas fa-ban"></i> Không thể hủy
                                                        </button>';
                                                }

                                               
                                            ?> 
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer text-muted text-center">
                                    <small>
                                        <i class="fas fa-clock"></i> 
                                        Đặt ngày <?= date('d/m/Y H:i', strtotime($b['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><i class="fas fa-info-circle"></i> Về Chúng Tôi</h5>
                    <p>Homestay Paradise - Nền tảng đặt phòng homestay hàng đầu Việt Nam.</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><i class="fas fa-link"></i> Liên Kết</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Về chúng tôi</a></li>
                        <li><a href="#" class="text-white-50">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="text-white-50">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><i class="fas fa-concierge-bell"></i> Dịch Vụ</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Đặt phòng homestay</a></li>
                        <li><a href="#" class="text-white-50">Hỗ trợ khách hàng</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5><i class="fas fa-phone-alt"></i> Liên Hệ</h5>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="fas fa-phone"></i> 0123-456-789</li>
                        <li><i class="fas fa-envelope"></i> info@homestay.vn</li>
                    </ul>
                </div>
            </div>
            <div class="text-center pt-3 mt-3 border-top border-secondary">
                <p class="mb-0">&copy; 2025 Homestay Paradise. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>