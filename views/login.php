<?php
session_start();

// Nếu đã đăng nhập rồi thì chuyển về trang chủ
if (isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../functions/db_connection.php';

$error = '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Xử lý khi người dùng bấm "Đăng nhập"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        $conn = getDbConnection();

        $sql = "SELECT id, username, password, role_id, status FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($user = mysqli_fetch_assoc($result)) {
                // Kiểm tra mật khẩu
                if (isset($user['status']) && $user['status'] === 'Bị khóa') {
                    $error = 'Tài khoản của bạn đã bị khóa.';
                }
                // Kiểm tra mật khẩu
                elseif (password_verify($password, $user['password']) || $password === $user['password']) {
                    // Lưu session
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];

                    // Chuyển hướng theo role
                    if ((int)$user['role_id'] === 1) {
                        header('Location: ../views/dashboard/dashboard.php');
                    } else {
                        header('Location: ../index.php');
                    }
                    exit();
                } else {
                    $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
                }
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Homestay Paradise</title>
    
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
                <!-- Left Side -->
                <div class="col-lg-1">
                    
                </div>

                <!-- Right Side -->
                <div class="col-lg-10">
                    <div class="login-right">
                        <div class="login-header">
                            <h2><i class="fas fa-sign-in-alt"></i> Đăng Nhập</h2>
                            <p>Nhập thông tin để tiếp tục</p>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        

                        <form method="POST" action="./login.php" id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-envelope"></i> Tên đăng nhập
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Nhập tên đăng  của bạn" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                <div class="invalid-feedback">
                                    Vui lòng nhập email hợp lệ!
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Mật khẩu
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control input-with-icon" 
                                           id="password" name="password" 
                                           placeholder="Nhập mật khẩu" required>
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback">
                                    Vui lòng nhập mật khẩu!
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a href="forgot-password.php" class="text-decoration-none" style="color: var(--primary-color);">
                                    Quên mật khẩu?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                            </button>
                        </form>

                        <div class="divider">
                            <span>Hoặc đăng nhập với</span>
                        </div>

                        <div class="social-login">
                            <button type="button" class="btn btn-social btn-facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                            <button type="button" class="btn btn-social btn-google">
                                <i class="fab fa-google"></i> Google
                            </button>
                        </div>

                        <div class="register-link">
                            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
                        </div>

                            
                    </div>
                </div>
                <div class="col-lg-1">
                    
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
   
</body>
</html>