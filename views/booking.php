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

// Lấy thông tin từ POST
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

// Lấy thông tin user
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

// Tính lại giá
$date1 = new DateTime($checkin);
$date2 = new DateTime($checkout);
$nights_calculated = $date1->diff($date2)->days;
if ($nights_calculated <= 0) $nights_calculated = 1;

$nights = $nights_calculated;
$price_per_night = $homestay['price_per_night'];
$subtotal = $price_per_night * $nights;
$total = $subtotal;
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

        <!-- Hiển thị thông báo lỗi/thành công -->
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Lỗi!</strong> <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="content-layout">
            <!-- Form bên trái -->
            <div class="card">
                <h2><i class="fas fa-edit"></i> Thông tin đặt phòng</h2>
                
                <form action="/web-homestay-management/handles/booking_process.php" method="POST" id="bookingForm">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <input type="hidden" name="homestay_id" value="<?= $homestay_id ?>">
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
                                   value="<?= htmlspecialchars($user_phone) ?>" 
                                   pattern="[0-9]{10,11}" required>
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
                            <label class="form-label">Số lượng khách <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="guests" 
                                   value="<?= $guests ?>" min="1" max="<?= $homestay['max_people'] ?? 10 ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Ngày nhận phòng <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="checkin" 
                                   value="<?= $checkin ?>" id="checkinInput" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày trả phòng <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="checkout" 
                                   value="<?= $checkout ?>" id="checkoutInput" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                    </div>

                    <!-- PHẦN THANH TOÁN -->
                    <div class="payment-section">
                        <h2><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
                        
                        <div class="payment-methods">
                            <!-- Tiền mặt -->
                            <label class="payment-option selected" data-payment="cash">
                                <input type="radio" name="payment" value="Tiền mặt" checked>
                                <div class="payment-content">
                                    <div class="payment-title">
                                        <span class="payment-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                                        <strong>Tiền mặt</strong>
                                        <span class="payment-badge popular">PHỔ BIẾN</span>
                                    </div>
                                    <p class="payment-description">Thanh toán bằng tiền mặt khi nhận phòng</p>
                                </div>
                            </label>
                            
                            <!-- Chuyển khoản ngân hàng -->
                            <label class="payment-option" data-payment="bank_transfer">
                                <input type="radio" name="payment" value="Chuyển khoản ngân hàng">
                                <div class="payment-content">   
                                    <div class="payment-title">
                                        <span class="payment-icon"><i class="fa-solid fa-building-columns"></i></span>
                                        <strong>Chuyển khoản ngân hàng</strong>
                                        <span class="payment-badge">NHANH</span>
                                    </div>
                                    <p class="payment-description">Chuyển khoản qua Vietcombank, Techcombank, VPBank...</p>
                                </div>
                            </label>
                            
                            <!-- Thẻ tín dụng/ghi nợ -->
                            <label class="payment-option" data-payment="credit_card">
                                <input type="radio" name="payment" value="Thẻ tín dụng">
                                <div class="payment-content">
                                    <div class="payment-title">
                                        <span class="payment-icon">💳</span>
                                        <strong>Thẻ tín dụng / Ghi nợ</strong>
                                        <span class="payment-badge">AN TOÀN</span>
                                    </div>
                                    <p class="payment-description">Visa, Mastercard, JCB, American Express</p>
                                    <div class="card-logos">
                                        <div class="card-logo" style="background: #1434CB; color: white;">VISA</div>
                                        <div class="card-logo" style="background: #EB001B; color: white;">MC</div>
                                        <div class="card-logo" style="background: #006FCF; color: white;">JCB</div>
                                        <div class="card-logo">AMEX</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Ví MoMo -->
                           
                        </div>

                        <!-- Form nhập thông tin thẻ (ẩn mặc định) -->
                        <!-- <div class="credit-card-form" id="creditCardForm">
                            <h5><i class="fas fa-lock"></i> Thông tin thẻ</h5>
                            
                            <div class="card-input-group">
                                <label>Số thẻ <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="card_number" 
                                       id="cardNumber"
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19"
                                       pattern="[0-9 ]*">
                            </div>
                            
                            <div class="card-input-group">
                                <label>Tên chủ thẻ <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="card_holder" 
                                       id="cardHolder"
                                       placeholder="NGUYEN VAN A"
                                       style="text-transform: uppercase;">
                            </div>
                            
                            <div class="card-input-row">
                                <div class="card-input-group">
                                    <label>Ngày hết hạn <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="card_expiry" 
                                           id="cardExpiry"
                                           placeholder="MM/YY"
                                           maxlength="5"
                                           pattern="[0-9/]*">
                                </div>
                                
                                <div class="card-input-group">
                                    <label>CVV <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="card_cvv" 
                                           id="cardCVV"
                                           placeholder="123"
                                           maxlength="4"
                                           pattern="[0-9]*">
                                </div>
                            </div>
                            
                            <div class="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>Thông tin thẻ được mã hóa SSL 256-bit</span>
                            </div>
                        </div> -->
                    </div>

                    <div class="form-check mb-3 mt-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Tôi đồng ý với <a href="#" target="_blank">Điều khoản dịch vụ</a> và <a href="#" target="_blank">Chính sách hủy phòng</a>
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
                    <img src="<?= htmlspecialchars($homestay['image_url']) ?>" 
                         alt="<?= htmlspecialchars($homestay['homestay_name']) ?>">
                    <div class="homestay-info">
                        <h3><?= htmlspecialchars($homestay['homestay_name']) ?></h3>
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($homestay['location']) ?></p>
                        <p><i class="fas fa-star text-warning"></i> 4.9 (127 đánh giá)</p>
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-row">
                        <span>Nhận phòng</span>
                        <strong id="displayCheckin"><?= date('d/m/Y', strtotime($checkin)) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Trả phòng</span>
                        <strong id="displayCheckout"><?= date('d/m/Y', strtotime($checkout)) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Số đêm</span>
                        <strong id="displayNights"><?= $nights ?> đêm</strong>
                    </div>
                    <div class="detail-row">
                        <span>Số khách</span>
                        <strong><?= $guests ?> người</strong>
                    </div>
                </div>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span><?= number_format($price_per_night) ?>đ x <span id="nightsCalc"><?= $nights ?></span> đêm</span>
                        <strong id="displaySubtotal"><?= number_format($subtotal) ?>đ</strong>
                    </div>

                    <div class="total-row">
                        <span>Tổng cộng</span>
                        <strong id="displayTotal"><?= number_format($total) ?>đ</strong>
                    </div>

                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Chính sách hủy phòng:</strong><br>
                    <small>
                        • Miễn phí hủy trước 7 ngày<br>
                        • Hoàn 50% nếu hủy trước 3 ngày<br>
                        • Không hoàn tiền trong vòng 3 ngày
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const pricePerNight = <?= $price_per_night ?>;
        
        // Payment option selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                // Bỏ chọn các option khác
                document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');

                // Lấy giá trị tiếng Việt từ radio bên trong
                // const selectedValue = this.querySelector('input[type="radio"]').value;
                // document.getElementById('paymentInput').value = selectedValue;

                // Nếu là thẻ tín dụng thì hiện form thẻ
                const paymentType = this.dataset.payment;
                const creditCardForm = document.getElementById('creditCardForm');

                if (paymentType === 'credit_card') {
                    creditCardForm.classList.add('show');
                } else {
                    creditCardForm.classList.remove('show');
                }
            });
        });


        // Format card number (add spaces every 4 digits)
        document.getElementById('cardNumber')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date (MM/YY)
        document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\//g, '');
            if (value.length >= 2) {
                e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4);
            } else {
                e.target.value = value;
            }
        });

        // Only allow numbers for CVV
        document.getElementById('cardCVV')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Recalculate price when dates change
        function recalculateDates() {
            const checkin = new Date(document.getElementById('checkinInput').value);
            const checkout = new Date(document.getElementById('checkoutInput').value);
            
            if (checkout > checkin) {
                const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
                const subtotal = pricePerNight * nights;
                const total = subtotal ;
                
                // Update display
                document.getElementById('displayNights').textContent = nights + ' đêm';
                document.getElementById('nightsCalc').textContent = nights;
                document.getElementById('displaySubtotal').textContent = subtotal.toLocaleString('vi-VN') + 'đ';
                document.getElementById('displayTotal').textContent = total.toLocaleString('vi-VN') + 'đ';
                
                // Update hidden fields
                document.querySelector('input[name="nights"]').value = nights;
                document.querySelector('input[name="subtotal"]').value = subtotal;
                document.querySelector('input[name="total"]').value = total;
            }
        }

        document.getElementById('checkinInput').addEventListener('change', recalculateDates);
        document.getElementById('checkoutInput').addEventListener('change', recalculateDates);

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!document.getElementById('terms').checked) {
                e.preventDefault();
                alert('Vui lòng đồng ý với điều khoản dịch vụ!');
                return false;
            }
            
            const checkin = new Date(document.getElementById('checkinInput').value);
            const checkout = new Date(document.getElementById('checkoutInput').value);
            
            if (checkout <= checkin) {
                e.preventDefault();
                alert('Ngày trả phòng phải sau ngày nhận phòng!');
                return false;
            }
        });
    </script>
</body>
</html>