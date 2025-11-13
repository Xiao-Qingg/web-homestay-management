<?php
$functions_path = __DIR__ . '/functions/homestay_functions.php';
if (!file_exists($functions_path)) {
    http_response_code(500);
    echo 'Lỗi: file functions/homestay_.php không tìm thấy. Vui lòng kiểm tra đường dẫn hoặc tạo file đó.';
    exit;
}
require_once $functions_path;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu đang đăng nhập với vai trò admin thì tự động đăng xuất
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
}

// Lấy tất cả homestays từ database
$homestays = getAllHomestays();

// Chuyển đổi dữ liệu từ database sang format phù hợp
$formatted_homestays = [];
foreach ($homestays as $homestay) {
    $formatted_homestays[] = [
        'id' => $homestay['id'],
        'name' => $homestay['homestay_name'],
        'location' => $homestay['location'],
        'price' => $homestay['price_per_night'],
        'rooms' => $homestay['num_room'],
        'guests' => $homestay['max_people'],
        'image' => $homestay['image_url'],
        'status' => $homestay['status'],
    ];
}

$search_results = $formatted_homestays;
$search_query = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $search_results = array_filter($formatted_homestays, function($homestay) use ($search_query) {
        return stripos($homestay['name'], $search_query) !== false || 
               stripos($homestay['location'], $search_query) !== false;
    });
}

// Xử lý lọc theo giá
if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $max_price = (int)$_GET['max_price'];
    $search_results = array_filter($search_results, function($homestay) use ($max_price) {
        return $homestay['price'] <= $max_price;
    });
}

// Xử lý lọc theo số phòng
if (isset($_GET['rooms']) && !empty($_GET['rooms'])) {
    $rooms = (int)$_GET['rooms'];
    $search_results = array_filter($search_results, function($homestay) use ($rooms) {
        return $homestay['rooms'] >= $rooms;
    });
}

// === PHÂN TRANG ===
$items_per_page = 6; // Số homestay mỗi trang
$total_items = count($search_results);
$total_pages = ceil($total_items / $items_per_page);

// Lấy trang hiện tại từ URL, mặc định là trang 1
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$current_page = min($current_page, max(1, $total_pages)); // Đảm bảo không vượt quá tổng số trang

// Tính offset
$offset = ($current_page - 1) * $items_per_page;

// Lấy homestay cho trang hiện tại
$search_results = array_values($search_results); // Reset keys
$current_page_items = array_slice($search_results, $offset, $items_per_page);

// Hàm tạo URL phân trang giữ nguyên các tham số filter
function getPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return 'index.php?' . http_build_query($params);
}

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homestay Paradise - Điểm Đến Lý Tưởng</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <!-- Thông báo thành công -->
    <?php if (isset($_SESSION['booking_success'])): ?>
         <script>
                alert("<?= htmlspecialchars($_SESSION['booking_success']) ?>");
        </script>
    <?php 
        unset($_SESSION['booking_success']); 
    endif; 
    ?>

    <!-- Thông báo lỗi nếu có -->
    <?php if (isset($_GET['error'])): ?>
    <div class="notification-container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Lỗi!</h5>
            <p class="mb-0"><?= htmlspecialchars($_GET['error']) ?></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Top Bar -->
    <?php include './views/header.php'?>

    <!-- Carousel -->
    <div id="carouselHomestay" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselHomestay" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselHomestay" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselHomestay" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./images/slidebar.png" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption">
                    <h2 class="animate__animated animate__fadeInDown">Trải Nghiệm Kỳ Nghỉ Tuyệt Vời</h2>
                    <p class="animate__animated animate__fadeInUp">Khám phá những homestay đẹp nhất Việt Nam</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./images/slidebar2.png" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption">
                    <h2 class="animate__animated animate__fadeInDown">Không Gian Riêng Tư & Thoải Mái</h2>
                    <p class="animate__animated animate__fadeInUp">Nhà của bạn ở bất kỳ đâu</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./images/slidebar3.png" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption">
                    <h2 class="animate__animated animate__fadeInDown">Giá Tốt Nhất Thị Trường</h2>
                    <p class="animate__animated animate__fadeInUp">Đặt phòng ngay hôm nay để nhận ưu đãi đặc biệt</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselHomestay" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselHomestay" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Advanced Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="search-card">
                <h4 class="mb-4"><i class="fas fa-filter"></i> Tìm Kiếm Nâng Cao</h4>
                <form action="index.php" method="GET" id="advancedSearchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Địa điểm</label>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Nhập tên hoặc địa điểm" 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Giá tối đa (VNĐ)</label>
                            <select class="form-select" name="max_price">
                                <option value="">Tất cả</option>
                                <option value="500000" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] == '500000') ? 'selected' : ''; ?>>Dưới 500.000đ</option>
                                <option value="700000" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] == '700000') ? 'selected' : ''; ?>>Dưới 700.000đ</option>
                                <option value="1000000" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] == '1000000') ? 'selected' : ''; ?>>Dưới 1.000.000đ</option>
                                <option value="1500000" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] == '1500000') ? 'selected' : ''; ?>>Dưới 1.500.000đ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Số phòng</label>
                            <select class="form-select" name="rooms">
                                <option value="">Tất cả</option>
                                <option value="1" <?php echo (isset($_GET['rooms']) && $_GET['rooms'] == '1') ? 'selected' : ''; ?>>1+ phòng</option>
                                <option value="2" <?php echo (isset($_GET['rooms']) && $_GET['rooms'] == '2') ? 'selected' : ''; ?>>2+ phòng</option>
                                <option value="3" <?php echo (isset($_GET['rooms']) && $_GET['rooms'] == '3') ? 'selected' : ''; ?>>3+ phòng</option>
                                <option value="4" <?php echo (isset($_GET['rooms']) && $_GET['rooms'] == '4') ? 'selected' : ''; ?>>4+ phòng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary-custom w-100">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Homestay Listings -->
    <section class="container my-5">
        <?php if ($search_query || isset($_GET['max_price']) || isset($_GET['rooms'])): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <?php if ($search_query): ?>
                Kết quả tìm kiếm cho: "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
            <?php else: ?>
                Kết quả lọc
            <?php endif; ?>
            - Tìm thấy <strong><?php echo $total_items; ?></strong> homestay
            <a href="index.php" class="btn btn-sm btn-outline-secondary float-end">
                <i class="fas fa-times"></i> Xóa bộ lọc
            </a>
        </div>
        <?php endif; ?>

        <h2 class="text-center mb-5">
            <i class="fas fa-star text-warning"></i> Homestay Nổi Bật
        </h2>

        <?php if ($total_items > 0): ?>
        <div class="row" id="homestayContainer">
            <?php foreach ($current_page_items as $homestay): ?>
            <div class="col-lg-4 col-md-6 homestay-item mb-4" data-price="<?php echo $homestay['price']; ?>">
                <div class="card homestay-card h-100">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars($homestay['image']); ?>" 
                             class="card-img-top homestay-image" 
                             alt="<?php echo htmlspecialchars($homestay['name']); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop'">
                        <span class="price-tag"><?php echo number_format($homestay['price']); ?>đ/đêm</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($homestay['name']); ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($homestay['location']); ?>
                        </p>
                        <div class="mb-3">
                            <span class="me-3"><i class="fas fa-bed"></i> <?php echo $homestay['rooms']; ?> phòng</span>
                            <span><i class="fas fa-users"></i> <?php echo $homestay['guests']; ?> khách</span>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="./views/homestay_detail.php?id=<?php echo $homestay['id']; ?>" class="btn btn-primary-custom">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <?php
                                $is_favorite = isset($_SESSION['favorites']) && in_array($homestay['id'], $_SESSION['favorites']);
                            ?>
                            <a href="./handles/add_to_wishlist.php?id=<?php echo $homestay['id']; ?>" 
                               class="btn <?php echo $is_favorite ? 'btn-danger' : 'btn-outline-danger'; ?>">
                                <i class="fa-solid fa-heart"></i> 
                                <?php echo $is_favorite ? 'Đã yêu thích' : 'Thêm vào yêu thích'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Homestay pagination">
            <ul class="pagination justify-content-center mt-4">
                <!-- Nút Previous -->
                <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo ($current_page > 1) ? getPaginationUrl($current_page - 1) : '#'; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php
                // Hiển thị tất cả các trang
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = ($i == $current_page) ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPaginationUrl($i) . '">' . $i . '</a></li>';
                }
                ?>

                <!-- Nút Next -->
                <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo ($current_page < $total_pages) ? getPaginationUrl($current_page + 1) : '#'; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-5">
            <div class="no-results">
                <i class="fas fa-search fa-4x mb-3 text-muted"></i>
                <h3>Không tìm thấy homestay phù hợp</h3>
                <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc điều chỉnh bộ lọc</p>
                <a href="index.php" class="btn btn-primary-custom mt-3">
                    <i class="fas fa-home"></i> Về trang chủ
                </a>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <?php include './views/footer.php' ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>