<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách homestays từ database
 * @return array Danh sách Homestay
 */
function getAllHomestays() {
    $conn = getDbConnection();
    
    // Truy vấn lấy tất cả students
   $sql = "SELECT id, homestay_name, location, price_per_night, num_room, max_people, image_url, status FROM homestays ORDER BY id";
    $result = mysqli_query($conn, $sql);
    
    $homestays = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $homestays[] = $row; // Thêm mảng $row vào cuối mảng $students
        }
    }
    
    mysqli_close($conn);
    return $homestays;
}

/**
 * Thêm student mới
 * @param string $homestay_name Mã sinh viên
 * @param string $location Tên sinh viên
 * @param double $price_per_night
 * @param int $num_room
 * @param int $max_people
 * @param string $image_url
 * @param string $status
 * @return bool True nếu thành công, False nếu thất bại
 */
function addHomestay($homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO homestays (homestay_name, location, price_per_night, num_room, max_people, image_url, status) VALUES (?, ?,?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssdiiss", $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một student theo ID
 * @param int $id ID của student
 * @return array|null Thông tin student hoặc null nếu không tìm thấy
 */
function getHomestayById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, homestay_name, location, price_per_night, num_room, max_people, image_url, status FROM homestays WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $homestay = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $homestay;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}
function getHomestayDetailById($id) {
    $conn = getDbConnection();

    $sql = "
        SELECT 
            hd.id AS detail_id,
            h.id AS homestay_id,
            h.homestay_name AS homestay_name,
            h.location AS location ,
            h.price_per_night AS price_per_night,
            h.num_room AS num_room,
            h.max_people AS max_people,
            h.image_url AS image_url,
            r.room_name AS room_name,
            r.price AS price,
            r.description AS description,
            r.capacity AS capacity,
            a.name AS amenity_name,
            hd.description AS description,
            hd.host AS host,
            i.room_image_url AS room_image_url
        FROM homestay_details hd
        JOIN homestays h ON hd.homestay_id = h.id
        LEFT JOIN rooms r ON hd.room_id = r.id
        LEFT JOIN amenities a ON hd.amenities_id = a.id
        LEFT JOIN room_images i ON hd.image_id = i.id
        WHERE hd.id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $detail = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $detail;
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
    return null;
}

function getRoomsByHomestayId($homestay_id) {
    $conn = getDbConnection();

    $sql = "
        SELECT 
            r.id AS room_id,
            r.room_name,
            r.price,
            r.description,
            r.capacity,
            a.name AS amenity_name,
            i.room_image_url
        FROM homestay_details hd
        JOIN rooms r ON hd.room_id = r.id
        LEFT JOIN amenities a ON hd.amenities_id = a.id
        LEFT JOIN room_images i ON hd.image_id = i.id
        WHERE hd.homestay_id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rooms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $rooms;
}


function getHomestayImages($homestay_id) {
    $conn = getDbConnection();
    $sql = "
        SELECT i.room_image_url
        FROM homestay_details hd
        JOIN room_images i ON hd.image_id = i.id
        WHERE hd.homestay_id = ?
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $homestay_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}





/**
 * Cập nhật thông tin student
 * @param int $id ID của student
 * @param string $student_code Mã sinh viên mới
 * @param string $student_name Tên sinh viên mới
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateHomestay($id, $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE homestays SET homestay_name = ?, location = ?, price_per_night = ?, num_room = ?, max_people = ?, image_url = ?, status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssdiissi", $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa student theo ID
 * @param int $id ID của student cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteHomestay($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM homestays WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}
?>
