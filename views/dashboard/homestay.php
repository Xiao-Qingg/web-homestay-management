<?php
session_start();
// Load authentication functions
require_once __DIR__ . '/../../functions/auth_functions.php';
checkLogin('../../views/login.php');
$current_page = 'homestays';
$page_title = 'Quản lý Homestay';


include './menu.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main class="main-content" style="margin-left: 260px; padding-left: 20px;">
    <div class="header d-flex justify-content-between align-items-center mb-3">
        <h1>Quản lý Homestay</h1>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin User') ?></span>
            <a href="../../handles/logout_process.php" class="btn btn-outline-secondary btn-sm ms-2"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="content-card">
        <h2>
            <a href="./homestay/create_homestay.php" class="btn btn-success btn-sm float-end">+ Thêm mới</a>
        </h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
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
                <tbody>
                    <?php require '../../handles/homestay_process.php';
                    $homestays = handleGetAllHomestays();
                     if (empty($homestays)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Không có homestay nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php $index = 1; ?>
                        <?php foreach ($homestays as $h): ?>
                        <tr>
                             <td><?= $index++; ?></td>
                            <td style="width:120px;">
                                <?php $img = $h['image_url'] ?? ''; ?>
                                <?php if ($img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:100px;height:60px;object-fit:cover;border-radius:4px;">
                                <?php else: ?>
                                    <div style="width:100px;height:60px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#999;">No image</div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($h['homestay_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['location'] ?? '') ?></td>
                            <td><?= isset($h['price_per_night']) ? number_format($h['price_per_night']) . ' đ' : '' ?></td>
                            <td><?= htmlspecialchars($h['num_room'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['max_people'] ?? '') ?></td>
                            <td>
                                <?php
                                $status = $h['status'] ?? '';
                                $badgeClass = ($status === 'Hoạt động') ? 'bg-success' : 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>
                            <td>
                                <a href="./homestay/edit_homestay.php?id=<?= $h['id'] ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-wrench"></i></a>
                                <a href="../../handles/homestay_process.php?action=delete&id=<?php echo $h['id']; ?>" 
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
</body>
</html>