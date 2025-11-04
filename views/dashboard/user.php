<?php
session_start();
// Load authentication functions
require_once __DIR__ . '/../../functions/auth_functions.php';
checkLogin('../../views/login.php');
$current_page = 'users';
$page_title = 'Quản lý người dùng';


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
        <h1>Quản lý người dùng</h1>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin User') ?></span>
            <a href="../../handles/logout_process.php" class="btn btn-outline-secondary btn-sm ms-2"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="content-card">
        

        <div class="table-responsive">
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>STT</th>
                        <th>Họ và tên</th>
                        <th>Tên đăng nhập</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php require '../../handles/user_process.php';
                    $users = handleGetAllUsers();
                     if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Không có người dùng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php $index = 1; ?>
                        <?php foreach ($users as $h): ?>
                        <tr>
                            <td><?= $index++; ?></td>
                            <td><?= htmlspecialchars($h['fullname'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['username'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['address'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['created_at'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['status'] ?? '') ?></td>
                            <td>
                                <a href="../../handles/user_process.php?action=delete&id=<?php echo $h['id']; ?>" 
                                    class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                <?php if ($h['status'] === 'Hoạt động'): ?>
                                    <a href="../../handles/user_process.php?id=<?= $h['id'] ?>&action=lock" class="btn btn-warning btn-sm" style="background-color: #ffc107;color: #fff;"><i class="fa-solid fa-lock"></i></a>
                                <?php else: ?>
                                    <a href="../../handles/user_process.php?id=<?= $h['id'] ?>&action=unlock" class="btn btn-success btn-sm"><i class="fa-solid fa-lock-open"></i></a>
                                <?php endif; ?>
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