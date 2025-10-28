<?php
session_start();
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

if ($homestay_id > 0) {
    $homestay = getHomestayById($homestay_id);
}

// Nếu không tìm thấy homestay, redirect về trang chủ
if (!$homestay) {
    header("Location: ../index.php");
    exit();}



// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require_once '../functions/homestay_functions.php';
require_once '../functions/user_functions.php';

// Lấy ID user từ session
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;

// Lấy thông tin user từ database
$user_info = getUserById($user_id);
if (!$user_info) {
    // Fallback nếu không tìm thấy trong DB
    $user_fullname = $_SESSION['fullname'] ?? $_SESSION['username'] ?? '';
    $user_email = $_SESSION['email'] ?? '';
    $user_phone = $_SESSION['phone'] ?? '';
    $user_address = $_SESSION['address'] ?? '';
} else {
    $user_fullname = $user_info['fullname'] ?? '';
    $user_email = $user_info['email'] ?? '';
    $user_phone = $user_info['phone'] ?? '';
    $user_address = $user_info['address'] ?? '';
}

// Lấy thông tin từ URL
$homestay_id = (int)($_GET['id'] ?? 0);
$checkin = $_GET['checkin'] ?? date('Y-m-d');
$checkout = $_GET['checkout'] ?? date('Y-m-d', strtotime('+1 days'));
$guests = (int)($_GET['guests'] ?? 2);



// Lấy thông tin homestay
$homestay = getHomestayById($homestay_id);
if (!$homestay) {
    header('Location: ../index.php');
    exit();
}

// Tính số đêm
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$nights = $date1->diff($date2)->days;
if ($nights <= 0) $nights = 1;

// Tính giá
$price_per_night = $homestay['price_per_night'];
$subtotal = $price_per_night * $nights;
$service_fee = $subtotal * 0.1;
$cleaning_fee = 100000;
$total = $subtotal + $service_fee + $cleaning_fee;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt phòng - <?= htmlspecialchars($homestay['homestay_name']) ?></title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .booking-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .content-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            margin-bottom: 25px;
            color: #2c3e50;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        
        .form-control, .form-select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .homestay-preview {
            display: flex;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .homestay-preview img {
            width: 100px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .homestay-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .homestay-info p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .booking-details {
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .price-breakdown {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }
        
        .btn-booking {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-booking:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .payment-methods {
            display: grid;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .payment-option input[type="radio"] {
            margin-right: 15px;
            width: 20px;
            height: 20px;
        }
        
        .payment-option.selected {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        @media (max-width: 768px) {
            .content-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> Đặt phòng</h1>
            <p>Hoàn tất thông tin để xác nhận đặt phòng của bạn</p>
        </div>

        <div class="content-layout">
            <!-- Form bên trái -->
            <div class="card">
                <h2><i class="fas fa-edit"></i> Thông tin đặt phòng</h2>
                
                <form action="../handles/booking_process.php" method="POST" id="bookingForm">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <input type="hidden" name="homestay_id" value="<?= $homestay_id ?>">
                    <input type="hidden" name="price_per_night" value="<?= $price_per_night ?>">
                    <input type="hidden" name="nights" value="<?= $nights ?>">
                    <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                    <input type="hidden" name="service_fee" value="<?= $service_fee ?>">
                    <input type="hidden" name="cleaning_fee" value="<?= $cleaning_fee ?>">
                    <input type="hidden" name="total" value="<?= $total ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" 
                                   value="<?= htmlspecialchars($user_fullname) ?>" required>
                        </div>
                        <!-- <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= htmlspecialchars($user_email) ?>" required>
                        </div> -->
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?= htmlspecialchars($user_phone) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="address" 
                                   value="<?= htmlspecialchars($user_address) ?>" 
                                   placeholder="Nhập địa chỉ của bạn">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số lượng khách</label>
                        <input type="number" class="form-control" name="guests" 
                           value="<?= $guests ?>" min="1" max="<?= $homestay['max_people'] ?>" required>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Ngày nhận phòng</label>
                            <input type="date" class="form-control" name="checkin" 
                                   value="<?= $checkin ?>" id="checkinInput" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày trả phòng</label>
                            <input type="date" class="form-control" name="checkout" 
                                   value="<?= $checkout ?>" id="checkoutInput" required>
                        </div>
                    </div>

                    
                    <label class="form-label">Yêu cầu đặc biệt</label>
                    <textarea class="form-control" name="special_request" rows="4" 
                              placeholder="VD: Cần giường phụ, đón sân bay..."></textarea>

                    <h2 class="mt-4"><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
                    
                    <div class="payment-methods">
                        <label class="payment-option selected">
                            <input type="radio" name="payment_method" value="bank_transfer" checked>
                            <div>
                                <strong>🏦 Chuyển khoản ngân hàng</strong>
                                <small class="d-block text-muted">Thanh toán qua chuyển khoản</small>
                            </div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="momo">
                            <div>
                                <strong>📱 Ví MoMo</strong>
                                <small class="d-block text-muted">Thanh toán qua ví điện tử</small>
                            </div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash">
                            <div>
                                <strong>💵 Tiền mặt</strong>
                                <small class="d-block text-muted">Thanh toán khi nhận phòng</small>
                            </div>
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách hủy phòng</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-booking">
                        <i class="fas fa-check-circle"></i> Xác nhận đặt phòng
                    </button>
                    <a href="homestay_detail.php?id=<?= $homestay_id ?>" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </form>
            </div>

            <!-- Summary bên phải -->
            <div class="card">
                <h2><i class="fas fa-receipt"></i> Chi tiết đơn hàng</h2>
                
                <div class="homestay-preview">
                    <img src="<?= htmlspecialchars($homestay['image_url']) ?>" alt="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                    <div class="homestay-info">
                        <h3><?= htmlspecialchars($homestay['homestay_name']) ?></h3>
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($homestay['location']) ?></p>
                        <p><i class="fas fa-star text-warning"></i> 4.9 (127 đánh giá)</p>
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-row">
                        <span>Nhận phòng</span>
                        <strong><?= date('d/m/Y', strtotime($checkin)) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Trả phòng</span>
                        <strong><?= date('d/m/Y', strtotime($checkout)) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Số đêm</span>
                        <strong id="nightsDisplay"><?= $nights ?> đêm</strong>
                    </div>
                    <div class="detail-row">
                        <span>Số khách</span>
                        <strong><?= $guests ?> người</strong>
                    </div>
                </div>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span><?= number_format($price_per_night) ?>đ x <span id="nightsCalc"><?= $nights ?></span> đêm</span>
                        <strong id="subtotalDisplay"><?= number_format($subtotal) ?>đ</strong>
                    </div>
                    <div class="price-row">
                        <span>Phí dịch vụ</span>
                        <strong id="serviceFeeDisplay"><?= number_format($service_fee) ?>đ</strong>
                    </div>
                    <div class="price-row">
                        <span>Phí vệ sinh</span>
                        <strong><?= number_format($cleaning_fee) ?>đ</strong>
                    </div>
                    <div class="total-row">
                        <span>Tổng cộng</span>
                        <strong id="totalDisplay"><?= number_format($total) ?>đ</strong>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Chính sách hủy phòng:</strong><br>
                    <small>• Miễn phí hủy trước 7 ngày<br>
                    • Hoàn 50% nếu hủy trước 3 ngày<br>
                    • Không hoàn tiền trong vòng 3 ngày</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const pricePerNight = <?= $price_per_night ?>;
        const cleaningFee = <?= $cleaning_fee ?>;

        // Payment option selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Tính lại giá khi đổi ngày
        function recalculate() {
            const checkin = new Date(document.getElementById('checkinInput').value);
            const checkout = new Date(document.getElementById('checkoutInput').value);
            const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
            
            if (nights > 0) {
                const subtotal = pricePerNight * nights;
                const serviceFee = subtotal * 0.1;
                const total = subtotal + serviceFee + cleaningFee;
                
                document.getElementById('nightsDisplay').textContent = `${nights} đêm`;
                document.getElementById('nightsCalc').textContent = nights;
                document.getElementById('subtotalDisplay').textContent = `${subtotal.toLocaleString()}đ`;
                document.getElementById('serviceFeeDisplay').textContent = `${serviceFee.toLocaleString()}đ`;
                document.getElementById('totalDisplay').textContent = `${total.toLocaleString()}đ`;
                
                // Update hidden field
                document.querySelector('input[name="nights"]').value = nights;
                document.querySelector('input[name="total"]').value = total;
            }
        }

        document.getElementById('checkinInput').addEventListener('change', recalculate);
        document.getElementById('checkoutInput').addEventListener('change', recalculate);

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!document.getElementById('terms').checked) {
                e.preventDefault();
                alert('Vui lòng đồng ý với điều khoản dịch vụ!');
                return false;
            }
        });
    </script>
</body>
</html>