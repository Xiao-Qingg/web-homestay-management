 <!-- user_process.php -->

<?php
// session_start();
require_once __DIR__ . '/../functions/user_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateUser();
        break;
    case 'lock':
    case 'unlock':
        handleToggleUserStatus();
        // handleEditUser();
        break;
    case 'delete':
        handleDeleteUser();
        break;
    
    // default:
    //     header("Location: ../views/dashboard/dashboard.php?error=Hành động không hợp lệ");
    //     exit();
}
/**
 * Lấy tất cả danh sách sinh viên
 */
function handleGetAllUsers() {
    return getAllUsers();
}

function handleGetUserById($id) {
    return getUserById($id);
}
function handleToggleUserStatus() {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: ../views/dashboard/user.php?error=Thiếu ID người dùng");
        exit();
    }

    $id = intval($_GET['id']);
    $result = toggleUserStatus($id);

    if ($result) {
        header("Location: ../views/dashboard/user.php?success=Cập nhật trạng thái tài khoản thành công");
    } else {
        header("Location: ../views/dashboard/user.php?error=Không thể cập nhật trạng thái");
    }
    exit();
}


/**
 * Xử lý tạo homestay mới
 */
function handleCreateUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/dashboard/user.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['fullname']) || !isset($_POST['username']) || !isset($_POST['phone']) || !isset($_POST['address']) || !isset($_POST['create_at'])) {
        header("Location: ../views/dashboard/user/create_user.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $create_at = trim($_POST['create_at']);
    
    // Validate dữ liệu
    if (empty($fullname) || empty($username) || empty($phone) || empty($address) || empty($create_at)) {
        header("Location: ../views/dashboard/user/create_user.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = addUser($fullname, $username, $phone, $address, $create_at);
    if ($result) {
        header("Location: ../views/dashboard/user.php?success=Thêm người dùng thành công");
    } else {
        header("Location: ../views/dashboard/user/create_user.php?error=Có lỗi xảy ra khi thêm người dùng");
    }
    exit();
}
 // Xử lý chỉnh sửa homestay
function handleEditUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/dashboard/user.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['fullname']) || !isset($_POST['username']) || !isset($_POST['phone']) || !isset($_POST['address']) || !isset($_POST['create_at'])) {
        header("Location: ../views/dashboard/user/edit_user.php?id=" . (isset($_POST['id']) ? $_POST['id'] : '') . "&error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = $_POST['id'];
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $create_at = trim($_POST['create_at']);

    if (empty($fullname) || empty($username) || empty($phone) || empty($address) || empty($create_at)) {
        header("Location: ../views/dashboard/homestay/edit_homestay.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }


    $result = updateUser($id, $fullname, $username, $phone, $address, $create_at);
    if ($result) {
        header("Location: ../views/dashboard/user.php?success=Cập nhật người dùng thành công");
    } else {
        header("Location: ../views/dashboard/user/edit_user.php?id=" . $id . "&error=Cập nhật người dùng thất bại");
    }
    exit();
}

function handleDeleteUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/dashboard/user.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/dashboard/user.php?error=Không tìm thấy ID người dùng");
        exit();
    }

    $id = $_GET['id'];
    if (!is_numeric($id)) {
        header("Location: ../views/dashboard/user.php?error=ID homestay không hợp lệ");
        exit();
    }

    $result = deleteUser($id);

    if ($result) {
        header("Location: ../views/dashboard/user.php?success=Xóa người dùng thành công");
    } else {
        header("Location: ../views/dashboard/user.php?error=Xóa người dùng thất bại");
    }
    exit();
}
?>
