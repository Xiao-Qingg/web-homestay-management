<?php
session_start();
require_once __DIR__ . '/../../functions/auth_functions.php';

checkLogin('../../views/login.php');
$current_page = 'bookings';
$page_title = 'Quản lý Booking';

include './menu.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/homestay_admin.css">
</head>
<body>
<main class="main-content" style="margin-left: 260px; padding-left: 20px;">
    <div class="header d-flex justify-content-between align-items-center mb-3">
        <h1><i class="fas fa-calendar-check"></i> Quản lý Booking</h1>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>STT</th>
                        <th>Họ tên</th>
                        <th>Tên Homestay</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require '../../handles/booking_process.php';    
                    $bookings = handleGetAllBookings();

                    if (empty($bookings)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có booking nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php $index = 1; foreach ($bookings as $b): ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= htmlspecialchars($b['fullname']) ?></td>
                                <td><?= htmlspecialchars($b['homestay_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($b['check_in'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($b['check_out'])) ?></td>
                                <td><?= number_format($b['total_price'], 0, ',', '.') ?> đ</td>
                                <td>
                                    <select style="width:150px; height:30px;" class="form-select form-select-sm status-select" 
                                            data-booking-id="<?= $b['booking_id'] ?>">
                                        <?php
                                        $options = ['Đang chờ xử lý', 'Đã xác nhận', 'Đã hủy', 'Đã hoàn thành'];
                                        foreach ($options as $option):
                                        ?>
                                            <option value="<?= $option ?>" <?= $b['status'] === $option ? 'selected' : '' ?>>
                                                <?= $option ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <a href="booking_detail.php?booking_id=<?= $b['booking_id'] ?>" 
                                       class="btn btn-info btn-sm me-1 text-white"
                                       title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i> 
                                    </a>
                                    <a href="../../handles/booking_process.php?action=delete&booking_id=<?= $b['booking_id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xóa booking này?')"
                                       title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<script src="../../assets/js/booking_admin.js"></script>

</body>
</html>