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
   <link rel="stylesheet" href="../css//change_password.css">
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

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Kiểm tra độ mạnh mật khẩu
const newPasswordInput = document.getElementById('new-password');
const strengthBar = document.getElementById('password-strength');

newPasswordInput.addEventListener('input', function() {
    const password = this.value;
    
    if (password.length > 0) {
        strengthBar.style.display = 'block';
        
        // Kiểm tra các tiêu chí
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        
        // Cập nhật checklist
        updateCheck('length-check', hasLength);
        updateCheck('uppercase-check', hasUppercase);
        updateCheck('lowercase-check', hasLowercase);
        updateCheck('number-check', hasNumber);
        
        // Tính điểm mạnh
        const score = [hasLength, hasUppercase, hasLowercase, hasNumber].filter(Boolean).length;
        
        strengthBar.classList.remove('weak', 'medium', 'strong');
        
        if (score <= 2) {
            strengthBar.classList.add('weak');
        } else if (score === 3) {
            strengthBar.classList.add('medium');
        } else {
            strengthBar.classList.add('strong');
        }
    } else {
        strengthBar.style.display = 'none';
    }
});

function updateCheck(id, isValid) {
    const element = document.getElementById(id);
    if (isValid) {
        element.classList.add('valid');
        element.querySelector('i').classList.remove('fa-circle');
        element.querySelector('i').classList.add('fa-check-circle');
    } else {
        element.classList.remove('valid');
        element.querySelector('i').classList.remove('fa-check-circle');
        element.querySelector('i').classList.add('fa-circle');
    }
}

// Kiểm tra mật khẩu khớp
const confirmPasswordInput = document.getElementById('confirm-password');
const matchHint = document.getElementById('match-hint');

confirmPasswordInput.addEventListener('input', function() {
    if (this.value.length > 0) {
        if (this.value !== newPasswordInput.value) {
            matchHint.style.display = 'block';
            matchHint.style.color = '#f5576c';
            matchHint.innerHTML = '<i class="fas fa-exclamation-circle"></i> Mật khẩu không khớp';
        } else {
            matchHint.style.display = 'block';
            matchHint.style.color = '#38ef7d';
            matchHint.innerHTML = '<i class="fas fa-check-circle"></i> Mật khẩu khớp';
        }
    } else {
        matchHint.style.display = 'none';
    }
});

// Validate form trước khi submit
document.getElementById('change-password-form').addEventListener('submit', function(e) {
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
        return false;
    }
    
    // Kiểm tra độ mạnh tối thiểu
    const hasLength = newPassword.length >= 8;
    const hasUppercase = /[A-Z]/.test(newPassword);
    const hasLowercase = /[a-z]/.test(newPassword);
    const hasNumber = /[0-9]/.test(newPassword);
    
    if (!hasLength || !hasUppercase || !hasLowercase || !hasNumber) {
        e.preventDefault();
        alert('Mật khẩu mới chưa đủ mạnh! Vui lòng đáp ứng tất cả yêu cầu.');
        return false;
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>