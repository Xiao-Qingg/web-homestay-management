<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin Tức - Group 22</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a7bb7;
            --secondary-color: #e74c3c;
        }
        
        .top-bar {
            background: var(--primary-color);
            color: white;
            padding: 10px 0;
        }
        
        .promo-banner {
            background: var(--secondary-color);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
        }
        
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-link {
            color: #333;
            font-weight: 500;
            margin: 0 10px;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        .nav-link.active {
            color: var(--primary-color);
            background: #e8f1f8;
            border-radius: 5px;
        }
        
        .news-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 30px;
            height: 100%;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .news-card img {
            height: 200px;
            object-fit: cover;
        }
        
        .featured-news {
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 40px;
            position: relative;
        }
        
        .featured-news img {
            height: 450px;
            object-fit: cover;
            width: 100%;
        }
        
        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            color: white;
            padding: 40px;
        }
        
        .category-badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .news-meta {
            color: #666;
            font-size: 0.9rem;
        }
        
        .sidebar {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <?php include '../views/header.php'?>

    <!-- Page Header -->
    <div class="bg-light py-5">
        <div class="container text-center">
            <h1 class="mb-3"><i class="fas fa-newspaper"></i> Tin Tức Du Lịch</h1>
            <p class="lead">Cập nhật thông tin và xu hướng du lịch mới nhất</p>
        </div>
    </div>

    <!-- Featured News -->
    <div class="container my-5">
        <div class="featured-news">
            <img src="https://cf.bstatic.com/xdata/images/hotel/square600/745167357.webp?k=d077be8b66e36035fb0f064401ea3a7d69e7770a6da9ebd8b2033a4914a7764e&o=" alt="Featured">
            <div class="featured-overlay">
                <span class="category-badge">TIN NỔI BẬT</span>
                <h2>10 Homestay Đẹp Nhất Việt Nam Năm 2024</h2>
                <p class="news-meta">
                    <i class="far fa-calendar"></i> 15/11/2024 | 
                    <i class="far fa-user"></i> Admin | 
                    <i class="far fa-eye"></i> 2,500 lượt xem
                </p>
                <p>Khám phá những homestay có kiến trúc độc đáo, view đẹp và dịch vụ tốt nhất được du khách yêu thích nhất trong năm nay.</p>
                <a href="#" class="btn btn-light">Đọc thêm <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <h3 class="mb-4">Tin Tức Mới Nhất</h3>
                
                <!-- News 1 -->
                <div class="card news-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="https://cf.bstatic.com/xdata/images/hotel/square600/543373351.webp?k=d8d309e2954dec8e253f0305591391588cfdc4b77ec8c249c295a2669405b261&o=" class="img-fluid" alt="News">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <span class="category-badge">DU LỊCH</span>
                                <h5 class="card-title">Mùa Lúa Chín Sapa - Thời Điểm Lý Tưởng Để Đi Du Lịch</h5>
                                <p class="news-meta">
                                    <i class="far fa-calendar"></i> 14/11/2024 | 
                                    <i class="far fa-user"></i> Nguyễn Văn A
                                </p>
                                <p class="card-text">Tháng 9-10 là thời điểm đẹp nhất để ngắm ruộng bậc thang lúa chín vàng óng tại Sapa. Cùng khám phá những homestay view đẹp nhất...</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- News 2 -->
                <!-- <div class="card news-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/300x200/FF6347/FFFFFF?text=Travel+Tips" class="img-fluid" alt="News">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <span class="category-badge">TIPS</span>
                                <h5 class="card-title">7 Bí Quyết Đặt Homestay Giá Rẻ Mà Vẫn Chất Lượng</h5>
                                <p class="news-meta">
                                    <i class="far fa-calendar"></i> 13/11/2024 | 
                                    <i class="far fa-user"></i> Trần Thị B
                                </p>
                                <p class="card-text">Chia sẻ kinh nghiệm săn homestay giá tốt từ những người đi du lịch nhiều. Những mẹo nhỏ giúp bạn tiết kiệm chi phí đáng kể...</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div> -->


                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Trước</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="sidebar mb-4">
                    <h5 class="mb-3">Tìm Kiếm</h5>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm tin tức...">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Categories -->
                <div class="sidebar mb-4">
                    <h5 class="mb-3">Chủ Đề</h5>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            Du lịch <span class="badge bg-primary float-end">25</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Điểm đến <span class="badge bg-primary float-end">18</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Tips & Tricks <span class="badge bg-primary float-end">32</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Khuyến mãi <span class="badge bg-primary float-end">15</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Xu hướng <span class="badge bg-primary float-end">12</span>
                        </a>
                    </div>
                </div>

                <!-- Popular Posts -->
                <div class="sidebar mb-4">
                    <h5 class="mb-3">Bài Viết Phổ Biến</h5>
                    <div class="mb-3">
                        <h6><a href="#" class="text-decoration-none">Top 5 Homestay Có View Biển Đẹp Nhất</a></h6>
                        <small class="text-muted"><i class="far fa-calendar"></i> 08/11/2024</small>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6><a href="#" class="text-decoration-none">Kinh Nghiệm Du Lịch Đà Lạt Tự Túc</a></h6>
                        <small class="text-muted"><i class="far fa-calendar"></i> 06/11/2024</small>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6><a href="#" class="text-decoration-none">Lý Do Nên Chọn Homestay Thay Vì Khách Sạn</a></h6>
                        <small class="text-muted"><i class="far fa-calendar"></i> 05/11/2024</small>
                    </div>
                </div>

                <!-- Tags -->
                <div class="sidebar">
                    <h5 class="mb-3">Tags</h5>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Sapa</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Đà Lạt</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Đà Nẵng</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Hội An</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Phú Quốc</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Du lịch</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Homestay</a>
                    <a href="#" class="badge bg-secondary me-1 mb-2">Tips</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-home"></i> Group 22</h5>
                    <p>Hệ thống homestay uy tín tại Việt Nam</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-tiktok fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube fa-2x"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Liên kết</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Trang chủ</a></li>
                        <li><a href="homestay.php" class="text-white">Homestay</a></li>
                        <li><a href="diem-den.php" class="text-white">Điểm đến</a></li>
                        <li><a href="lien-he.php" class="text-white">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p><i class="fas fa-phone"></i> 0123-456-789</p>
                    <p><i class="fas fa-envelope"></i> group22@homestay.vn</p>
                    <p><i class="fas fa-map-marker-alt"></i> Hà Nội, Việt Nam</p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p>&copy; 2024 Group 22. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>