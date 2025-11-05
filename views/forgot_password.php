<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
          --primary-gradient:linear-gradient(135deg, #4b78bc 0%, #295370 100%);
            --success-gradient: linear-gradient(135deg, #2554abff 0%, #2b5fcfff 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #4b78bc 0%, #295370 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -250px;
            left: -250px;
            animation: float 8s ease-in-out infinite;
        }
        
        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -200px;
            right: -200px;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }
        
        .forgot-container {
            max-width: 480px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .forgot-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .forgot-header {
            background: var(--primary-gradient);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .lock-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            animation: shake 2s ease-in-out infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-5deg); }
            20%, 40% { transform: rotate(5deg); }
        }
        
        .lock-icon i {
            font-size: 45px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .forgot-header h4 {
            color: white;
            font-weight: 700;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .forgot-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 14px;
        }
        
        .forgot-body {
            padding: 40px 30px;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
        }
        
        .alert i {
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .form-label i {
            color: #667eea;
        }
        
        .input-group-custom {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #cbd5e0;
            font-size: 18px;
            z-index: 2;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px 14px 50px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            outline: none;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            background: var(--primary-gradient);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .divider {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 25px 0;
            color: #94a3b8;
            font-size: 14px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .back-to-login a:hover {
            color: #764ba2;
            gap: 12px;
        }
        
        .info-box {
            background: linear-gradient(135deg, #f0f4ff 0%, #e6ecff 100%);
            border-left: 4px solid #667eea;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .info-box p {
            margin: 0;
            color: #4a5568;
            font-size: 14px;
            line-height: 1.6;
        }
        
        @media (max-width: 576px) {
            .forgot-header {
                padding: 30px 20px;
            }
            
            .forgot-body {
                padding: 30px 20px;
            }
            
            .lock-icon {
                width: 80px;
                height: 80px;
            }
            
            .lock-icon i {
                font-size: 35px;
            }
        }
    </style>
</head>
<body>

<div class="forgot-container">
    <div class="forgot-card">
        <div class="forgot-header">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h4>Quên mật khẩu?</h4>
            <p>Đừng lo lắng, chúng tôi sẽ giúp bạn lấy lại mật khẩu</p>
        </div>
        
        <div class="forgot-body">
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

            

            <form method="POST" action="../handles/forgotPassword_process.php">
                <div class="form-group">
                    <label class="form-label">
                        
                        Tên tài khoản
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="text" 
                               name="username" 
                               class="form-control" 
                               placeholder="Nhập tên tài khoản của bạn"
                               required
                               autocomplete="username">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Gửi link đặt lại mật khẩu
                </button>
            </form>

            <div class="divider">
                <span>hoặc</span>
            </div>

            <div class="back-to-login">
                <a href="./login.php">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>