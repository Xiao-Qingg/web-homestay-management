<?php
// session_start();
$current_page = 'settings';
$page_title = 'Cài đặt';

// Load functions nếu cần
require_once __DIR__ . '/../../handles/user_process.php';

$current_user = [
    'id' => $_SESSION['user_id'] ?? 1,
    'username' => $_SESSION['username'] ?? 'admin',
    'fullname' => $_SESSION['fullname'] ?? 'Administrator',
    'email' => $_SESSION['email'] ?? 'admin@homestay.com',
    'phone' => $_SESSION['phone'] ?? '0123456789',
    'role' => $_SESSION['role'] ?? 'admin'
];

include './menu.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../../css/setting.css">
</head>
<body>

<main class="main-content">
    <div class="header">
        <h1><i class="fas fa-cog"></i> Cài đặt</h1>
    </div>

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

                <form action="../../handles/update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="avatar-upload">
                        <div class="avatar-preview" id="avatarPreview">
                            <?= strtoupper(substr($current_user['fullname'], 0, 1)) ?>
                        </div>
                        <div class="avatar-actions">
                            <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-upload"></i> Tải ảnh lên
                            </button>
                            <button type="button" class="btn btn-secondary">
                                <i class="fas fa-trash"></i> Xóa ảnh
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên *</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($current_user['fullname']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tên đăng nhập</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($current_user['username']) ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($current_user['phone']) ?>">
                        </div>
                         <div class="form-group">
                            <label>Vai trò</label>
                            <input type="text" value="<?= $current_user['role']  ?>" disabled>
                        </div>
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

                <form action="../../handles/change_password.php" method="POST">
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

                <div class="settings-card" style="margin-top: 30px;">
                    <h4><i class="fas fa-shield-alt"></i> Xác thực hai yếu tố</h4>
                    <p>Tăng cường bảo mật bằng cách yêu cầu mã xác thực khi đăng nhập</p>
                    <button class="btn btn-secondary" style="margin-top: 10px;">
                        <i class="fas fa-plus"></i> Kích hoạt 2FA
                    </button>
                </div>
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

<script>
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.settings-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Remove active class from nav items
        document.querySelectorAll('.settings-nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Show selected section
        document.getElementById(sectionId).classList.add('active');
        
        // Add active class to clicked nav item
        event.target.closest('.settings-nav-item').classList.add('active');
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').innerHTML = 
                    `<img src="${e.target.result}" alt="Avatar">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function backupData() {
        if (confirm('Bạn có muốn sao lưu dữ liệu hệ thống?')) {
            alert('Đang tiến hành sao lưu dữ liệu...');
            // Thêm code sao lưu ở đây
        }
    }

    function confirmReset() {
        if (confirm('CẢNH BÁO: Hành động này sẽ xóa toàn bộ dữ liệu hệ thống. Bạn có chắc chắn muốn tiếp tục?')) {
            if (confirm('Xác nhận lần cuối: Dữ liệu không thể khôi phục sau khi xóa!')) {
                alert('Chức năng đang được phát triển!');
            }
        }
    }

    function toggleDarkMode(checkbox) {
        if (checkbox.checked) {
            document.body.style.background = '#1a1a1a';
            alert('Chế độ tối đang được phát triển!');
            checkbox.checked = false;
        }
    }

    function changeThemeColor(color) {
        document.querySelectorAll('.theme-color').forEach(el => {
            el.style.border = '3px solid transparent';
        });
        event.target.style.border = '3px solid #333';
        alert('Đổi màu chủ đạo sang: ' + color);
    }

    function changeFontSize(size) {
        document.body.style.fontSize = size;
    }
</script>

</body>
</html>