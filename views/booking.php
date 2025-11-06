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
require_once '../functions/user_functions.php';

// Kiểm tra role admin
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

// ===== THAY ĐỔI TỪ ĐÂY =====
// Lấy thông tin từ POST thay vì GET
$user_id = (int)($_POST['user_id'] ?? 0);
$homestay_id = $_POST['homestay_id'] ?? $_GET['id'] ?? 0;

$checkin = $_POST['checkin'] ?? date('Y-m-d');
$checkout = $_POST['checkout'] ?? date('Y-m-d', strtotime('+1 days'));
$guests = (int)($_POST['guests'] ?? 1);
$price_per_night = (float)($_POST['price_per_night'] ?? 0);
$nights = (int)($_POST['nights'] ?? 1);
$total = (float)($_POST['total'] ?? 0);

// Lấy thông tin homestay
$homestay = getHomestayById($homestay_id);
if (!$homestay) {
    header('Location: ../index.php');
    exit();
}

// Kiểm tra đăng nhập
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;
if ($user_id <= 0) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin user từ database
$user_info = getUserById($user_id);
if (!$user_info) {
    $user_fullname = $_SESSION['fullname'] ?? $_SESSION['username'] ?? '';
    $user_phone = $_SESSION['phone'] ?? '';
    $user_address = $_SESSION['address'] ?? '';
} else {
    $user_fullname = $user_info['fullname'] ?? '';
    $user_phone = $user_info['phone'] ?? '';
    $user_address = $user_info['address'] ?? '';
}

// Tính lại để đảm bảo chính xác (phòng trường hợp JS bị sửa)
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$nights_calculated = $date1->diff($date2)->days;
if ($nights_calculated <= 0) $nights_calculated = 1;

$total = $homestay['price_per_night'] * $nights_calculated;


// Dùng giá trị tính lại
$nights = $nights_calculated;
$price_per_night = $homestay['price_per_night'];
$subtotal = $total;


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt phòng - <?= htmlspecialchars($homestay['homestay_name']) ?></title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/web-homestay-management/assets/css/booking.css">
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
                
                <form action="/web-homestay-management/handles/booking_process.php?action=create" method="POST" id="bookingForm">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <input type="hidden" id="homestay_id" name="homestay_id" value="<?php echo $homestay_id; ?>">


                    <input type="hidden" name="price_per_night" value="<?= $price_per_night ?>">    
                    <input type="hidden" name="nights" value="<?= $nights ?>">
                    <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                    <input type="hidden" name="total" value="<?= $total ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" 
                                   value="<?= htmlspecialchars($user_fullname) ?>" required>
                        </div>
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
                                   value="<?= $guests ?>" min="1" max="<?= $homestay['max_people'] ?? 10 ?>" required>
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
                    <!-- <div class="payment-methods">
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
                    </div> -->

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách hủy phòng</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-booking "  >
                        <i class="fas fa-check-circle"></i> Xác nhận đặt phòng
                    </button>
                    <a href="homestay_detail.php?id=<?= $homestay_id ?>" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
               
            </div>

            <!-- GIỮ NGUYÊN PHẦN SUMMARY BÊN PHẢI -->
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
                        <!-- <strong id="subtotalDisplay"><?= number_format($total) ?>đ</strong> -->
                    </div>
                    
                    
                    <div class="total-row">
                        <span>Tổng cộng</span>
                        <strong id="total"><?= number_format($total) ?>đ</strong>
                    </div>
                </div>
                 </form>

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

        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        
    </script>
    <script src="../assets/js/booking.js"></script>
</body>
</html>