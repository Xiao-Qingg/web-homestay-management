<?php
require_once '../functions/db_connection.php'; // file kết nối CSDL (sửa lại nếu khác)
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    // Kiểm tra rỗng
    if (empty($username)) {
        header("Location: ../views/forgot_password.php?error=Vui lòng nhập tên tài khoản");
        exit();
    }

    // Kết nối DB
    $conn = getDbConnection();

    // Kiểm tra username có tồn tại hay không
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Nếu tồn tại, lưu username vào session để qua bước reset
        $_SESSION['reset_username'] = $username;

        // // Chuyển sang trang đổi mật khẩu và gửi username qua URL
        // header("Location: ../views/change_password.php?username=" . urlencode($username));
        exit();
    } else {
        header("Location: ../views/forgot_password.php?error=Tài khoản không tồn tại");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("Location: ../views/forgot_password.php?error=Phương thức không hợp lệ");
    exit();
}
