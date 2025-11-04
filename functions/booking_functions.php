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
            b.address
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN homestay_details hd ON b.homestay_detail_id = hd.id
        JOIN homestays h ON hd.homestay_id = h.id
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
 * Thêm booking mới
 * @param int $homestay_detail_id 
 * @param int $user_id
 * @param string $check_in
 * @param string $check_out
 * @param int $num_people
 * @param double $total_price
 * @param string $status
 * @return bool True nếu thành công, False nếu thất bại
 */
function addBooking($homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address) {
    $conn = getDbConnection();

    $sql = "
        INSERT INTO bookings (homestay_detail_id, user_id, check_in, check_out, num_people, total_price, status, fullname, phone, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissidssss", $homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address);
        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }

    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một booking theo ID
 * @param int $id ID của booking
 * @return array|null Thông tin booking hoặc null nếu không tìm thấy
 */
function getBookingsByUserId($user_id) {
    $conn = getDbConnection();

    $sql = "
        SELECT 
            b.booking_id,
            b.homestay_detail_id,
            b.check_in,
            b.check_out,
            b.num_people,
            b.total_price,
            b.status,
            b.created_at,
            h.id AS homestay_id,
            h.homestay_name,          
            b.address,
            h.location,
            h.image_url        
        FROM bookings b
        JOIN homestay_details hd ON b.homestay_detail_id = hd.id
        JOIN homestays h ON hd.homestay_id = h.id
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
            h.location
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN homestay_details hd ON b.homestay_detail_id = hd.id
        JOIN homestays h ON hd.homestay_id = h.id
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
function updateBooking($id, $homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address) {
    $conn = getDbConnection();

    $sql = "
        UPDATE bookings 
        SET homestay_detail_id = ?, user_id = ?, check_in = ?, check_out = ?, 
            num_people = ?, total_price = ?, status = ?, fullname = ?, phone = ?, address = ?
        WHERE booking_id = ?
    ";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissidssssi", $homestay_detail_id, $user_id, $check_in, $check_out, $num_people, $total_price, $status, $fullname, $phone, $address, $id);
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
 * @param int $id ID của booking cần xóa
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
