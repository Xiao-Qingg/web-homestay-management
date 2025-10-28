<?php
session_start();
// Load authentication functions
require_once __DIR__ . '/../../../functions/auth_functions.php';
checkLogin('../../../views/login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <title>Edit homestay</title>
</head>
<body>
    
    <div class="container">
        <main class="main-content" >
    <div class="header d-flex justify-content-between align-items-center mb-3">
    <?php
        // Kiểm tra có ID không
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header("Location: ../homestay.php?error=Không tìm thấy học phần");
            exit;
        }
        
        $id = $_GET['id'];
        
        // Lấy thông tin học phần
        require_once __DIR__ .'../../../../handles/homestay_process.php';
        $homestay = handleGetHomestayById($id);

        if (!$homestay) {
            header("Location: ../homestay.php?error=Không tìm thấy homestay");
            exit;
        }
        
        // Hiển thị thông báo lỗi
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_GET['error']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
        ?>
        <script>
        // Sau 3 giây sẽ tự động ẩn alert
        setTimeout(() => {
            let alertNode = document.querySelector('.alert');
            if (alertNode) {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                bsAlert.close();
            }
        }, 3000);
        </script>
    </div>
    <?php if ($homestay): ?>
    <div class="content-card">
        <h2>
            Chỉnh sửa: <?= htmlspecialchars($homestay['homestay_name']) ?>
            <a href="../homestay.php" class="btn btn-secondary btn-sm float-end">← Quay lại</a>
        </h2>

        <form method="POST" action="../../../handles/homestay_process.php" id="homestayForm" class="mt-4">
            <div class="row">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= htmlspecialchars($homestay['id']) ?>">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="homestay_name" class="form-label">Tên Homestay <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="homestay_name" name="homestay_name" 
                               value="<?= htmlspecialchars($homestay['homestay_name']) ?>" 
                               placeholder="Nhập tên homestay" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="location" class="form-label">Địa điểm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" 
                               value="<?= htmlspecialchars($homestay['location']) ?>" 
                               placeholder="VD: Sapa, Lào Cai" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="price_per_night" class="form-label">Giá/đêm (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price_per_night" name="price_per_night" 
                               value="<?= htmlspecialchars($homestay['price_per_night']) ?>" 
                               placeholder="850000" min="0" step="1000" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="num_room" class="form-label">Số phòng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="num_room" name="num_room" 
                               value="<?= htmlspecialchars($homestay['num_room']) ?>" 
                               placeholder="3" min="1" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="max_people" class="form-label">Sức chứa (người) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_people" name="max_people" 
                               value="<?= htmlspecialchars($homestay['max_people']) ?>" 
                               placeholder="6" min="1" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="image_url" class="form-label">URL Ảnh</label>
                <input type="url" class="form-control" id="image_url" name="image_url" 
                       value="<?= htmlspecialchars($homestay['image_url']) ?>" 
                       placeholder="https://example.com/image.jpg">
                <small class="text-muted">Nhập đường dẫn URL của ảnh homestay</small>
                
                <?php if (!empty($homestay['image_url'])): ?>
                <div class="mt-2">
                    <img src="<?= htmlspecialchars($homestay['image_url']) ?>" 
                         alt="Preview" 
                         style="max-width: 200px; max-height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd;"
                         onerror="this.style.display='none'">
                </div>
                <?php endif; ?>
            </div>

      

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="Hoạt động" <?= ($homestay['status'] === 'Hoạt động') ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="Không hoạt động" <?= ($homestay['status'] === 'Không hoạt động') ? 'selected' : '' ?>>Không hoạt động</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật Homestay
                </button>
                <a href="../homestay.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
                
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="content-card">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Không tìm thấy homestay!
        </div>
        <a href="../homestay.php" class="btn btn-primary">← Quay về danh sách</a>
    </div>
    <?php endif; ?>
</main>
    </div>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<script>
// Form validation
document.getElementById('homestayForm')?.addEventListener('submit', function(e) {
    const homestayName = document.getElementById('homestay_name').value.trim();
    const location = document.getElementById('location').value.trim();
    const price = parseFloat(document.getElementById('price_per_night').value);
    const rooms = parseInt(document.getElementById('num_room').value);
    const people = parseInt(document.getElementById('max_people').value);
    
    if (!homestayName) {
        e.preventDefault();
        alert('Vui lòng nhập tên homestay!');
        return false;
    }
    
    if (!location) {
        e.preventDefault();
        alert('Vui lòng nhập địa điểm!');
        return false;
    }
    
    if (price <= 0) {
        e.preventDefault();
        alert('Giá phải lớn hơn 0!');
        return false;
    }
    
    if (rooms <= 0) {
        e.preventDefault();
        alert('Số phòng phải lớn hơn 0!');
        return false;
    }
    
    if (people <= 0) {
        e.preventDefault();
        alert('Sức chứa phải lớn hơn 0!');
        return false;
    }
});

// Auto hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

</body>
</html>

