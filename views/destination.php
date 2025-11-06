<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm Đến - Group 22</title>
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
        
        .destination-card {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            height: 350px;
            margin-bottom: 30px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .destination-card:hover {
            transform: scale(1.05);
        }
        
        .destination-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .destination-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 30px 20px;
        }
        
        .destination-card h3 {
            color: white;
            margin-bottom: 10px;
        }
        
        .destination-card .badge {
            font-size: 0.9rem;
        }
        
        .featured-destination {
            height: 500px;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <?php include '../views/header.php'?>

    <!-- Page Header -->
    <div class="bg-light py-5">
        <div class="container text-center">
            <h1 class="mb-3">Khám Phá Điểm Đến Tuyệt Vời</h1>
            <p class="lead">Những địa điểm du lịch hấp dẫn nhất Việt Nam</p>
        </div>
    </div>

    <!-- Featured Destination -->
    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-star text-warning"></i> Điểm Đến Nổi Bật</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="destination-card featured-destination">
                    <img src="https://cf.bstatic.com/xdata/images/region/1120x840/60672.webp?k=05e2c58f785269921cadb804afd9ad762aad5ab06e0d7c9c99a5553885290ea3&o=" alt="Hạ Long">
                    <div class="destination-overlay">
                        <h2>Vịnh Hạ Long</h2>
                        <p><i class="fas fa-map-marker-alt"></i> Quảng Ninh</p>
                        <p>Di sản thiên nhiên thế giới với hàng nghìn hòn đảo đá vôi kỳ vĩ</p>
                        <span class="badge bg-success">120+ Homestay</span>
                        <span class="badge bg-info ms-2">Top Destination</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Destinations -->
    <div class="container mb-5">
        <h2 class="mb-4"><i class="fas fa-map-marked-alt"></i> Điểm Đến Phổ Biến</h2>
        <div class="row">
            <!-- Destination 1 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/462329043.webp?k=8b31b6ba7625ad90d27025060c07dcc8d55d990b4f20f8c954d3a9567978be3c&o=" alt="Sapa">
                    <div class="destination-overlay">
                        <h3>Sapa</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Lào Cai</p>
                        <p>Thị trấn miền núi với ruộng bậc thang tuyệt đẹp</p>
                        <span class="badge bg-primary">85+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 2 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/539848990.webp?k=a6852ab4c1883949dfc6fc2c5e288b216b10f26bea2f55c2d9d16beef3135b97&o=" alt="Đà Lạt">
                    <div class="destination-overlay">
                        <h3>Đà Lạt</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Lâm Đồng</p>
                        <p>Thành phố ngàn hoa với khí hậu mát mẻ quanh năm</p>
                        <span class="badge bg-primary">150+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 3 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/748435301.webp?k=0e081a7996dc883d8139f99d0b85c2aa018f5e2225b2d84298dda5ea43bb3cf6&o=" alt="Đà Nẵng">
                    <div class="destination-overlay">
                        <h3>Đà Nẵng</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Đà Nẵng</p>
                        <p>Thành phố đáng sống với bãi biển đẹp nhất Việt Nam</p>
                        <span class="badge bg-primary">200+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 4 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/640621213.webp?k=5425d9828546f808d50259b4750cd0a2eb1d1d2051566a6db08f122669721fab&o=" alt="Hội An">
                    <div class="destination-overlay">
                        <h3>Hội An</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Quảng Nam</p>
                        <p>Phố cổ với kiến trúc độc đáo và đèn lồng rực rỡ</p>
                        <span class="badge bg-primary">95+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 5 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square240/577552502.webp?k=3469f6f67c2d831883d04a6e4c609b66fc70cc593584888f00ed041ef395cef4&o=" alt="Phú Quốc">
                    <div class="destination-overlay">
                        <h3>Phú Quốc</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Kiên Giang</p>
                        <p>Đảo ngọc với bãi biển xanh trong và san hô đẹp</p>
                        <span class="badge bg-primary">110+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 6 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/754200599.webp?k=2202a2f81f45817c18178b12301efb1ebaf61867cee4e6ce4cc442c3098ad832&o=" alt="Nha Trang">
                    <div class="destination-overlay">
                        <h3>Nha Trang</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Khánh Hòa</p>
                        <p>Thành phố biển với nhiều hoạt động du lịch hấp dẫn</p>
                        <span class="badge bg-primary">180+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 7 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/746013908.webp?k=ed32c676502e539e5da2086e77555b9e507eb77706b22ce87113dab264f7323b&o=" alt="Hà Nội">
                    <div class="destination-overlay">
                        <h3>Hà Nội</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Thủ đô</p>
                        <p>Thủ đô nghìn năm văn hiến với nhiều di tích lịch sử</p>
                        <span class="badge bg-primary">250+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 8 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/471824267.webp?k=f250134323593eb31bd9c7b1cc7360e609110c419827f9c08c178f9591fe4245&o=" alt="Cần Thơ">
                    <div class="destination-overlay">
                        <h3>Cần Thơ</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Đồng bằng sông Cửu Long</p>
                        <p>Chợ nổi và văn hóa miệt vườn sông nước đặc trưng</p>
                        <span class="badge bg-primary">75+ Homestay</span>
                    </div>
                </div>
            </div>

            <!-- Destination 9 -->
            <div class="col-md-4">
                <div class="destination-card">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/square600/553300945.webp?k=ed78f9ac06ea28ffb1045057b97952daea0e9de3c86ca2a6b45f5838089f44eb&o=" alt="Ninh Bình">
                    <div class="destination-overlay">
                        <h3>Ninh Bình</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Ninh Bình</p>
                        <p>Vịnh Hạ Long trên cạn với cảnh quan thiên nhiên tuyệt đẹp</p>
                        <span class="badge bg-primary">65+ Homestay</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-primary text-white py-5">
        <div class="container text-center">
            <h2>Sẵn Sàng Khám Phá?</h2>
            <p class="lead">Đặt homestay ngay hôm nay và nhận ưu đãi đặc biệt!</p>
            <a href="homestay.php" class="btn btn-light btn-lg mt-3">
                <i class="fas fa-search"></i> Tìm Homestay Ngay
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
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