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
    //     header("Location: ../views/dashboard/dashboard.php?error=Hành động không hợp lệ");
    //     exit();
}
/**
 * Lấy tất cả danh sách sinh viên
 */
function handleGetAllHomestays() {
    return getAllHomestays();
}

function handleGetHomestayById($id) {
    return getHomestayById($id);
}

/**
 * Xử lý tạo homestay mới
 */
function handleCreateHomestay() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/dashboard/homestay.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['homestay_name']) || !isset($_POST['location']) || !isset($_POST['price_per_night']) || !isset($_POST['num_room']) || !isset($_POST['max_people']) || !isset($_POST['image_url']) || !isset($_POST['status'])) {
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $homestay_name = trim($_POST['homestay_name']);
    $location = trim($_POST['location']);
    $price_per_night = trim($_POST['price_per_night']);
    $num_room = trim($_POST['num_room']);
    $max_people = trim($_POST['max_people']);
    $image_url = trim($_POST['image_url']);
    $status = trim($_POST['status']);
    
    // Validate dữ liệu
    if (empty($homestay_name) || empty($location) || empty($price_per_night) || empty($num_room) || empty($max_people) || empty($image_url) || empty($status)) {
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = addHomestay($homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status);
    
    if ($result) {
        header("Location: ../views/dashboard/homestay.php?success=Thêm homestay thành công");
    } else {
        header("Location: ../views/dashboard/homestay/create_homestay.php?error=Có lỗi xảy ra khi thêm homestay");
    }
    exit();
}
 // Xử lý chỉnh sửa homestay
function handleEditHomestay() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/dashboard/homestay.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['id']) || !isset($_POST['homestay_name']) || !isset($_POST['location']) || !isset($_POST['price_per_night']) || !isset($_POST['num_room']) || !isset($_POST['max_people']) || !isset($_POST['image_url']) || !isset($_POST['status'])) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=" . (isset($_POST['id']) ? $_POST['id'] : '') . "&error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = $_POST['id'];
    $homestay_name = trim($_POST['homestay_name']);
    $location = trim($_POST['location']);
    $price_per_night = trim($_POST['price_per_night']);
    $num_room = trim($_POST['num_room']);
    $max_people = trim($_POST['max_people']);
    $image_url = trim($_POST['image_url']);
    $status = trim($_POST['status']);

    if (empty($homestay_name) || empty($location) || empty($price_per_night) || empty($num_room) || empty($max_people) || empty($image_url) || empty($status)) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    if (!is_numeric($price_per_night) || !is_numeric($num_room) || !is_numeric($max_people) || !is_numeric($id)) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=" . $id . "&error=Dữ liệu không hợp lệ");
        exit();
    }

    $result = updateHomestay($id, $homestay_name, $location, $price_per_night, $num_room, $max_people, $image_url, $status);

    if ($result) {
        header("Location: ../views/dashboard/homestay.php?success=Cập nhật homestay thành công");
    } else {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=" . $id . "&error=Cập nhật homestay thất bại");
    }
    exit();
}

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

    $result = deleteHomestay($id);

    if ($result) {
        header("Location: ../views/dashboard/homestay.php?success=Xóa homestay thành công");
    } else {
        header("Location: ../views/dashboard/homestay.php?error=Xóa homestay thất bại");
    }
    exit();
}
?>
