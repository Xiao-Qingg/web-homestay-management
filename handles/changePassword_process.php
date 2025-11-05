<?php
require_once '../functions/db_connection.php';
session_start();

// Lấy username từ session
$username = $_SESSION['change_password_username'] ?? '';
$current_password = trim($_POST['current_password'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

// Kiểm tra dữ liệu
if ($username === '' || $current_password === '' || $new_password === '' || $confirm_password === '') {
    header("Location: ../views/change_password.php?error=Vui lòng nhập đầy đủ thông tin.");
    exit();
}

// Kiểm tra mật khẩu xác nhận
if ($new_password !== $confirm_password) {
    header("Location: ../views/change_password.php?error=Mật khẩu xác nhận không khớp.");
    exit();
}

$conn = getDbConnection();

// Lấy mật khẩu hiện tại trong DB
$sql = "SELECT password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../views/change_password.php?error=Tài khoản không tồn tại.");
    exit();
}

$row = $result->fetch_assoc();
$db_password = $row['password'];

// So sánh mật khẩu hiện tại (chưa mã hóa, nếu bạn chưa dùng password_hash)
if ($current_password !== $db_password) {
    header("Location: ../views/change_password.php?error=Mật khẩu hiện tại không đúng.");
    exit();
}

// Cập nhật mật khẩu mới (chưa mã hóa theo yêu cầu)
$update_sql = "UPDATE users SET password = ? WHERE username = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ss", $new_password, $username);

if ($update_stmt->execute()) {
    unset($_SESSION['change_password_username']); // xóa session sau khi đổi thành công
    header("Location: ../views/profile.php?success=Đổi mật khẩu thành công.");
    exit();
} else {
    header("Location: ../views/change_password.php?error=Lỗi khi cập nhật mật khẩu.");
    exit();
}

$conn->close();
?>
