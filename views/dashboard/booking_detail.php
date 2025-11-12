<?php
session_start();
require_once __DIR__ . '/../../functions/auth_functions.php';
checkLogin('../../views/login.php');

$current_page = 'bookings';
$page_title = 'Chi tiết Booking';

include './menu.php';

// Lấy booking_id từ URL
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($booking_id <= 0) {
    header("Location: bookings.php?error=ID booking không hợp lệ");
    exit();
}

require_once '../../handles/booking_process.php';
$booking = getBookingDetailById($booking_id);

if (!$booking) {
    header("Location: bookings.php?error=Không tìm thấy booking");
    exit();
}

// Xử lý cập nhật ghi chú
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_note') {
    $note = trim($_POST['note'] ?? '');
    $result = updateBookingNote($booking_id, $note);
    if ($result) {
        $booking['note'] = $note; // Cập nhật lại để hiển thị
        $success_message = "Cập nhật ghi chú thành công!";
    } else {
        $error_message = "Cập nhật ghi chú thất bại!";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/homestay_admin.css">
    <style>
        .detail-card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .info-label { font-weight: 600; color: #495057; }
        .info-value { color: #212529; }
        .note-box { min-height: 120px; }
    </style>
</head>
<body>
<main class="main-content" style="margin-left: 260px; padding: 20px;">
    <div class="header d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-check"></i> Chi tiết Booking #<?= $booking['booking_id'] ?></h1>
        <a href="./booking.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="content-card detail-card p-4">
        <div class="row g-4">
            <!-- Thông tin khách hàng -->
            <div class="col-lg-6">
                <h5 class="border-bottom pb-2 mb-3 text-primary"><i class="fas fa-user"></i> Thông tin khách hàng</h5>
                <table class="table table-borderless">
                    <tr>
                        <td class="info-label">Họ tên:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['fullname']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Số điện thoại:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['phone']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Địa chỉ:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['address'] ?: 'Chưa cung cấp') ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Người đặt (tài khoản):</td>
                        <td class="info-value"><?= htmlspecialchars($booking['customer_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">SĐT tài khoản:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['customer_phone']) ?></td>
                    </tr>
                </table>
            </div>

            <!-- Thông tin đặt phòng -->
            <div class="col-lg-6">
                <h5 class="border-bottom pb-2 mb-3 text-primary"><i class="fas fa-home"></i> Thông tin đặt phòng</h5>
                <table class="table table-borderless">
                    <tr>
                        <td class="info-label">Homestay:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['homestay_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Địa điểm:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['location']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Check-in:</td>
                        <td class="info-value"><?= date('d/m/Y', strtotime($booking['check_in'])) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Check-out:</td>
                        <td class="info-value"><?= date('d/m/Y', strtotime($booking['check_out'])) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Số người:</td>
                        <td class="info-value"><?= (int)$booking['num_people'] ?> người</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tổng tiền:</td>
                        <td class="info-value text-danger fw-bold">
                            <?= number_format($booking['total_price'], 0, ',', '.') ?> đ
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Phương thức thanh toán:</td>
                        <td class="info-value"><?= htmlspecialchars($booking['payment']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Trạng thái:</td>
                        <td>
                            <span class="badge <?= 
                                $booking['status'] === 'Đã xác nhận' ? 'bg-success' :
                                ($booking['status'] === 'Đang chờ xử lý' ? 'bg-warning' :
                                ($booking['status'] === 'Đã hủy' ? 'bg-danger' : 'bg-primary'))
                            ?>">
                                <?= htmlspecialchars($booking['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Ngày đặt:</td>
                        <td class="info-value"><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Ghi chú trước check-out -->
        <!-- Thay thế phần form ghi chú bằng đoạn này -->
        <div class="mt-4">
            <h5 class="border-bottom pb-2 text-primary"><i class="fas fa-sticky-note"></i> Ghi chú (trước khi check-out)</h5>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3">
                    <i class="fas fa-check-circle"></i> <?= $success_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" class="mt-3">
                <input type="hidden" name="action" value="update_note">
                <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
                
                <div class="mb-3">
                    <textarea name="note" class="form-control note-box" placeholder="Nhập ghi chú về tình trạng phòng, yêu cầu khách, hoặc vấn đề cần lưu ý..." 
                            rows="5"><?= htmlspecialchars($booking['note'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu ghi chú
                </button>
            </form>
        </div>

        <!-- Thông tin check-out thực tế (nếu có) -->
        <?php if (!empty($booking['actual_checkout'])): ?>
        <div class="mt-4 p-3 bg-light rounded">
            <h6 class="text-success"><i class="fas fa-sign-out-alt"></i> Check-out thực tế</h6>
            <p class="mb-1"><strong>Thời gian:</strong> <?= date('d/m/Y H:i', strtotime($booking['actual_checkout'])) ?></p>
            <?php if (!empty($booking['checkout_note'])): ?>
                <p class="mb-0"><strong>Ghi chú check-out:</strong> <?= nl2br(htmlspecialchars($booking['checkout_note'])) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>