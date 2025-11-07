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
    <link rel="stylesheet" href="../../css/dashboard.css">
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
                    <tr >
                        <th>STT</th>
                        <th>Họ tên</th>
                        <th>Số điện thoại</th>
                        <th>Tên Homestay</th>
                        <th>Địa chỉ</th>
                        <th>Số người</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                         <th>Tổng tiền</th>
                        <th>Phương thức</th>
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
                            <td colspan="10" class="text-center">Không có booking nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php $index = 1; foreach ($bookings as $b): ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= htmlspecialchars($b['fullname']) ?></td>
                                <td><?= htmlspecialchars($b['phone']) ?></td>
                                <td><?= htmlspecialchars($b['homestay_name']) ?></td>
                                <td><?= htmlspecialchars($b['address']) ?></td>
                                <td><?= htmlspecialchars($b['num_people']) ?></td>
                                <td><?= htmlspecialchars($b['check_in']) ?></td>
                                <td><?= htmlspecialchars($b['check_out']) ?></td>
                                <td><?= htmlspecialchars($b['total_price']) ?></td>
                                <td><?= htmlspecialchars($b['payment']) ?></td>
                               <td>
                                    <select style="width:120px; height:30px;" class="form-select form-select-sm status-select" 
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
                                    <a href="../../handles/booking_process.php?action=delete&booking_id=<?= $b['booking_id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xóa booking này?')">
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

<script>
// Chạy sau khi DOM đã load xong
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', async function() {
            const bookingId = this.dataset.bookingId;
            const newStatus = this.value;
            const originalStatus = this.querySelector('option[selected]')?.value || this.value;

            try {
                const res = await fetch('../../handles/booking_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'update_status',
                        booking_id: bookingId,
                        status: newStatus
                    })
                });

                const result = await res.text();
                console.log('Response:', result);

                if (result.trim() === 'success') {
                    alert('Cập nhật trạng thái thành công!');
                    // Cập nhật selected attribute
                    this.querySelectorAll('option').forEach(opt => {
                        opt.removeAttribute('selected');
                    });
                    this.querySelector(`option[value="${newStatus}"]`).setAttribute('selected', 'selected');
                } else {
                    alert('Có lỗi khi cập nhật trạng thái!');
                    this.value = originalStatus; // Khôi phục giá trị cũ
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Lỗi kết nối đến server!');
                this.value = originalStatus; // Khôi phục giá trị cũ
            }
        });
    });
});
</script>

</body>
</html>