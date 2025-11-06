<?php
require_once '../functions/auth_functions.php';
checkLogin('../views/login.php');

$functions_path = __DIR__ . '/../functions/homestay_functions.php';
if (!file_exists($functions_path)) {
    http_response_code(500);
    echo 'Lỗi: file functions/homestay_functions.php không tìm thấy.';
    exit;
}
require_once $functions_path;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra role admin
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
}


// Lấy ID homestay từ URL
$homestay_id = (int)($_GET['id'] ?? 0);
$homestay = null;
$images = getHomestayImages($homestay_id);
$rooms = getRoomsByHomestayId($homestay_id);
if ($homestay_id > 0) {
    $homestay = getHomestayById($homestay_id);
}

// Nếu không tìm thấy homestay, redirect về trang chủ
if (!$homestay) {
    header("Location: ../index.php");
    exit();
}

$logged = isset($_SESSION['id']) || isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($homestay['homestay_name']) ?> - Homestay Paradise</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS riêng cho detail -->
   <link rel="stylesheet" href="/web-homestay-management/assets/css/homestay_detail.css">
</head>
<body>
    <style>

        .section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-family: Arial, sans-serif;
        }

        .section th, .section td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        .section th {
            background-color: #f5f5f5;
            color: #333;
        }

        .section tr:nth-child(even) {
            background-color: #fafafa;
        }

        .section img {
            border-radius: 8px;
            transition: transform 0.2s ease;
        }

        .section img:hover {
            transform: scale(1.05);
        }

    </style>
    <!-- Top Bar giống Index -->
    <?php include '../views/header.php'?>

    <!-- Main Container -->
    <div class="container" style="margin-top: 30px;">
        <!-- Image Gallery -->
        <div class="gallery">
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($homestay['image_url']) ?>" alt="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                
            </div>
            <?php foreach ($images as $img): ?>
                <div class="gallery-item">
                    <img src="<?= htmlspecialchars($img['room_image_url']) ?>" alt="Ảnh homestay">
                </div>
            <?php endforeach; ?>
            <!-- <div class="gallery-item">
                <img src="<?= htmlspecialchars($homestay['room_image_url']) ?>" alt="Phòng ngủ">
            </div>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($homestay['room_image_url']) ?>" alt="Nhà bếp">
            </div>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($homestay['room_image_url']) ?>" alt="View">
            </div> -->
        </div>  

        <!-- Content Layout -->
        <div class="content-layout">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Title Section -->
                <div class="title-section">
                    <h1><?= htmlspecialchars($homestay['homestay_name']) ?></h1>
                    
                    <div class="location">
                        <i class="fa-solid fa-location-dot" style="font-size:22px; margin-right:5px;"></i>
                        <?= htmlspecialchars($homestay['location']) ?>
                    </div>
                    <div class="rating">
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        
                        </div>
                        <span class="reviews">5.0 (2 đánh giá)</span>
                    </div>
                </div>
                

                <!-- Room Info -->
                

                <div class="section">
                    <table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse: collapse; text-align: center;">
                        <thead style="background-color: #f0f0f0;">
                            <tr>
                                <th>Tên phòng</th>
                                <!-- <th>Giá / đêm (VNĐ)</th> -->
                                <th>Sức chứa</th>
                                <th>Tiện nghi</th>
                                <th>Mô tả</th>
                                <!-- <th>Đặt phòng</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                                    <!-- <td><?= number_format($room['price']) ?></td> -->
                                    <td><?= htmlspecialchars($room['capacity']) ?> người</td>
                                    <td><?= htmlspecialchars($room['amenity_name']) ?></td>
                                    <td><?= htmlspecialchars($room['description']) ?></td>
                                     <!-- <td>
                                        <form class="booking-room-form" method="GET" action="../views/booking.php">
                                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                            <input type="hidden" name="price" value="<?= $room[''] ?>">
                                            <input type="hidden" name="checkin" id="checkin_<?= $room['id'] ?>" value="<?= date('Y-m-d') ?>">
                                            <input type="hidden" name="checkout" id="checkout_<?= $room['id'] ?>" value="<?= date('Y-m-d', strtotime('+1 days')) ?>">
                                            <input type="hidden" name="guest_count" value="1">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-calendar-check"></i> Đặt phòng
                                            </button>
                                        </form>
                                    </td> -->
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Amenities -->
                <div class="section">
                    <h2>Tiện nghi</h2>
                    <div class="amenities-grid">
                        <?php
                        $amenities_list = !empty($homestay['amenities']) ? explode(',', $homestay['amenities']) : ['WiFi miễn phí', 'Bếp đầy đủ', 'View đẹp', 'Chỗ đậu xe'];
                        $amenity_icons = ['<i class="fa-solid fa-wifi"></i>', '<i class="fa-solid fa-kitchen-set"></i>', '<i class="fa-solid fa-sun-plant-wilt"></i>', '<i class="fa-solid fa-square-parking"></i>', '🔥', '📺', '🧺', '🏡'];
                        
                        foreach ($amenities_list as $index => $amenity):
                            $icon = $amenity_icons[$index % count($amenity_icons)];
                        ?>
                        <div class="amenity-item">
                            <span class="amenity-icon"><?= $icon ?></span>
                            <span><?= htmlspecialchars(trim($amenity)) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Host Info -->
                <div class="section">
                    <h2>Chủ nhà</h2>
                    <div class="host-card">
                        <div class="host-avatar" style="background-color:blue !important;">NH</div>
                        <div class="host-info">
                            <h3>Nguyễn Hoàng</h3>
                            <p>Chủ nhà · Tham gia từ 2023</p>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="section">
                    <h2>Đánh giá từ khách (2)</h2>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="reviewer-name">Trần Minh Anh</span>
                            <span class="review-date">Tháng 10, 2025</span>
                        </div>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <p style="margin-top: 10px; line-height: 1.6;">
                            Chỗ ở tuyệt vời! View đẹp không tì vết, nhà cửa sạch sẽ và chủ nhà rất thân thiện. Chúng tôi đã có kỳ nghỉ tuyệt vời tại đây. Rất đáng để quay lại!
                        </p>
                    </div>
                    <div class="review-card">
                        <div class="review-header">
                            <span class="reviewer-name">Lê Văn Bình</span>
                            <span class="review-date">Tháng 9, 2025</span>
                        </div>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <p style="margin-top: 10px; line-height: 1.6;">
                            Không gian yên tĩnh, thoáng mát. Phù hợp cho gia đình. Bếp đầy đủ tiện nghi, chúng tôi tự nấu ăn rất tiện. Sẽ giới thiệu cho bạn bè!
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Booking Card -->
            <div class="sidebar">
                <form action="./booking.php" method="POST" id="bookingForm">
                    <div class="booking-card">
                        <div class="price-section">
                            <div class="price"><?= number_format($homestay['price_per_night']) ?>đ</div>
                            <div class="price-label">mỗi đêm</div>
                        </div>

                        <div class="date-picker">
                            <div>
                                <div class="date-label">NHẬN PHÒNG</div>
                                <input type="date" 
                                    class="date-input" 
                                    id="checkin" 
                                    name="checkin"
                                    value="<?= date('Y-m-d') ?>"
                                    min="<?= date('Y-m-d') ?>"
                                    required>
                            </div>
                            <div>
                                <div class="date-label">TRẢ PHÒNG</div>
                                <input type="date" 
                                    class="date-input" 
                                    id="checkout" 
                                    name="checkout"
                                    value="<?= date('Y-m-d', strtotime('+1 days')) ?>"
                                    min="<?= date('Y-m-d', strtotime('+1 days')) ?>"
                                    required>
                            </div>
                        </div>

                        <div class="guest-selector">
                            <div class="guest-row">
                                <span>Số khách</span>
                                <div class="guest-controls">
                                    <button type="button" class="guest-btn" onclick="changeGuests(-1)">−</button>
                                    <span class="guest-count" id="guestCount">1</span>
                                    <button type="button" class="guest-btn" onclick="changeGuests(1)">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs để gửi dữ liệu -->
                        <input type="hidden" name="homestay_id" value="<?= htmlspecialchars($homestay['id']) ?>">
                        <input type="hidden" name="homestay_name" value="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                        <input type="hidden" name="price_per_night" value="<?= $homestay['price_per_night'] ?>">
                        <input type="hidden" name="guests" id="guestsInput" value="1">
                        <input type="hidden" name="nights" id="nightsInput" value="1">
                        <input type="hidden" name="subtotal" id="subtotalInput" value="<?= $homestay['price_per_night'] ?>">
                        <input type="hidden" name="service_fee" id="serviceFeeInput" value="<?= $homestay['price_per_night'] * 0.1 ?>">
                        <input type="hidden" name="total" id="totalInput" value="<?= $homestay['price_per_night'] * 1.1 ?>">

                        <button type="submit" class="btn-booking w-100">
                            <i class="fas fa-shopping-cart fa-lg"></i> Đặt ngay
                        </button>

                        <div style="text-align: center; margin-top: 15px; color: #666; font-size: 14px;">
                            Bạn sẽ chưa bị tính phí
                        </div>

                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span id="priceCalc"><?= number_format($homestay['price_per_night']) ?>đ x 1 đêm</span>
                                <!-- <span id="subtotal"><?= number_format($homestay['price_per_night']) ?>đ</span> -->
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                <span>Tổng cộng</span>
                                <span id="subtotal"><?= number_format($homestay['price_per_night']) ?>đ</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
   <?php
        include '../views/footer.php'
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
   <script src="../assets/js/homestay_detail.js">
    let guestCount = 1;
    const pricePerNight = <?= $homestay['price_per_night'] ?>;
    const maxGuests = <?= $homestay['max_guests'] ?? 10 ?>;

    // Thay đổi số khách
    function changeGuests(delta) {
        const newCount = guestCount + delta;
        if (newCount >= 1 && newCount <= maxGuests) {
            guestCount = newCount;
            document.getElementById('guestCount').textContent = guestCount;
            document.getElementById('guestsInput').value = guestCount;
        }
    }
   </script>   
</body>
</html>