<style>
                :root {
        --primary-color: #4b78bc;
        --secondary-color: #295370;
        --accent-color: #ff6b6b;
        }
                .top-bar {
        background: linear-gradient(
            135deg,
            var(--primary-color) 0%,
            var(--secondary-color) 100%
        );
        color: white;
        padding: 10px 0;
        font-size: 14px;
        }

        .discount-badge {
        background: var(--accent-color);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        animation: pulse 2s infinite;
        }

        @keyframes pulse {
        0%,
        100% {
            transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
                }

        .social-links a {
        color: white;
        font-size: 18px;
        margin: 0 8px;
        transition: transform 0.3s;
        }

        .social-links a:hover {
        transform: translateY(-3px);
        }

        /* Header */
        header {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 15px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        }

        .logo {
        font-size: 28px;
        font-weight: bold;
        color: var(--primary-color);
        text-decoration: none;
        }

        .logo:hover {
        color: var(--secondary-color);
        }

        .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--accent-color);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        }

        .btn-custom {
        border-radius: 25px;
        padding: 8px 25px;
        font-weight: 500;
        transition: all 0.3s;
        }

        .btn-outline-custom {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        }

        .btn-outline-custom:hover {
        background: var(--primary-color);
        color: white;
        }

        .btn-primary-custom {
        background: var(--primary-color);
        border: none;
        color: white;
        }

        .btn-primary-custom:hover {
        background: var(--secondary-color);
        }

        /* Navigation */
        .navbar {
        background: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
        }

        .navbar-nav .nav-link {
        color: #333;
        font-weight: 500;
        padding: 15px 20px;
        transition: color 0.3s;
        position: relative;
        }

        .navbar-nav .nav-link:hover {
        color: var(--primary-color);
        }

        .navbar-nav .nav-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: width 0.3s;
        }

        .navbar-nav .nav-link:hover::after {
        width: 100%;
        }
</style>
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
                        <a href="https://www.facebook.com/hong.trankhac.98"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.tiktok.com/@tkh021005"><i class="fab fa-tiktok"></i></a>
                        <a href="https://www.instagram.com/khac_hog02/"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/@tkhgaming6363"><i class="fab fa-youtube"></i></a>
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
                    <a href="/web-homestay-management/index.php" class="logo" style="font-size:26px ">
                        <i class="fas fa-home"></i> Group 22 
                    </a>
                </div>
                <div class="col-md-5">
                    <form action="index.php" method="GET" id="searchForm">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Tìm kiếm homestay theo tên hoặc địa điểm..." 
                                   value="">
                            <button class="btn btn-primary-custom" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
                
                <?php
                if (session_status() === PHP_SESSION_NONE) session_start();
                $logged = isset($_SESSION['id']) || isset($_SESSION['user_id']);
                ?>
                <div class="col-md-4 text-end">
                    <?php if ($logged): ?>
                        <?php $fullname = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Người dùng'; ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-custom dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Hi, <?= htmlspecialchars($fullname) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/web-homestay-management/views/profile.php"><i class="fas fa-user-circle"></i> Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="/web-homestay-management/views/my_bookings.php"><i class="fas fa-calendar"></i> Đặt phòng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="./handles/logout_process.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                        <a href="/web-homestay-management/views/favorites.php" class="btn btn-link position-relative me-3">
                        <i class="fa-solid fa-heart" style="font-size:20px; color: #dc3545;"></i>
                        <span class="cart-badge">
                            <?php echo isset($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0; ?>
                        </span>
                    </a>
                    <?php else: ?>
                        <a href="views/login.php" class="btn btn-outline-custom btn-custom me-2">Đăng nhập</a>
                        <a href="views/register.php" class="btn btn-primary-custom btn-custom">Đăng ký</a>
                    <?php endif; ?>

                   
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
                        <a class="nav-link" href="/web-homestay-management/index.php"><i class="fas fa-home"></i> Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-building"></i> Homestay</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/web-homestay-management/views/destination.php"><i class="fas fa-map-marked-alt"></i> Điểm đến</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=""><i class="fas fa-percent"></i> Ưu đãi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/web-homestay-management/views/news.php"><i class="fas fa-newspaper"></i> Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-phone-alt"></i> Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>