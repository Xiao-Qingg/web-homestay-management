<?php
require_once __DIR__ . '/../functions/booking_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateBooking();
        break;
    case 'delete':
        handleDeleteBooking();
        break;
    case 'deleteFromUser':
        handleDeleteBookingUser();
        break;
    case 'update_status':
        handleUpdateStatus();
        break;
    case 'update_note':
        handleUpdateNote();
        break;
}

/**
 * Lấy tất cả danh sách booking
 */
function handleGetAllBookings() {
    return getAllBookings();
}

function handleGetBookingById($id) {
    return getBookingById($id);
}

function handleGetBookingsByUserId($user_id) {
    return getBookingsByUserId($user_id);
}

/**
 * Xử lý tạo booking mới
 */
function handleCreateBooking() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/index.php?error=Phương thức không hợp lệ");
        exit();
    }

    // Lấy dữ liệu từ form
    $homestay_id = isset($_POST['homestay_id']) ? (int)$_POST['homestay_id'] : 0;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $check_in = isset($_POST['checkin']) ? trim($_POST['checkin']) : '';
    $check_out = isset($_POST['checkout']) ? trim($_POST['checkout']) : '';
    $num_people = isset($_POST['guests']) ? (int)$_POST['guests'] : 0;
    $total_price = isset($_POST['total']) ? (float)$_POST['total'] : 0;
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $payment = isset($_POST['payment']) ? trim($_POST['payment']) : 'Tiền mặt';
    $valid_payment_methods = ['Tiền mặt', 'Chuyển khoản ngân hàng', 'Thẻ tín dụng'];
    if (!in_array($payment, $valid_payment_methods)) {
        $payment = 'Tiền mặt';
    }
    
    $status = 'Đang chờ xử lý';

    // Validate dữ liệu
    if (empty($homestay_id) || empty($user_id) || empty($check_in) || 
        empty($check_out) || empty($num_people) || empty($total_price)
        || empty($fullname) || empty($address) || empty($phone) ){
        header("Location: ../views/index.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    $result = addBooking($homestay_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address, $payment);

    if ($result) {
        $_SESSION['booking_success'] = "Đặt phòng thành công! Chúng tôi sẽ liên hệ với bạn qua số điện thoại $phone.";
        header("Location: ../index.php?success=1");
    } else {
        header("Location: ../index.php?error=Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại!");
    }
    exit();
}

/**
 * Xử lý xóa booking
 */
function handleDeleteBooking() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/dashboard/bookings.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
        header("Location: ../views/dashboard/bookings.php?error=Không tìm thấy ID booking");
        exit();
    }

    $booking_id = $_GET['booking_id'];
    if (!is_numeric($booking_id)) {
        header("Location: ../views/dashboard/bookings.php?error=ID booking không hợp lệ");
        exit();
    }

    $result = deleteBooking($booking_id);

    if ($result) {
        header("Location: ../views/dashboard/bookings.php?success=Xóa booking thành công");
    } else {
        header("Location: ../views/dashboard/bookings.php?error=Xóa booking thất bại");
    }
    exit();
}

/**
 * Xử lý xóa booking từ user
 */
function handleDeleteBookingUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/my_bookings.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
        header("Location: ../views/my_bookings.php?error=Không tìm thấy ID booking");
        exit();
    }

    $booking_id = $_GET['booking_id'];
    if (!is_numeric($booking_id)) {
        header("Location: ../views/my_bookings.php?error=ID booking không hợp lệ");
        exit();
    }

    $result = deleteBooking($booking_id);

    if ($result) {
        header("Location: ../views/my_bookings.php?success=Xóa booking thành công");
    } else {
        header("Location: ../views/my_bookings.php?error=Xóa booking thất bại");
    }
    exit();
}

/**
 * Xử lý cập nhật trạng thái
 */
function handleUpdateStatus() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo 'error';
        exit();
    }

    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if ($booking_id <= 0 || empty($status)) {
        echo 'error';
        exit();
    }

    $result = updateBookingStatus($booking_id, $status);

    if ($result) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit();
}

/**
 * Xử lý cập nhật ghi chú
 */
/**
 * Xử lý cập nhật ghi chú
 */
function handleUpdateNote() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: bookings.php?error=Phương thức không hợp lệ");
        exit();
    }

    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';

    if ($booking_id <= 0) {
        header("Location: booking_detail.php?booking_id=$booking_id&error=ID không hợp lệ");
        exit();
    }

    $result = updateBookingNote($booking_id, $note);

    if ($result) {
        header("Location: booking_detail.php?booking_id=$booking_id&success=Ghi chú đã được lưu!");
    } else {
        header("Location: booking_detail.php?booking_id=$booking_id&error=Cập nhật thất bại!");
    }
    exit();
}

?>