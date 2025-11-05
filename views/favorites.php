<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Vui lòng đăng nhập để xem danh sách yêu thích"));
    exit();
}

require_once __DIR__ . '/../functions/homestay_functions.php';

// Lấy danh sách ID yêu thích từ session
$favorite_ids = $_SESSION['favorites'] ?? [];
$favorites = [];

// Lấy thông tin chi tiết các homestay yêu thích
if (!empty($favorite_ids)) {
    foreach ($favorite_ids as $id) {
        $homestay = getHomestayById($id);
        if ($homestay) {
            $favorites[] = [
                'id' => $homestay['id'],
                'name' => $homestay['homestay_name'],
                'location' => $homestay['location'],
                'price' => $homestay['price_per_night'],
                'rooms' => $homestay['num_room'],
                'guests' => $homestay['max_people'],
                'image' => $homestay['image_url'],
                'status' => $homestay['status']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Yêu Thích - Homestay Paradise</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-heart text-danger"></i> Danh Sách Yêu Thích Của Tôi</h2>
                <p class="text-muted">Bạn có <?php echo count($favorites); ?> homestay trong danh sách yêu thích</p>
            </div>
            <div class="col text-end">
                 <a href="/web-homestay-management/index.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại trang chủ
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['favorite_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['favorite_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['favorite_message']); endif; ?>

        <?php if (empty($favorites)): ?>
        <div class="text-center py-5">
            <i class="fas fa-heart-broken fa-4x text-muted mb-3"></i>
            <h3>Chưa có homestay yêu thích</h3>
            <p class="text-muted">Hãy khám phá và thêm những homestay bạn thích vào danh sách!</p>
            <a href="/web-homestay-management/index.php" class="btn btn-primary mt-3">
                <i class="fas fa-search"></i> Khám phá homestay
            </a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($favorites as $homestay): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card homestay-card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="position-relative overflow-hidden">
                        <img src="<?php echo htmlspecialchars($homestay['image']); ?>" 
                             class="card-img-top homestay-image" 
                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;"
                             alt="<?php echo htmlspecialchars($homestay['name']); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop'">
                        
                        <!-- Overlay gradient -->
                        <div class="position-absolute bottom-0 start-0 w-100 h-50" 
                             style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);"></div>
                        
                        <!-- Price tag -->
                        <div class="position-absolute bottom-0 start-0 m-3">
                            <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill shadow-lg">
                                <i class="fas fa-tag"></i> <?php echo number_format($homestay['price']); ?>đ/đêm
                            </span>
                        </div>
                        
                        <!-- Remove button -->
                        <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 rounded-circle shadow-sm" 
                                style="width: 40px; height: 40px; padding: 0; transition: all 0.3s ease;"
                                onclick="if(confirm('Bạn có chắc muốn xóa khỏi danh sách yêu thích?')) window.location.href='/web-homestay-management/handles/add_to_wishlist.php?id=<?php echo $homestay['id']; ?>'"
                                onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='white'; this.style.transform='rotate(90deg)'"
                                onmouseout="this.style.backgroundColor='white'; this.style.color='#212529'; this.style.transform='rotate(0deg)'">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <!-- Favorite badge -->
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-white text-danger shadow-sm">
                                <i class="fas fa-heart"></i> Yêu thích
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <h5 class="card-title  fw-bold" style="color: #2c3e50; min-height: 30px;">
                            <?php echo htmlspecialchars($homestay['name']); ?>
                        </h5>
                        
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i> 
                            <?php echo htmlspecialchars($homestay['location']); ?>
                        </p>
                        
                        <div class="d-flex justify-content-start gap-4 mb-3 pb-3 border-bottom">
                            <div class="text-center">
                                <i class="fas fa-bed text-primary fs-5"></i>
                                <div class="small text-muted mt-1"><?php echo $homestay['rooms']; ?> phòng</div>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-users text-success fs-5"></i>
                                <div class="small text-muted mt-1"><?php echo $homestay['guests']; ?> khách</div>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-star text-warning fs-5"></i>
                                <div class="small text-muted mt-1">4.8/5</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="homestay_detail.php?id=<?php echo $homestay['id']; ?>" 
                               class="btn btn-primary btn-lg shadow-sm"
                               style="background: linear-gradient(135deg, #425dd5ff 0%, #285adaff 100%); border: none; transition: transform 0.2s ease;"
                               onmouseover="this.style.transform='translateY(-2px)'"
                               onmouseout="this.style.transform='translateY(0)'">
                                <i class="fas fa-eye me-2"></i> Xem chi tiết
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>