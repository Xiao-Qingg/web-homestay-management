<?php
session_start();
$current_page = 'settings';
$page_title = 'Cài đặt';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}

// Load functions
require_once __DIR__ . '/../../functions/user_functions.php';

// Lấy thông tin user thật từ DB
$current_user = getUserById($_SESSION['id']);

if (!$current_user) {
    header("Location: ../../handles/logout_process.php");
    exit();
}

// Xử lý cập nhật profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (!empty($fullname)) {
        if (updateUserProfile($_SESSION['id'], $fullname, $phone, $address)) {
            $_SESSION['success'] = "Cập nhật thông tin thành công!";
            // Cập nhật lại thông tin
            $current_user = getUserById($_SESSION['id']);
        } else {
            $_SESSION['error'] = "Cập nhật thông tin thất bại!";
        }
    } else {
        $_SESSION['error'] = "Họ tên không được để trống!";
    }
    header("Location: setting.php");
    exit();
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin!";
    } elseif (strlen($new_password) < 8) {
        $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 8 ký tự!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Mật khẩu xác nhận không khớp!";
    } elseif ($current_password !== $current_user['password']) {
        $_SESSION['error'] = "Mật khẩu hiện tại không đúng!";
    } else {
        if (changeUserPassword($_SESSION['id'], $new_password)) {
            $_SESSION['success'] = "Đổi mật khẩu thành công!";
        } else {
            $_SESSION['error'] = "Đổi mật khẩu thất bại!";
        }
    }
    header("Location: setting.php");
    exit();
}

include './menu.php';
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../../assets/css/setting.css">
</head>
<body>

<main class="main-content">
    <div class="header">
        <h1><i class="fas fa-cog"></i> Cài đặt</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="settings-container">
        <!-- Settings Navigation -->
        <div class="settings-nav">
            <div class="settings-nav-item active" onclick="showSection('profile')">
                <i class="fas fa-user"></i>
                <span>Thông tin cá nhân</span>
            </div>
            <div class="settings-nav-item" onclick="showSection('security')">
                <i class="fas fa-lock"></i>
                <span>Bảo mật</span>
            </div>
            <div class="settings-nav-item" onclick="showSection('notifications')">
                <i class="fas fa-bell"></i>
                <span>Thông báo</span>
            </div>
            <div class="settings-nav-item" onclick="showSection('system')">
                <i class="fas fa-server"></i>
                <span>Hệ thống</span>
            </div>
            <div class="settings-nav-item" onclick="showSection('appearance')">
                <i class="fas fa-palette"></i>
                <span>Giao diện</span>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="settings-content">
            <!-- Profile Section -->
            <div id="profile" class="settings-section active">
                <h2 class="section-title">Thông tin cá nhân</h2>
                <p class="section-description">Cập nhật thông tin tài khoản của bạn</p>

                <form method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên *</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($current_user['fullname']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tên đăng nhập</label>
                            <input type="text" value="<?= htmlspecialchars($current_user['username']) ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($current_user['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Vai trò</label>
                            <input type="text" value="<?= $current_user['role_id'] == 1 ? 'Admin' : 'Người dùng' ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($current_user['address'] ?? '') ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Khôi phục
                        </button>
                        <a href="../../handles/logout_process.php" style="background-color:grey; color:white;" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                        </a>
                    </div>
                </form>
            </div>

            <!-- Security Section -->
            <div id="security" class="settings-section">
                <h2 class="section-title">Bảo mật</h2>
                <p class="section-description">Quản lý mật khẩu và cài đặt bảo mật</p>

                <div class="settings-card">
                    <h4><i class="fas fa-key"></i> Đổi mật khẩu</h4>
                    <p>Cập nhật mật khẩu thường xuyên để bảo vệ tài khoản của bạn</p>
                </div>

                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label>Mật khẩu hiện tại *</label>
                        <input type="password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu mới *</label>
                        <input type="password" name="new_password" id="newPassword" required>
                        <small style="color: #888; font-size: 12px;">Ít nhất 8 ký tự, bao gồm chữ và số</small>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới *</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock"></i> Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Section -->
            <div id="notifications" class="settings-section">
                <h2 class="section-title">Thông báo</h2>
                <p class="section-description">Quản lý cách bạn nhận thông báo</p>

                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Thông báo Email</h4>
                        <p>Nhận thông báo qua email về hoạt động quan trọng</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Booking mới</h4>
                        <p>Thông báo khi có đặt phòng mới</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Hủy booking</h4>
                        <p>Thông báo khi khách hàng hủy đặt phòng</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Báo cáo tuần</h4>
                        <p>Nhận báo cáo tổng hợp hàng tuần</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>

            <!-- System Section -->
            <div id="system" class="settings-section">
                <h2 class="section-title">Cài đặt hệ thống</h2>
                <p class="section-description">Cấu hình hệ thống và tùy chọn nâng cao</p>

                <div class="settings-card">
                    <h4><i class="fas fa-database"></i> Sao lưu dữ liệu</h4>
                    <p>Sao lưu toàn bộ dữ liệu hệ thống định kỳ</p>
                    <button class="btn btn-primary" style="margin-top: 10px;" onclick="backupData()">
                        <i class="fas fa-download"></i> Sao lưu ngay
                    </button>
                </div>

                <div class="form-group">
                    <label>Múi giờ</label>
                    <select>
                        <option>GMT+7 (Hà Nội, Bangkok)</option>
                        <option>GMT+8 (Singapore, Manila)</option>
                        <option>GMT+9 (Tokyo, Seoul)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Định dạng ngày tháng</label>
                    <select>
                        <option>DD/MM/YYYY</option>
                        <option>MM/DD/YYYY</option>
                        <option>YYYY-MM-DD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Đơn vị tiền tệ</label>
                    <select>
                        <option>VNĐ (Việt Nam Đồng)</option>
                        <option>USD (US Dollar)</option>
                        <option>EUR (Euro)</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>

                <div class="danger-zone">
                    <h4><i class="fas fa-exclamation-triangle"></i> Vùng nguy hiểm</h4>
                    <p>Các hành động sau đây không thể hoàn tác. Vui lòng cẩn thận!</p>
                    <button class="btn btn-danger" onclick="confirmReset()">
                        <i class="fas fa-trash-restore"></i> Reset hệ thống
                    </button>
                </div>
            </div>

            <!-- Appearance Section -->
            <div id="appearance" class="settings-section">
                <h2 class="section-title">Giao diện</h2>
                <p class="section-description">Tùy chỉnh giao diện hệ thống</p>

                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Chế độ tối</h4>
                        <p>Sử dụng giao diện tối để giảm căng thẳng mắt</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleDarkMode(this)">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="form-group">
                    <label>Màu chủ đạo</label>
                    <div style="display: flex; gap: 15px; margin-top: 10px;">
                        <div onclick="changeThemeColor('#667eea')" style="width: 50px; height: 50px; background: #667eea; border-radius: 8px; cursor: pointer; border: 3px solid transparent;" class="theme-color active"></div>
                        <div onclick="changeThemeColor('#43e97b')" style="width: 50px; height: 50px; background: #43e97b; border-radius: 8px; cursor: pointer;"></div>
                        <div onclick="changeThemeColor('#fa709a')" style="width: 50px; height: 50px; background: #fa709a; border-radius: 8px; cursor: pointer;"></div>
                        <div onclick="changeThemeColor('#4facfe')" style="width: 50px; height: 50px; background: #4facfe; border-radius: 8px; cursor: pointer;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Kích thước chữ</label>
                    <select onchange="changeFontSize(this.value)">
                        <option value="14px">Nhỏ</option>
                        <option value="16px" selected>Trung bình</option>
                        <option value="18px">Lớn</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/setting.js"></script>

</body>
