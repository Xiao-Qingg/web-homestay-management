<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['id']) && !isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header("Location: ../views/login.php?error=" . urlencode("Vui lòng đăng nhập để sử dụng tính năng yêu thích"));
    exit();
}

// Lấy ID homestay từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../index.php?error=" . urlencode("ID homestay không hợp lệ"));
    exit();
}

$homestay_id = (int)$_GET['id'];

// Khởi tạo mảng favorites nếu chưa có
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Kiểm tra xem homestay đã có trong danh sách yêu thích chưa
if (in_array($homestay_id, $_SESSION['favorites'])) {
    // Nếu đã có thì xóa khỏi danh sách (toggle)
    $key = array_search($homestay_id, $_SESSION['favorites']);
    unset($_SESSION['favorites'][$key]);
    $_SESSION['favorites'] = array_values($_SESSION['favorites']); // Reindex array
    $_SESSION['favorite_message'] = "Đã xóa khỏi danh sách yêu thích";
    $_SESSION['favorite_type'] = "warning";
} else {
    // Nếu chưa có thì thêm vào
    $_SESSION['favorites'][] = $homestay_id;
    $_SESSION['favorite_message'] = "Đã thêm vào danh sách yêu thích";
    $_SESSION['favorite_type'] = "success";
}

// Lưu số lượng vào session để hiển thị
$_SESSION['favorites_count'] = count($_SESSION['favorites']);

// Quay lại trang trước
$redirect_url = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: " . $redirect_url);
exit();