<?php
function checkLogin($redirectPath = '../views/login.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
        $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này!';
        header('Location: ' . $redirectPath);
        exit();
    }
}

function logout($redirectPath = '../views/login.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    session_start();
    header('Location: ' . $redirectPath);
    exit();
}

function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
        return [
            'id' => $_SESSION['id'],
            'username' => $_SESSION['username'],
            'role_id' => $_SESSION['role_id'] ?? null
        ];
    }
    return null;
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['id']) && isset($_SESSION['username']);
}

function authenticateUser($conn, $username, $password) {
    $sql = "SELECT id, username, password, role_id, status FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return false;
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (isset($user['status']) && $user['status'] === 'Bị khóa') {
            return ['error' => 'locked'];
        }
        if ($password === $user['password']) {
            mysqli_stmt_close($stmt);
            return $user;
        }
    }

    if ($stmt) mysqli_stmt_close($stmt);
    return false;
}


