<?php
// session_start();
require_once __DIR__ . '/../functions/homestay_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateHomestay();
        break;
    case 'edit':
        handleEditHomestay();
        break;
    case 'delete':
        handleDeleteHomestay();
        break;
    // default:
    //     header("Location: ../views/dashboard/homestay.php?error=Hành động không xác định");
    //     exit();
}

/**
 * Lấy tất cả danh sách homestays
 */
function handleGetAllHomestays() {
    return getAllHomestays();
}

function handleGetHomestayById($id) {
    return getHomestayById($id);
}

/**
 * Xử lý tạo homestay mới với đầy đủ thông tin
 */
function handleCreateHomestay() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/dashboard/homestay.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Kiểm tra các trường bắt buộc
    $required_fields = ['homestay_name', 'location', 'price_per_night', 'num_room', 'max_people', 'image_url', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            header("Location: ../views/dashboard/homestay/create_homestay.php?error=Thiếu trường: " . $field);
            exit();
        }
    }
    
    // Lấy dữ liệu cơ bản
    $homestay_name = trim($_POST['homestay_name']);
    $location = trim($_POST['location']);
    $price_per_night = trim($_POST['price_per_night']);
    $num_room = trim($_POST['num_room']);
    $max_people = trim($_POST['max_people']);
    $image_url = trim($_POST['image_url']);
    $status = trim($_POST['status']);
    
    // Validate dữ liệu cơ bản
    if (empty($homestay_name) || empty($location) || empty($price_per_night) || 
        empty($num_room) || empty($max_people) || empty($status)) {
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Validate số liệu
    if (!is_numeric($price_per_night) || !is_numeric($num_room) || !is_numeric($max_people)) {
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Dữ liệu số không hợp lệ");
        exit();
    }
    
    if ($price_per_night < 0 || $num_room < 1 || $max_people < 1) {
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Giá trị số không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu JSON từ hidden inputs
    $rooms_json = isset($_POST['rooms']) ? $_POST['rooms'] : '[]';
    $amenities_json = isset($_POST['amenities']) ? $_POST['amenities'] : '[]';
    $images_json = isset($_POST['images']) ? $_POST['images'] : '[]';
    
    // Log để debug
    error_log("=== CREATE HOMESTAY ===");
    error_log("Rooms JSON: " . $rooms_json);
    error_log("Amenities JSON: " . $amenities_json);
    error_log("Images JSON: " . $images_json);
    
    // Decode JSON
    $rooms = json_decode($rooms_json, true);
    $amenities = json_decode($amenities_json, true);
    $images = json_decode($images_json, true);
    
    // Validate JSON decode với thông báo chi tiết
    if ($rooms === null) {
        $json_error = json_last_error_msg();
        error_log("❌ JSON decode error for rooms: " . $json_error);
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Dữ liệu phòng không hợp lệ: " . urlencode($json_error));
        exit();
    }
    
    if ($amenities === null) {
        $json_error = json_last_error_msg();
        error_log("❌ JSON decode error for amenities: " . $json_error);
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Dữ liệu tiện nghi không hợp lệ: " . urlencode($json_error));
        exit();
    }
    
    if ($images === null) {
        $json_error = json_last_error_msg();
        error_log("❌ JSON decode error for images: " . $json_error);
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Dữ liệu ảnh không hợp lệ: " . urlencode($json_error));
        exit();
    }
    
    // Kiểm tra tối thiểu 4 phòng
    if (count($rooms) < 4) {
        error_log("❌ Not enough rooms: " . count($rooms));
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Vui lòng thêm ít nhất 4 phòng (hiện có: " . count($rooms) . ")");
        exit();
    }
    
    // Kiểm tra có ít nhất 1 ảnh
    if (count($images) < 1) {
        error_log("❌ No images provided");
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Vui lòng thêm ít nhất 1 ảnh");
        exit();
    }
    
    // Validate cấu trúc rooms
    foreach ($rooms as $index => $room) {
        if (!isset($room['name']) || !isset($room['capacity'])) {
            error_log("❌ Invalid room structure at index $index");
            header("Location: ../views/dashboard/homestay/create_homestay.php?error=Cấu trúc dữ liệu phòng không hợp lệ");
            exit();
        }
    }
    
    // Validate cấu trúc images
    foreach ($images as $index => $image) {
        if (!isset($image['url']) || !isset($image['roomId'])) {
            error_log("❌ Invalid image structure at index $index");
            header("Location: ../views/dashboard/homestay/create_homestay.php?error=Cấu trúc dữ liệu ảnh không hợp lệ");
            exit();
        }
    }
    
    // Log data để debug
    error_log("✓ Creating homestay with " . count($rooms) . " rooms, " . count($amenities) . " amenities, " . count($images) . " images");
    
    // Gọi hàm thêm homestay đầy đủ
    $result = addHomestayComplete(
        $homestay_name, 
        $location, 
        $price_per_night, 
        $num_room, 
        $max_people, 
        $image_url, 
        $status,
        $rooms,
        $amenities,
        $images
    );
    
    if ($result) {
        error_log("✅ Create successful! Homestay ID: $result");
        header("Location: ../views/dashboard/homestay.php?success=Thêm homestay thành công (ID: $result)");
    } else {
        error_log("❌ Create failed - check logs above");
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Có lỗi xảy ra khi thêm homestay. Vui lòng kiểm tra log");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa homestay (cập nhật đầy đủ rooms, amenities, images)
 */
function handleEditHomestay() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/dashboard/homestay.php?error=Phương thức không hợp lệ");
        exit();
    }

    // Kiểm tra các trường bắt buộc
    $required_fields = ['id', 'homestay_name', 'location', 'price_per_night', 'num_room', 'max_people', 'image_url', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $redirect_id = isset($_POST['id']) ? $_POST['id'] : '';
            header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$redirect_id&error=Thiếu trường: " . $field);
            exit();
        }
    }

    $id = trim($_POST['id']);
    $homestay_name = trim($_POST['homestay_name']);
    $location = trim($_POST['location']);
    $price_per_night = trim($_POST['price_per_night']);
    $num_room = trim($_POST['num_room']);
    $max_people = trim($_POST['max_people']);
    $image_url = trim($_POST['image_url']);
    $status = trim($_POST['status']);

    // Validate dữ liệu cơ bản
    if (empty($homestay_name) || empty($location) || empty($price_per_night) || 
        empty($num_room) || empty($max_people) || empty($status) || empty($id)) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    if (!is_numeric($price_per_night) || !is_numeric($num_room) || !is_numeric($max_people) || !is_numeric($id)) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Dữ liệu không hợp lệ");
        exit();
    }
    
    if ($price_per_night < 0 || $num_room < 1 || $max_people < 1) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Giá trị số không hợp lệ");
        exit();
    }

    // Lấy dữ liệu JSON từ hidden inputs
    $rooms_json = isset($_POST['rooms']) ? $_POST['rooms'] : '[]';
    $amenities_json = isset($_POST['amenities']) ? $_POST['amenities'] : '[]';
    $images_json = isset($_POST['images']) ? $_POST['images'] : '[]';

    // Log để debug
    error_log("=== HANDLE EDIT HOMESTAY #$id ===");
    error_log("Rooms JSON: " . $rooms_json);
    error_log("Amenities JSON: " . $amenities_json);
    error_log("Images JSON: " . $images_json);

    $rooms = json_decode($rooms_json, true);
    $amenities = json_decode($amenities_json, true);
    $images = json_decode($images_json, true);

    // Validate JSON với thông báo chi tiết hơn
    if ($rooms === null) { 
        $json_error = json_last_error_msg(); 
        error_log("❌ JSON decode error for rooms: " . $json_error);
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Dữ liệu phòng không hợp lệ: " . urlencode($json_error)); 
        exit(); 
    }
    
    if ($amenities === null) { 
        $json_error = json_last_error_msg(); 
        error_log("❌ JSON decode error for amenities: " . $json_error);
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Dữ liệu tiện nghi không hợp lệ: " . urlencode($json_error)); 
        exit(); 
    }
    
    if ($images === null) { 
        $json_error = json_last_error_msg(); 
        error_log("❌ JSON decode error for images: " . $json_error);
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Dữ liệu ảnh không hợp lệ: " . urlencode($json_error)); 
        exit(); 
    }

    // Log sau khi decode thành công
    error_log("✓ Decoded - Rooms: " . count($rooms) . ", Amenities: " . count($amenities) . ", Images: " . count($images));

    // Validate cấu trúc rooms
    foreach ($rooms as $index => $room) {
        if (!isset($room['name']) || !isset($room['capacity'])) {
            error_log("❌ Invalid room structure at index $index");
            header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Cấu trúc dữ liệu phòng không hợp lệ");
            exit();
        }
    }

    // Validate cấu trúc images
    foreach ($images as $index => $image) {
        if (!isset($image['url']) || !isset($image['roomId'])) {
            error_log("❌ Invalid image structure at index $index");
            header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Cấu trúc dữ liệu ảnh không hợp lệ");
            exit();
        }
    }

    // Kiểm tra tối thiểu 4 phòng
    if (count($rooms) < 4) {
        error_log("❌ Not enough rooms: " . count($rooms));
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Vui lòng thêm ít nhất 4 phòng (hiện có: " . count($rooms) . ")");
        exit();
    }

    // Kiểm tra ít nhất 1 ảnh
    if (count($images) < 1) {
        error_log("❌ No images provided");
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Vui lòng thêm ít nhất 1 ảnh");
        exit();
    }

    error_log("✓ All validations passed, calling updateHomestayComplete...");

    // Gọi hàm update homestay đầy đủ
    $result = updateHomestayComplete(
        $id,
        $homestay_name,
        $location,
        $price_per_night,
        $num_room,
        $max_people,
        $image_url,
        $status,
        $rooms,
        $amenities,
        $images
    );

    if ($result) {
        error_log("✅ Update successful!");
        header("Location: ../views/dashboard/homestay.php?success=Cập nhật homestay thành công");
    } else {
        error_log("❌ Update failed - check logs above");
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=$id&error=Cập nhật homestay thất bại. Kiểm tra log để biết lỗi.");
    }
    exit();
}

/**
 * Xử lý xóa homestay
 */
function handleDeleteHomestay() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/dashboard/homestay.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/dashboard/homestay.php?error=Không tìm thấy ID homestay");
        exit();
    }

    $id = $_GET['id'];
    if (!is_numeric($id)) {
        header("Location: ../views/dashboard/homestay.php?error=ID homestay không hợp lệ");
        exit();
    }

    error_log("=== DELETE HOMESTAY #$id ===");

    $result = deleteHomestay($id);

    if ($result) {
        error_log("✅ Delete successful!");
        header("Location: ../views/dashboard/homestay.php?success=Xóa homestay thành công");
    } else {
        error_log("❌ Delete failed");
        header("Location: ../views/dashboard/homestay.php?error=Xóa homestay thất bại");
    }
    exit();
}

?>