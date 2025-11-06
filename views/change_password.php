<?php
session_start();
$username = $_SESSION['change_password_username'] ?? '';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="../assets/css/change_password.css">
</head>
<body>

<div class="container password-container">
    <div class="password-card">
        <div class="password-header">
            <div class="key-icon">
                <i class="fas fa-key"></i>
            </div>
            <h4>Đổi mật khẩu</h4>
            <p>Bảo mật tài khoản của bạn bằng mật khẩu mạnh</p>
        </div>
        
        <div class="password-body">
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

            <div class="password-requirements">
                <h6>
                    <i class="fas fa-shield-alt"></i>
                    Yêu cầu mật khẩu:
                </h6>
                <ul id="password-checklist">
                    <li id="length-check">
                        <i class="fas fa-circle"></i>
                        <span>Ít nhất 8 ký tự</span>
                    </li>
                    <li id="uppercase-check">
                        <i class="fas fa-circle"></i>
                        <span>Có chữ hoa (A-Z)</span>
                    </li>
                    <li id="lowercase-check">
                        <i class="fas fa-circle"></i>
                        <span>Có chữ thường (a-z)</span>
                    </li>
                    <li id="number-check">
                        <i class="fas fa-circle"></i>
                        <span>Có số (0-9)</span>
                    </li>
                </ul>
            </div>

            <form method="POST" action="../handles/changePassword_process.php" id="change-password-form">
                <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i>
                        Mật khẩu hiện tại
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               name="current_password" 
                               id="current-password"
                               class="form-control" 
                               placeholder="Nhập mật khẩu hiện tại"
                               required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('current-password')"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i>
                        Mật khẩu mới
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" 
                               name="new_password" 
                               id="new-password"
                               class="form-control" 
                               placeholder="Nhập mật khẩu mới"
                               required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new-password')"></i>
                    </div>
                    <div class="password-strength" id="password-strength">
                        <div class="password-strength-bar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-check-circle"></i>
                        Xác nhận mật khẩu mới
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-check-circle input-icon"></i>
                        <input type="password" 
                               name="confirm_password" 
                               id="confirm-password"
                               class="form-control" 
                               placeholder="Nhập lại mật khẩu mới"
                               required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm-password')"></i>
                    </div>
                    <div class="password-hint" id="match-hint" style="display: none; color: #f5576c;">
                        <i class="fas fa-exclamation-circle"></i>
                        Mật khẩu không khớp
                    </div>
                </div>

                <div class="btn-group-custom">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Đổi mật khẩu
                    </button>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/change_password.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>