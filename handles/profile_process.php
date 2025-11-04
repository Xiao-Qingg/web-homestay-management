<?php
require_once __DIR__ . '/../functions/user_functions.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../views/login.php?error=Vui lòng đăng nhập");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['id'];
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $result = updateUserProfile($id, $fullname, $phone, $address);

    if ($result) {
       header("Location: ../views/profile.php?success=Sửa thông tin thành công");

    } else {
        header("Location: ../views/profile.php?error=Cập nhật thất bại");
    }
    exit();
}
?>

