<?php
session_start();
require_once __DIR__ . '/../../functions/auth_functions.php';
checkLogin('../../views/login.php');
$current_page = 'homestays';
$page_title = 'Quản lý Homestay';

include './menu.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/homestay_admin.css">
    
</head>
<body>
    <main class="main-content" style="margin-left: 260px; padding-left: 20px;">
        <div class="header d-flex justify-content-between align-items-center mb-3">
            <h1><i class="fa-solid fa-house"></i> Quản lý Homestay</h1>
            <a href="./homestay/create_homestay.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                Tìm kiếm & Lọc
            </div>
            <div class="filter-controls">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-search"></i>
                        Tìm kiếm
                    </label>
                    <input type="text" 
                           class="filter-input" 
                           id="searchInput"
                           placeholder="Tìm theo tên, địa điểm...">
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-dollar-sign"></i>
                        Giá tối thiểu
                    </label>
                    <input type="number" 
                           class="filter-input" 
                           id="minPrice"
                           placeholder="0"
                           min="0"
                           step="100000">
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-dollar-sign"></i>
                        Giá tối đa
                    </label>
                    <input type="number" 
                           class="filter-input" 
                           id="maxPrice"
                           placeholder="10,000,000"
                           min="0"
                           step="100000">
                </div>

                <button class="filter-btn btn-filter" onclick="applyFilter()">
                    <i class="fas fa-check"></i>
                    Áp dụng
                </button>

                <button class="filter-btn btn-reset" onclick="resetFilter()">
                    <i class="fas fa-redo"></i>
                    Reset
                </button>
            </div>
        </div>

        <div class="content-card">
            <!-- Results Info -->
            <div class="results-info">
                <div class="results-count">
                    <i class="fas fa-list"></i>
                    Hiển thị <span id="resultCount">0</span> kết quả
                </div>
            </div>

            <div class="table-responsive table-container">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Ảnh</th>
                            <th>Tên Homestay</th>
                            <th>Địa điểm</th>
                            <th>Giá/đêm</th>
                            <th>Phòng</th>
                            <th>Sức chứa</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="homestayTableBody">
                        <?php 
                        require '../../handles/homestay_process.php';
                        $homestays = handleGetAllHomestays();
                        
                        if (empty($homestays)): ?>
                            <tr>
                                <td colspan="9" class="no-results">
                                    <i class="fas fa-home"></i>
                                    <div>Không có homestay nào.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $index = 1; ?>
                            <?php foreach ($homestays as $h): ?>
                           <tr class="homestay-row" 
                                data-name="<?= htmlspecialchars(strtolower($h['homestay_name'] ?? '')) ?>"
                                data-location="<?= htmlspecialchars(strtolower($h['location'] ?? '')) ?>"
                                data-price="<?= $h['price_per_night'] ?? 0 ?>"
                                onclick="window.location.href='./homestay/detail_homestay.php?id=<?= $h['id'] ?>'"
                                style="cursor: pointer;">

                                <td><?= $index++; ?></td>
                                <td style="width:120px;">
                                    <?php $img = $h['image_url'] ?? ''; ?>
                                    <?php if ($img): ?>
                                        <img src="<?= htmlspecialchars($img) ?>" 
                                             alt="" 
                                             style="width:100px;height:60px;object-fit:cover;border-radius:8px;">
                                    <?php else: ?>
                                        <div style="width:100px;height:60px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#999;border-radius:8px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($h['homestay_name'] ?? '') ?></td>
                                <td>
                                    <i class="fas fa-map-marker-alt" style="color:#667eea;"></i>
                                    <?= htmlspecialchars($h['location'] ?? '') ?>
                                </td>
                                <td>
                                    <strong style="color:#28a745;">
                                        <?= isset($h['price_per_night']) ? number_format($h['price_per_night']) . ' đ' : '' ?>
                                    </strong>
                                </td>
                                <td>
                                    <i class="fas fa-door-open"></i>
                                    <?= htmlspecialchars($h['num_room'] ?? '') ?>
                                </td>
                                <td>
                                    <i class="fas fa-users"></i>
                                    <?= htmlspecialchars($h['max_people'] ?? '') ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $h['status'] ?? '';
                                    $badgeClass = ($status === 'Hoạt động') ? 'bg-success' : 'bg-secondary';
                                    ?>
                                    <span style="color:white;" class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="./homestay/edit_homestay.php?id=<?= $h['id'] ?>" 
                                       class="btn btn-warning btn-sm" 
                                       style="color:white;">
                                        <i class="fa-solid fa-wrench"></i>
                                    </a>
                                    <a href="../../handles/homestay_process.php?action=delete&id=<?= $h['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lấy tất cả các hàng homestay
        const allRows = document.querySelectorAll('.homestay-row');
        
        // Hàm áp dụng filter
        function applyFilter() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
            
            let visibleCount = 0;
            
            allRows.forEach((row, index) => {
                const name = row.getAttribute('data-name');
                const location = row.getAttribute('data-location');
                const price = parseFloat(row.getAttribute('data-price'));
                
                // Kiểm tra điều kiện tìm kiếm
                const matchSearch = !searchTerm || 
                                   name.includes(searchTerm) || 
                                   location.includes(searchTerm);
                
                // Kiểm tra điều kiện giá
                const matchPrice = price >= minPrice && price <= maxPrice;
                
                // Hiển thị hoặc ẩn hàng
                if (matchSearch && matchPrice) {
                    row.style.display = '';
                    // Cập nhật STT
                    row.querySelector('td:first-child').textContent = ++visibleCount;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Cập nhật số lượng kết quả
            document.getElementById('resultCount').textContent = visibleCount;
            
            // Hiển thị thông báo nếu không có kết quả
            const tbody = document.getElementById('homestayTableBody');
            const noResultRow = tbody.querySelector('.no-result-message');
            
            if (visibleCount === 0 && allRows.length > 0) {
                if (!noResultRow) {
                    const newRow = document.createElement('tr');
                    newRow.className = 'no-result-message';
                    newRow.innerHTML = `
                        <td colspan="9" class="no-results">
                            <i class="fas fa-search"></i>
                            <div>Không tìm thấy kết quả phù hợp</div>
                            <small style="color: #999; margin-top: 10px; display: block;">
                                Thử thay đổi tiêu chí tìm kiếm
                            </small>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                }
            } else if (noResultRow) {
                noResultRow.remove();
            }
        }
        
        // Hàm reset filter
        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            
            // Hiển thị tất cả các hàng
            allRows.forEach((row, index) => {
                row.style.display = '';
                row.querySelector('td:first-child').textContent = index + 1;
            });
            
            // Xóa thông báo không có kết quả
            const noResultRow = document.getElementById('homestayTableBody').querySelector('.no-result-message');
            if (noResultRow) {
                noResultRow.remove();
            }
            
            // Cập nhật số lượng
            document.getElementById('resultCount').textContent = allRows.length;
        }
        
        // Tìm kiếm khi nhấn Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilter();
            }
        });
        
        // Tìm kiếm khi thay đổi giá
        document.getElementById('minPrice').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilter();
            }
        });
        
        document.getElementById('maxPrice').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilter();
            }
        });
        
        // Khởi tạo số lượng ban đầu
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('resultCount').textContent = allRows.length;
        });
    </script>
</body>
</html> 