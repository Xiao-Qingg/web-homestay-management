<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/auth_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    handleLogin();
}

function handleLogin() {
    $conn = getDbConnection();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ username và password!';
        header('Location: ../views/login.php');
        exit();
    }

    $user = authenticateUser($conn, $username, $password);
    if ($user === false) {
        session_start();
        $_SESSION['error'] = "Sai tên đăng nhập hoặc mật khẩu!";
        header("Location: ../views/login.php");
        exit();
    } elseif (isset($user['error']) && $user['error'] === 'Bị khóa') {
        session_start();
        $_SESSION['error'] = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên!";
        header("Location: ../views/login.php");
        exit();
    }
    else  {
        
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['success'] = 'Đăng nhập thành công!';
        mysqli_close($conn);

        if ($user['role_id'] == 1) {
            header('Location: ../views/dashboard/dashboard.php'); // admin
        } else {
            header('Location: ../index.php'); // user
        }
        exit();
    }

    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    mysqli_close($conn);
    header('Location: ../views/login.php');
    exit();
}
?>
