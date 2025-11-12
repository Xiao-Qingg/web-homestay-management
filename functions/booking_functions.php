<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách bookings từ database
 * @return array Danh sách bookings
 */
function getAllBookings() {
    $conn = getDbConnection();

    $sql = "
        SELECT 
            b.booking_id,
            b.check_in,
            b.check_out,
            b.num_people,
            b.total_price,
            b.status,
            b.created_at,
            u.fullname AS customer_name,
            u.phone AS customer_phone,
            h.homestay_name,
            h.location,
            b.fullname,
            b.phone,
            b.address,
            b.payment
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN homestays h ON b.homestay_id = h.id
        ORDER BY b.created_at DESC
    ";

    $result = mysqli_query($conn, $sql);
    $bookings = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
    }

    mysqli_close($conn);
    return $bookings;
}

/**
 * Lấy chi tiết một booking theo ID (cho trang booking_detail.php)
 * @param int $booking_id ID của booking
 * @return array|null Thông tin booking hoặc null nếu không tìm thấy
 */
function getBookingDetailById($booking_id) {
    $conn = getDbConnection();

    $sql = "
        SELECT 
            b.booking_id,
            b.check_in,
            b.check_out,
            b.num_people,
            b.total_price,
            b.status,
            b.created_at,
            b.fullname,
            b.phone,
            b.address,
            b.payment,
            b.note,
            u.fullname AS customer_name,
            u.phone AS customer_phone,
            h.homestay_name,
            h.location
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN homestays h ON b.homestay_id = h.id
        WHERE b.booking_id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $booking;
}

/**
 * Thêm booking mới
 * @param int $homestay_id 
 * @param int $user_id
 * @param string $check_in
 * @param string $check_out
 * @param int $num_people
 * @param double $total_price
 * @param string $status
 * @return bool True nếu thành công, False nếu thất bại
 */
function addBooking($homestay_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address, $payment) {
    $conn = getDbConnection();

    $sql = "
        INSERT INTO bookings (homestay_id, user_id, check_in, check_out, num_people, total_price, status, fullname, phone, address, payment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissidsssss", $homestay_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address, $payment);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin bookings theo user ID
 * @param int $user_id ID của user
 * @return array Danh sách bookings
 */
function getBookingsByUserId($user_id) {
    $conn = getDbConnection();

    $sql = "
       SELECT 
            b.booking_id,
            h.homestay_name AS homestay_name,
            h.location,
            h.price_per_night,
            h.image_url,
            b.check_in,
            b.check_out,
            b.num_people,
            b.total_price,
            b.status,
            b.created_at,
            b.fullname,
            b.phone,
            b.address,
            b.payment
        FROM bookings b
        JOIN homestays h ON b.homestay_id = h.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $bookings;
}

/**
 * Lấy thông tin một booking theo ID (hàm cũ - giữ lại để tương thích)
 * @param int $booking_id ID của booking
 * @return array|null Thông tin booking hoặc null nếu không tìm thấy
 */
function getBookingById($booking_id) {
    $conn = getDbConnection();

    $sql = "
        SELECT 
            b.booking_id,
            b.check_in,
            b.check_out,
            b.num_people,
            b.total_price,
            b.status,
            b.created_at,
            u.fullname AS customer_name,
            u.phone AS customer_phone,
            h.homestay_name,
            h.location,
            b.payment
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN homestays h ON b.homestay_id = h.id
        WHERE b.booking_id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $booking;
}

/**
 * Cập nhật ghi chú của booking
 * @param int $booking_id ID của booking
 * @param string $note Nội dung ghi chú
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateBookingNote($booking_id, $note) {
    $conn = getDbConnection();
    
    $sql = "UPDATE bookings SET note = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $note, $booking_id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật trạng thái của booking
 * @param int $booking_id ID của booking
 * @param string $status Trạng thái mới
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateBookingStatus($booking_id, $status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật checkout và ghi chú
 * @param int $booking_id ID của booking
 * @param string $checkout_time Thời gian checkout thực tế
 * @param string $note Ghi chú
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateBookingCheckout($booking_id, $checkout_time, $note) {
    $conn = getDbConnection();
    
    $sql = "UPDATE bookings 
            SET actual_checkout = ?, 
                checkout_note = ?,
                status = 'Đã check-out'
            WHERE booking_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $checkout_time, $note, $booking_id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật thông tin booking
 * @param int $id ID của booking
 * @param int $homestay_detail_id
 * @param int $user_id
 * @param string $check_in
 * @param string $check_out
 * @param int $num_people
 * @param double $total_price
 * @param string $status
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateBooking($id, $homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address, $payment) {
    $conn = getDbConnection();

    $sql = "
        UPDATE bookings 
        SET homestay_detail_id = ?, user_id = ?, check_in = ?, check_out = ?, 
            num_people = ?, total_price = ?, status = ?, fullname = ?, phone = ?, address = ?, payment = ?
        WHERE booking_id = ?
    ";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissidssssi", $homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address, $payment, $id);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Xóa booking theo ID
 * @param int $booking_id ID của booking cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteBooking($booking_id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

?>