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
   
   <style>
        /* ===== IMPROVED STYLES ===== */
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin-top: 30px;
            margin-bottom: 50px;
        }

        /* Gallery Styles */
        .gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 40px;
            height: 500px;
        }

        .gallery-item:first-child {
            grid-column: 1 / 3;
            grid-row: 1 / 3;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Content Layout */
        .content-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 80px;
            align-items: start;
        }

        .main-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        /* Title Section */
        .title-section {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }

        .title-section h1 {
            font-size: 32px;
            font-weight: 600;
            color: #222;
            margin-bottom: 15px;
        }

        .location {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .location i {
            color: #FF385C;
            margin-right: 8px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stars {
            color: #FFB400;
            font-size: 14px;
        }

        .reviews {
            color: #666;
            font-size: 14px;
        }

        /* Section Styles */
        .section {
            padding: 30px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #222;
        }

        /* Room Table */
        .room-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .room-table thead {
            background: linear-gradient(135deg, #667eea 0%, #3a89ceff 100%);
            color: white;
        }

        .room-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .room-table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #444;
            vertical-align: top;
        }

        .room-table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .room-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Amenities Grid */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .amenity-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .amenity-icon {
            font-size: 24px;
            color: #667eea;
        }

        /* Host Card */
        .host-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .host-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #2f6ae7ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
        }

        .host-info h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #222;
        }

        .host-info p {
            color: #666;
            margin: 0;
        }

        /* Review Card */
        .review-card {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .reviewer-name {
            font-weight: 600;
            color: #222;
        }

        .review-date {
            color: #999;
            font-size: 14px;
        }

        /* Booking Card */
        .sidebar {
            position: sticky;
            top: 100px;
        }

        .booking-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            border: 1px solid #e0e0e0;
        }

        .price-section {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 25px;
        }

        .price {
            font-size: 28px;
            font-weight: 700;
            color: #222;
        }

        .price-label {
            color: #666;
            font-size: 16px;
        }

        .date-picker {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .date-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .date-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .date-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Guest Selector - FIXED */
        .guest-selector {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .guest-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .guest-row span {
            font-weight: 500;
            color: #222;
        }

        .guest-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .guest-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .guest-btn:hover:not(:disabled) {
            border-color: #667eea;
            color: #667eea;
            transform: scale(1.1);
        }

        .guest-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .guest-count {
            font-size: 16px;
            font-weight: 600;
            color: #222;
            min-width: 30px;
            text-align: center;
        }

        /* Button Booking */
        .btn-booking {
            background: linear-gradient(135deg, #667eea 0%, #2a7cd8ff 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-booking:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-booking i {
            margin-right: 8px;
        }

        /* Price Breakdown */
        .price-breakdown {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #666;
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            color: #222;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .content-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .sidebar {
                position: relative;
                top: 0;
            }

            .gallery {
                height: 400px;
            }
        }

        @media (max-width: 768px) {
            .gallery {
                grid-template-columns: 1fr;
                height: auto;
            }

            .gallery-item:first-child {
                grid-column: 1;
                grid-row: 1;
                height: 300px;
            }

            .gallery-item {
                height: 200px;
            }

            .main-content {
                padding: 20px;
            }

            .booking-card {
                padding: 20px;
            }

            .room-table {
                font-size: 13px;
            }

            .room-table th,
            .room-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <?php include '../views/header.php'?>

    <!-- Main Container -->
    <div class="container">
        <!-- Image Gallery -->
        <div class="gallery">
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($homestay['image_url']) ?>" alt="<?= htmlspecialchars($homestay['homestay_name']) ?>">
            </div>
            <?php foreach (array_slice($images, 0, 4) as $img): ?>
                <div class="gallery-item">
                    <img src="<?= htmlspecialchars($img['room_image_url']) ?>" alt="Ảnh homestay">
                </div>
            <?php endforeach; ?>
        </div>  

        <!-- Content Layout -->
        <div class="content-layout">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Title Section -->
                <div class="title-section">
                    <h1><?= htmlspecialchars($homestay['homestay_name']) ?></h1>
                    
                    <div class="location">
                        <i class="fa-solid fa-location-dot"></i>
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
                    <h2><i class="fas fa-bed"></i> Phòng có sẵn</h2>
                    <table class="room-table">
                        <thead>
                            <tr>
                                <th>Tên phòng</th>
                                <th>Sức chứa</th>
                                <th>Tiện nghi</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($room['room_name']) ?></strong></td>
                                    <td><i class="fas fa-user"></i> <?= htmlspecialchars($room['capacity']) ?> người</td>
                                    <td><?= htmlspecialchars($room['amenity_name']) ?></td>
                                    <td><?= htmlspecialchars($room['description']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Amenities -->
                <div class="section">
                    <h2><i class="fas fa-star"></i> Tiện nghi</h2>
                    <div class="amenities-grid">
                        <?php
                        $amenities_list = !empty($homestay['amenities']) ? explode(',', $homestay['amenities']) : ['WiFi miễn phí', 'Bếp đầy đủ', 'View đẹp', 'Chỗ đậu xe'];
                        $amenity_icons = [
                            '<i class="fa-solid fa-wifi"></i>', 
                            '<i class="fa-solid fa-kitchen-set"></i>', 
                            '<i class="fa-solid fa-mountain-sun"></i>', 
                            '<i class="fa-solid fa-square-parking"></i>',
                            '<i class="fa-solid fa-snowflake"></i>',
                            '<i class="fa-solid fa-tv"></i>',
                            '<i class="fa-solid fa-shirt"></i>',
                            '<i class="fa-solid fa-house"></i>'
                        ];
                        
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
                    <h2><i class="fas fa-user-circle"></i> Chủ nhà</h2>
                    <div class="host-card">
                        <div class="host-avatar">NH</div>
                        <div class="host-info">
                            <h3>Nguyễn Hoàng</h3>
                            <p>Chủ nhà · Tham gia từ 2023</p>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="section">
                    <h2><i class="fas fa-comments"></i> Đánh giá từ khách (2)</h2>
                    
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
                        <p style="margin-top: 10px; line-height: 1.6; color: #444;">
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
                        <p style="margin-top: 10px; line-height: 1.6; color: #444;">
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
                            <div class="price-label">/ đêm</div>
                        </div>

                        <div class="date-picker">
                            <div>
                                <div class="date-label">Nhận phòng</div>
                                <input type="date" 
                                    class="date-input" 
                                    id="checkin" 
                                    name="checkin"
                                    value="<?= date('Y-m-d') ?>"
                                    min="<?= date('Y-m-d') ?>"
                                    required>
                            </div>
                            <div>
                                <div class="date-label">Trả phòng</div>
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
                                    <button type="button" class="guest-btn" id="decreaseGuests">−</button>
                                    <span class="guest-count" id="guestCount">1</span>
                                    <button type="button" class="guest-btn" id="increaseGuests">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs -->
                        <input type="hidden" name="homestay_id" value="<?= htmlspecialchars($homestay['id']) ?>">
                        <input type="hidden" name="homestay_name" value="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                        <input type="hidden" name="price_per_night" value="<?= $homestay['price_per_night'] ?>">
                        <input type="hidden" name="guests" id="guestsInput" value="1">
                        <input type="hidden" name="nights" id="nightsInput" value="1">
                        <input type="hidden" name="subtotal" id="subtotalInput" value="<?= $homestay['price_per_night'] ?>">
                        <input type="hidden" name="total" id="totalInput" value="<?= $homestay['price_per_night'] ?>">

                        <button type="submit" class="btn-booking">
                            <i class="fas fa-calendar-check"></i> Đặt ngay
                        </button>

                        <div style="text-align: center; margin-top: 15px; color: #999; font-size: 14px;">
                            Bạn sẽ chưa bị tính phí
                        </div>

                        <div class="price-breakdown">
                            <div class="price-row">
                                <span id="priceCalc"><?= number_format($homestay['price_per_night']) ?>đ x 1 đêm</span>
                                <span id="subtotalDisplay"><?= number_format($homestay['price_per_night']) ?>đ</span>
                            </div>
                            
                            <div class="price-total">
                                <span>Tổng cộng</span>
                                <span id="totalDisplay"><?= number_format($homestay['price_per_night']) ?>đ</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../views/footer.php'?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // FIXED GUEST COUNTER SCRIPT
        let guestCount = 1;
        const pricePerNight = <?= $homestay['price_per_night'] ?>;
        const maxGuests = <?= $homestay['max_guests'] ?? 10 ?>;

        const decreaseBtn = document.getElementById('decreaseGuests');
        const increaseBtn = document.getElementById('increaseGuests');
        const guestCountDisplay = document.getElementById('guestCount');
        const guestsInput = document.getElementById('guestsInput');

        // Decrease guests
        decreaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (guestCount > 1) {
                guestCount--;
                updateGuestDisplay();
            }
        });

        // Increase guests
        increaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (guestCount < maxGuests) {
                guestCount++;
                updateGuestDisplay();
            }
        });

        function updateGuestDisplay() {
            guestCountDisplay.textContent = guestCount;
            guestsInput.value = guestCount;
            
            // Disable/enable buttons
            decreaseBtn.disabled = guestCount <= 1;
            increaseBtn.disabled = guestCount >= maxGuests;
        }

        // Calculate nights
        function calculateNights() {
            const checkinDate = new Date(document.getElementById('checkin').value);
            const checkoutDate = new Date(document.getElementById('checkout').value);
            const timeDiff = checkoutDate - checkinDate;
            const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            return nights > 0 ? nights : 1;
        }

        // Update price
        function updatePrice() {
            const nights = calculateNights();
            const subtotal = pricePerNight * nights;
            const total = subtotal;

            // Update display
            document.getElementById('priceCalc').textContent = 
                `${pricePerNight.toLocaleString('vi-VN')}đ x ${nights} đêm`;
            document.getElementById('subtotalDisplay').textContent = 
                `${subtotal.toLocaleString('vi-VN')}đ`;
            document.getElementById('totalDisplay').textContent = 
                `${total.toLocaleString('vi-VN')}đ`;

            // Update hidden inputs
            document.getElementById('nightsInput').value = nights;
            document.getElementById('subtotalInput').value = subtotal;
            document.getElementById('totalInput').value = total;
        }

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const checkin = new Date(document.getElementById('checkin').value);
            const checkout = new Date(document.getElementById('checkout').value);
            
            if (checkout <= checkin) {
                e.preventDefault();
                alert('Ngày trả phòng phải sau ngày nhận phòng!');
                return false;
            }
            
            const nights = calculateNights();
            if (nights < 1) {
                e.preventDefault();
                alert('Vui lòng chọn ngày hợp lệ!');
                return false;
            }
        });

        // Update price on date change
        document.getElementById('checkin').addEventListener('change', function() {
            const checkin = new Date(this.value);
            const checkout = document.getElementById('checkout');
            const minCheckout = new Date(checkin);
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkout.min = minCheckout.toISOString().split('T')[0];
            
            if (new Date(checkout.value) <= checkin) {
                checkout.value = minCheckout.toISOString().split('T')[0];
            }
            updatePrice();
        });

        document.getElementById('checkout').addEventListener('change', updatePrice);

        // Initialize
        updateGuestDisplay();
        updatePrice();
    </script>
</body>
</html>