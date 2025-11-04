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
    case 'edit':
        handleEditBooking();
        break;
    case 'delete':
        handleDeleteBooking();
        break;
    case 'deleteFromUser':
        handleDeleteBookingUser();
        break;
    // default:
    //     header("Location: ../views/dashboard/dashboard.php?error=Hành động không hợp lệ");
    //     exit();
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
    $status = 'Đang chờ xử lý'; // Mặc định là pending

    // Validate dữ liệu
    if (empty($homestay_id) || empty($user_id) || empty($check_in) || 
        empty($check_out) || empty($num_people) || empty($total_price)
        || empty($fullname) || empty($address) || empty($phone)){
        header("Location: ../views/index.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi hàm addBooking từ booking_functions.php
    // Lưu ý: homestay_detail_id = homestay_id (nếu bạn không có bảng homestay_details riêng)
    $result = addBooking($homestay_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address);

    if ($result) {
        //thêm đoạn để hiển thị thông báo thành công
        $_SESSION['booking_success'] = "Đặt phòng thành công! Chúng tôi sẽ liên hệ với bạn qua số điện thoại $phone.";
        header("Location: ../index.php?success=1");
    } else {
        // Thất bại: quay lại trang booking với thông báo lỗi
        header("Location: ../index.php?error=Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại!");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa booking
 */
function handleEditBooking() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/booking.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (
        !isset($_POST['booking_id']) ||
        !isset($_POST['homestay_detail_id']) ||
        !isset($_POST['user_id']) ||
        !isset($_POST['check_in']) ||
        !isset($_POST['check_out']) ||
        !isset($_POST['num_people']) ||
        !isset($_POST['total_price']) ||
        !isset($_POST['status'])
    ) {
        header("Location: ../views/booking/edit_booking.php?booking_id=" . ($_POST['booking_id'] ?? '') . "&error=Thiếu thông tin cần thiết");
        exit();
    }

    $booking_id = $_POST['booking_id'];
    $homestay_detail_id = trim($_POST['homestay_detail_id']);
    $user_id = trim($_POST['user_id']);
    $check_in = trim($_POST['check_in']);
    $check_out = trim($_POST['check_out']);
    $num_people = trim($_POST['num_people']);
    $total_price = trim($_POST['total_price']);
    $status = trim($_POST['status']);

    if (
        empty($booking_id) || empty($homestay_detail_id) || empty($user_id) ||
        empty($check_in) || empty($check_out) ||
        empty($num_people) || empty($total_price) || empty($status)
    ) {
        header("Location: ../views/booking/edit_booking.php?booking_id=" . $booking_id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    $result = updateBooking($booking_id, $homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status);

    if ($result) {
        header("Location: ../views/booking.php?success=Cập nhật booking thành công");
    } else {
        header("Location: ../views/booking/edit_booking.php?booking_id=" . $booking_id . "&error=Cập nhật booking thất bại");
    }
    exit();
}

/**
 * Xử lý xóa booking
 */
function handleDeleteBooking() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/dashboard/booking.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
        header("Location: ../view/dashboard/booking.php?error=Không tìm thấy ID booking");
        exit();
    }

    $booking_id = $_GET['booking_id'];
    if (!is_numeric($booking_id)) {
        header("Location: ../views/dashboard/booking.php?error=ID booking không hợp lệ");
        exit();
    }

    $result = deleteBooking($booking_id);

    if ($result) {
        header("Location: ../views/dashboard/booking.php?success=Xóa booking thành công");
    } else {
        header("Location: ../views/dashboard/booking.php?error=Xóa booking thất bại");
    }
    exit();
}

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
        header("Location: ../views/my_bookings.phpp?error=Xóa booking thất bại");
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    require_once __DIR__ . '/../functions/db_connection.php';

    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['status'];

    $conn = getDbConnection();
    $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "success";
    } else {
        echo "fail";
    }

    mysqli_close($conn);
    exit();
}


?>


