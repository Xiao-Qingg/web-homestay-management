<?php
session_start();

// Nếu đã đăng nhập rồi thì chuyển về trang chủ
if (isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

$error = '';
$success = '';

require_once '../functions/db_connection.php';
require_once '../functions/user_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (addUser($fullname, $username, $password, $phone, $address)) {
        echo "<script>alert('Đăng ký thành công!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi đăng ký!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Homestay Paradise</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="back-home">
        <a href="../index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Về Trang Chủ
        </a>
    </div>

    <div class="login-container animate__animated animate__fadeIn">
        <div class="login-card">
            <div class="row g-0">
                <div class="col-lg-1"></div>

                <div class="col-lg-10">
                    <div class="login-right">
                        <div class="login-header">
                            <h2><i class="fas fa-user-plus"></i> Đăng Ký</h2>
                            <p>Tạo tài khoản mới để trải nghiệm Homestay Paradise</p>
                        </div>

                        

                        

                        <form method="POST" action="./register.php">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">
                                    <i class="fas fa-user"></i> Họ và tên
                                </label>
                                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Nhập họ và tên"
                                       value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-envelope"></i> Tên đăng nhập
                                </label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập"
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>
                            <!-- nhập số điện thoại, địa chỉ -->
                             <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-envelope"></i> Số điện thoại
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-envelope"></i> Địa chỉ
                                </label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ"
                                    value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                                </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Mật khẩu
                                </label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Xác nhận mật khẩu
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                            </div>

                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-user-plus"></i> Đăng Ký
                            </button>
                        </form>

                        <div class="divider">
                            <span>Hoặc</span>
                        </div>

                        <div class="register-link">
                            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-1"></div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
