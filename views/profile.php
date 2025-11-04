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
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white text-center py-3 rounded-top-4">
            <h4 class="mb-0">Hồ sơ cá nhân</h4>
        </div>
        <div class="card-body p-4">

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form method="POST" action="../handles/profile_process.php">
               <div class="mb-3">
                    <label class="form-label fw-semibold">Tên tài khoản</label>
                    <input type="email" class="form-control" 
                           value="<?= htmlspecialchars($user['username']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" 
                           value="<?= htmlspecialchars($user['fullname']) ?>" required>
                </div>


                <div class="mb-3">
                    <label class="form-label fw-semibold">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($user['phone']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" 
                           value="<?= htmlspecialchars($user['address']) ?>">
                </div>


                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Lưu thay đổi</button>
                    <a href="../index.php" class="btn btn-secondary px-4">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
