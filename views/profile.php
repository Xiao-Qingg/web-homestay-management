<?php
require_once '../functions/auth_functions.php';
checkLogin('../views/login.php');

$functions_path = __DIR__ . '/../functions/user_functions.php';
if (!file_exists($functions_path)) {
    http_response_code(500);
    echo 'Lỗi: file functions/user_functions.php không tìm thấy.';
    exit;
}
require_once $functions_path;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
}
$id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : (isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0);
if ($id <= 0) {
    header("Location: ../views/login.php");
    exit();
}

// Lấy danh sách booking của user
$user = getUserById($id);
$_SESSION['change_password_username'] = $user['username'];

// Kiểm tra biến $logged từ auth_functions
$logged = isset($_SESSION['id']) || isset($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css//profile.css">
</head>

<body>

<div class="container profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h4>Hồ sơ cá nhân</h4>
        </div>
        
        <div class="profile-body">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($_GET['success']) ?></span>
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($_GET['error']) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="../handles/profile_process.php">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i>
                        Tên tài khoản
                    </label>
                    <input type="text" class="form-control" 
                           value="<?= htmlspecialchars($user['username']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Họ và tên
                    </label>
                    <input type="text" name="fullname" class="form-control" 
                           value="<?= htmlspecialchars($user['fullname']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i>
                        Số điện thoại
                    </label>
                    <input type="text" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($user['phone']) ?>"
                           placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Địa chỉ
                    </label>
                    <input type="text" name="address" class="form-control" 
                           value="<?= htmlspecialchars($user['address']) ?>"
                           placeholder="Nhập địa chỉ">
                </div>

                <div class="btn-group-custom">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        Lưu thay đổi
                    </button>
                    <a href="./change_password.php" class="btn" style="background-color:darkcyan; color:white;">
                        <i class="fas fa-key"></i>
                        Đổi mật khẩu
                    </a>


                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                 
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>